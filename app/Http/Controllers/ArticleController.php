<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('user', 'category');

        if ($request->search) {
            $query->where('title', 'Like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sortOrder = $request->input('sort') == 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortOrder);

        $articles = $query->paginate(10);
        $categories = Category::where('status', 1)->get();

        return view('admin.articles.index', compact('articles', 'categories', 'sortOrder'));
    }

    public function showCategory($id)
    {
        $articles = Article::with('user')->where('category_id', $id)->latest()->paginate(5);
        $categories = Category::where('status', 1)->get();

        return view('dashboard_view', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.articles.create', compact('categories'));
    }

    public function dashboard()
    {
        $articles = Article::with('user', 'category')->latest()->paginate(5);
        $categories = Category::where('status', 1)->get();
        $totalArticles = Article::count();
        $totalCategories = Category::count();

        return view('dashboard_view', compact(
            'articles',
            'categories',
            'totalArticles',
            'totalCategories'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|max:2048',
            'media_link' => 'nullable|string' 
        ]);

        $imagePath = $request->hasFile('image') 
            ? $this->storeImage($request->file('image')) 
            : $request->media_link;

        Article::create([
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'image' => $imagePath,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('articles.index')->with('success', 'Article created');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);

        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || Auth::id() === $article->user_id) {
            $categories = Category::where('status', 1)->get();
            return view('admin.articles.edit', compact('article', 'categories'));
        }

        return redirect()->route('articles.index')->with('error', 'Unauthorized access');
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || Auth::id() === $article->user_id) {
            $request->validate([
                'title' => 'required',
                'body' => 'required',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'media_link' => 'nullable|string'
            ]);

            $data = $request->only(['title', 'body', 'category_id', 'status']);

            if ($request->hasFile('image')) {
                $data['image'] = $this->storeImage($request->file('image'), $article->image);
            } 
            elseif ($request->media_link) {
                $data['image'] = $request->media_link;
            }

            $article->update($data);

            return redirect()->route('articles.index')->with('success', 'Article updated');
        }

        return redirect()->route('articles.index')->with('error', 'Unauthorized');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || Auth::id() === $article->user_id) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $article->delete();
            
            return redirect()->route('articles.index')->with('success', 'Article deleted successfully');
        }

        return redirect()->route('articles.index')->with('error', 'Unauthorized action');
    }

    public function show($id)
    {
        $article = Article::with('user', 'category')->findOrFail($id);
        $categories = Category::where('status', 1)->get();

        return view('admin.articles.show', compact('article', 'categories'));
    }

    public function export(Request $request)
    {
        $query = Article::with('user', 'category');

        if ($request->search) {
             $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $articles = $query->get();

        $filename = 'articles_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $callback = function () use ($articles) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Body', 'Category', 'Author', 'Status', 'Created At']);

            foreach ($articles as $article) {
                fputcsv($file, [
                    $article->id,
                    $article->title,
                    strip_tags($article->body),
                    $article->category->title ?? 'N/A',
                    $article->user->name ?? 'N/A',
                    $article->status == 1 ? 'Published' : 'Unpublished',
                    $article->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    private function storeImage($file, $oldImage = null)
    {
        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        
        $path = 'uploads/' . $filename;

        $manager = new ImageManager(new Driver());
        $img = $manager->read($file);
        $img->cover(400, 400);

        Storage::disk('public')->put($path, (string) $img->encode());

        return $path;
    }

    public function toggleStatus($id)
    {
        $article = Article::findOrFail($id);
        
        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin' || Auth::id() === $article->user_id) {
            $article->status = $article->status == 1 ? 0 : 1;
            $article->save();
            return back()->with('success', 'Article status updated!');
        }

        return back()->with('error', 'Unauthorized action.');
    }
}