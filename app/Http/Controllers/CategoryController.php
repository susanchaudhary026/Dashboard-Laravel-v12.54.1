<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(5);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:255|unique:categories,title|regex:/^[^<>]*$/|trim'
        ]);

        Category::create([
            'title' => trim($request->title)
        ]);

        return redirect()->route('categories.index');
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        $articles = Article::with('user', 'category')
            ->where('category_id', $id)
            ->latest()
            ->get();

        return redirect()->route('articles.index', ['category_id' => $id]);
    }

    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255|unique:categories,title,' . $id
        ]);

        $category->update([
            'title' => $request->title
        ]);

        return redirect()->route('categories.index');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect('categories')->with('success', 'Category deleted successfully');
    }
        public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();

        return back()->with('success', 'Status updated successfully!');
    }
}
