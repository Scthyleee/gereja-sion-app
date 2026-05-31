<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersidanganController extends Controller
{
    public function index()
    {
        $dokumen = DB::table('bahan_persidangan')->orderByDesc('created_at')->paginate(20);
        return view('persidangan.index', compact('dokumen'));
    }

    public function store(Request $request)
    {
        $request->validate(['judul' => 'required|string|max:200']);

        $data = $request->only('judul', 'kategori', 'tanggal_sidang', 'deskripsi');
        $data['uploaded_by'] = auth()->id();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('persidangan', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
        }

        DB::table('bahan_persidangan')->insert($data);
        return back()->with('success', 'Dokumen persidangan berhasil ditambahkan.');
    }

    public function destroy(int $id)
    {
        $doc = DB::table('bahan_persidangan')->where('id_dokumen', $id)->first();
        if ($doc && $doc->file_path) Storage::disk('public')->delete($doc->file_path);
        DB::table('bahan_persidangan')->where('id_dokumen', $id)->delete();
        return back()->with('success', 'Dokumen dihapus.');
    }
}
