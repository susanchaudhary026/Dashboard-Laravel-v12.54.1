<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Helpers\FileHelper;

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
        $validated = $request->validate([
            'title' => 'required|min:5|max:255|regex:/^[^<>]*$/',
            'body' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'media_link' => 'nullable|string'
        ]);

        $validated['body'] = strip_tags($validated['body'], [
            '<p>', '<br>', '<b>', '<i>', '<ul>', '<ol>', '<li>', '<img>', '<blockquote>', '<span>'
        ]);

        $title = trim($request->title);

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = FileHelper::storeImage($request->file('image'));
            } catch (\Exception $e) {
                return back()->withErrors(['image' => $e->getMessage()]);
            }
        } elseif ($request->media_link) {
            $imagePath = $request->media_link;
        }

        Article::create([
            'title' => $title,
            'body' => $validated['body'],
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
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
            $validated = $request->validate([
                'title' => 'required|min:5|max:255|regex:/^[^<>]*$/',
                'body' => 'required',
                'category_id' => 'required|integer|exists:categories,id',
                'status' => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'media_link' => 'nullable|string'
            ]);

            $validated['body'] = strip_tags($validated['body'], [
            '<p>', '<br>', '<b>', '<i>', '<ul>', '<ol>', '<li>', '<img>', '<blockquote>', '<span>'
            ]);
            
            $title = trim($request->title);

            $data = $request->only(['title', 'body', 'category_id', 'status']);
            $data['title'] = $title;

            if ($request->hasFile('image')) {
                try {
                    $data['image'] = FileHelper::storeImage($request->file('image'), $article->image);
                } catch (\Exception $e) {
                    return back()->withErrors(['image' => $e->getMessage()]);
                }
            } elseif ($request->media_link) {
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
            FileHelper::deleteImage($article->image);
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