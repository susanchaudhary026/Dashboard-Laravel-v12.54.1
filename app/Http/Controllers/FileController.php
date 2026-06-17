<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $currentPath = $request->input('path', 'uploads');

        $directories = Storage::disk('public')->directories($currentPath);
        $folders = array_map(fn($path) => [
            'name' => basename($path),
            'path' => $path,
            'type' => 'folder'
        ], $directories);

        $filePaths = Storage::disk('public')->files($currentPath);
        
        if ($currentPath == 'uploads') {
            $articleFiles = Storage::disk('public')->files('uploads/articles');
            $filePaths = array_merge($filePaths, $articleFiles);
        }

        $files = array_map(function($path) {
            return [
                'name'   => basename($path),
                'path'   => $path,
                'url'    => Storage::url($path),
                'type'   => 'file',
                'folder' => str_contains($path, 'uploads/articles') ? 'Articles' : 'General'
            ];
        }, $filePaths);

        $allItems = array_merge($folders, $files);
        $breadcrumbs = explode('/', $currentPath);
        $allFolders = Storage::disk('public')->allDirectories('uploads');

        return view('admin.files.index', compact('allItems', 'currentPath', 'breadcrumbs', 'allFolders'));
    }
    

    public function createFolder(Request $request)
    {
        $path = $request->current_path . '/' . str_replace(' ', '_', $request->folder_name);
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
            return back()->with('success', 'Folder created!');
        }
        return back()->with('error', 'Folder already exists');
    }

    public function moveFile(Request $request)
    {
        $oldPath = $request->file_path; 
        $destination = $request->destination; 
        $fileName = basename($oldPath);
        $newPath = $destination . '/' . $fileName;

        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);

            Article::where('image', $oldPath)->update(['image' => $newPath]);

            return back()->with('success', 'File moved and Article links updated!');
        }

        return back()->with('error', 'File not found.');
    }

    public function copyFile(Request $request)
    {
        $oldPath = $request->file_path;
        $newPath = $request->destination . '/copy_' . basename($oldPath);

        Storage::disk('public')->copy($oldPath, $newPath);
        return back()->with('success', 'File copied successfully!');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->get('path', 'uploads');
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs($path, $fileName, 'public');
            return response()->json(['success' => $fileName]);
        }
        return response()->json(['error' => 'Upload Failed'], 400);
    }

    public function destroy(Request $request)
    {
        Storage::disk('public')->delete($request->path);
        return back()->with('success', 'Deleted successfully');
    }

    public function getMediaJson()
    {
        
    if(!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized', 'data' => null], 401);
    };
        $files = Storage::disk('public')->allFiles('uploads'); 
        
        $data = [];
        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $data[] = [
                    'name' => basename($file),
                    'url'  => Storage::url($file),
                    'path' => $file
                ];
            }
        }

        return response()->json($data);
    }
}