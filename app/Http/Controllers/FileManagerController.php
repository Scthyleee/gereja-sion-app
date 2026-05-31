<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function index(Request $request)
    {
        $path = $request->get('path', '');
        $disk = Storage::disk('public');
        $fullPath = $path ?: '';

        $directories = collect($disk->directories($fullPath))->map(fn($d) => [
            'name' => basename($d),
            'path' => $d,
            'type' => 'folder',
            'size' => 0,
            'modified' => null,
        ]);

        $files = collect($disk->files($fullPath))->map(fn($f) => [
            'name' => basename($f),
            'path' => $f,
            'type' => 'file',
            'ext' => pathinfo($f, PATHINFO_EXTENSION),
            'size' => $disk->size($f),
            'modified' => date('Y-m-d H:i', $disk->lastModified($f)),
            'url' => asset('storage/' . $f),
        ]);

        $items = $directories->concat($files);

        $breadcrumbs = [];
        if ($path) {
            $parts = explode('/', $path);
            $accumulated = '';
            foreach ($parts as $p) {
                $accumulated .= ($accumulated ? '/' : '') . $p;
                $breadcrumbs[] = ['name' => $p, 'path' => $accumulated];
            }
        }

        return view('files.index', compact('items', 'path', 'breadcrumbs'));
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|max:20480']);
        $path = $request->get('path', '');
        $file = $request->file('file');
        $file->storeAs($path, $file->getClientOriginalName(), 'public');
        return back()->with('success', 'File berhasil diupload.');
    }

    public function createFolder(Request $request)
    {
        $request->validate(['folder_name' => 'required|string|max:100']);
        $path = $request->get('path', '');
        $newPath = $path ? $path . '/' . $request->folder_name : $request->folder_name;
        Storage::disk('public')->makeDirectory($newPath);
        return back()->with('success', 'Folder berhasil dibuat.');
    }

    public function destroy(Request $request)
    {
        $disk = Storage::disk('public');
        $target = $request->target;

        if ($disk->exists($target)) {
            $disk->delete($target);
        } elseif ($disk->directoryExists($target)) {
            $disk->deleteDirectory($target);
        }

        return back()->with('success', 'Berhasil dihapus.');
    }
}
