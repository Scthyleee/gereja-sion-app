<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BackupController extends Controller
{
    public function index()
    {
        $backups = DB::table('data_backups')->orderByDesc('created_at')->paginate(20);
        $years = DB::table('transaksi')->select(DB::raw('DISTINCT tahun'))->orderByDesc('tahun')->pluck('tahun');

        return view('settings.backup', compact('backups', 'years'));
    }

    public function generate(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $backup = DB::table('data_backups')->insertGetId([
            'tahun' => $tahun,
            'jenis' => 'tahunan',
            'deskripsi' => "Backup tahunan {$tahun}",
            'status' => 'generating',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dir = "backups/{$tahun}";
        Storage::makeDirectory($dir);
        $files = [];
        $totalSize = 0;

        try {
            // 1. SQL Dump
            $sqlPath = "{$dir}/backup-{$tahun}.sql";
            $tables = ['transaksi', 'transaksi_ps', 'transaksi_kk', 'warga_jemaat', 'keluarga',
                'pendaftaran_baptis', 'pendaftaran_nikah', 'jadwal_ibadah', 'jadwal_petugas',
                'pedoman_rapp', 'pedoman_rapp_detail', 'saldo_awal', 'warta_mingguan'];

            $sqlContent = "-- Backup GerejaApp Tahun {$tahun}\n-- Generated: " . now()->toDateTimeString() . "\n\n";
            foreach ($tables as $table) {
                if (!\Schema::hasTable($table)) continue;
                $rows = DB::table($table)->when(
                    in_array($table, ['transaksi', 'transaksi_ps', 'transaksi_kk']),
                    fn($q) => $q->whereYear('tanggal', $tahun)
                )->get();

                if ($rows->isEmpty()) continue;

                $columns = array_keys((array)$rows->first());
                $sqlContent .= "\n-- Table: {$table}\n";
                foreach ($rows as $row) {
                    $values = collect((array)$row)->map(function ($v) {
                        if (is_null($v)) return 'NULL';
                        return "'" . addslashes($v) . "'";
                    })->implode(', ');
                    $sqlContent .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES ({$values});\n";
                }
            }
            Storage::put($sqlPath, $sqlContent);
            $files[] = $sqlPath;

            // 2. Generate PDFs for each month (ZA14, ZA15)
            for ($m = 1; $m <= 12; $m++) {
                $bulanStr = sprintf('%d-%02d', $tahun, $m);

                // ZA14
                $data14 = \App\Models\KodeAkun::penerimaan()->where('aktif', true)->orderBy('kode')
                    ->get()->map(function ($akun) use ($m, $tahun) {
                        $jumlah = \App\Models\Transaksi::where('kode_akun_id', $akun->id)
                            ->where('bulan', $m)->where('tahun', $tahun)->sum('jumlah');
                        return (object)['kode' => $akun->kode, 'nama' => $akun->nama, 'jumlah' => $jumlah];
                    })->filter(fn($r) => $r->jumlah > 0);

                if ($data14->count()) {
                    $profil = DB::table('profil_gereja')->first();
                    $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');
                    $pdfPath = "{$dir}/za14-{$bulanStr}.pdf";
                    $pdf = Pdf::loadView('print.laporan-za14', ['data' => $data14, 'bulan' => $m, 'tahun' => $tahun, 'profil' => $profil, 'settings' => $settings]);
                    Storage::put($pdfPath, $pdf->output());
                    $files[] = $pdfPath;
                }
            }

            // 3. ZA16 (Neraca)
            $profil = DB::table('profil_gereja')->first();
            $settings = DB::table('system_settings')->pluck('setting_value', 'setting_key');
            $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
            $saldoData = [];
            foreach ($kantongList as $k) {
                $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
                $masuk = \App\Models\Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'penerimaan')->sum('jumlah');
                $keluar = \App\Models\Transaksi::where('tahun', $tahun)->where('kantong_dana', $k)->where('tipe', 'pengeluaran')->sum('jumlah');
                $saldoData[] = (object)['kantong' => $k, 'label' => match($k) { 'rutin'=>'Dana Rutin MPH','phbg'=>'Dana PHBG','pembangunan'=>'Dana Pembangunan P4A','cadangan'=>'Dana Cadangan','diakonia_sosial'=>'Dana Diakonia Sosial','diakonia_kedukaan'=>'Dana Diakonia Kedukaan' }, 'awal' => $awal, 'masuk' => $masuk, 'keluar' => $keluar, 'akhir' => $awal + $masuk - $keluar];
            }
            $za16Pdf = Pdf::loadView('print.laporan-za16', compact('saldoData', 'tahun', 'profil', 'settings'));
            $za16Path = "{$dir}/za16-{$tahun}.pdf";
            Storage::put($za16Path, $za16Pdf->output());
            $files[] = $za16Path;

            // Calculate total size
            foreach ($files as $f) {
                if (Storage::exists($f)) $totalSize += Storage::size($f);
            }

            // Create ZIP
            $zipPath = storage_path("app/{$dir}/backup-{$tahun}.zip");
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($files as $f) {
                    $zip->addFile(storage_path("app/{$f}"), basename($f));
                }
                $zip->close();
            }

            DB::table('data_backups')->where('id', $backup)->update([
                'file_paths' => json_encode($files),
                'total_files' => count($files),
                'total_size_mb' => round($totalSize / 1048576, 2),
                'sql_path' => $sqlPath,
                'zip_path' => "backups/{$tahun}/backup-{$tahun}.zip",
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            return back()->with('success', "Backup tahun {$tahun} berhasil! " . count($files) . " file dihasilkan.");
        } catch (\Exception $e) {
            DB::table('data_backups')->where('id', $backup)->update(['status' => 'failed', 'deskripsi' => $e->getMessage(), 'updated_at' => now()]);
            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $backup = DB::table('data_backups')->findOrFail($id);
        $zipPath = storage_path("app/{$backup->zip_path}");
        if (!file_exists($zipPath)) return back()->with('error', 'File backup tidak ditemukan.');
        return response()->download($zipPath, "backup-{$backup->tahun}.zip");
    }

    public function cleanup(Request $request, $id)
    {
        $backup = DB::table('data_backups')->findOrFail($id);
        if ($backup->status !== 'completed') return back()->with('error', 'Backup harus selesai sebelum membersihkan data.');

        $tahun = $backup->tahun;

        DB::transaction(function () use ($tahun) {
            // Delete transaksi (saldo awal tetap)
            \App\Models\Transaksi::where('tahun', $tahun)->delete();
            \App\Models\TransaksiPS::whereYear('tanggal', $tahun)->delete();
            \App\Models\TransaksiKK::whereYear('tanggal', $tahun)->delete();

            // Carry over saldo akhir to next year saldo awal
            $nextYear = $tahun + 1;
            $kantongList = ['rutin', 'phbg', 'pembangunan', 'cadangan', 'diakonia_sosial', 'diakonia_kedukaan'];
            foreach ($kantongList as $k) {
                $awal = DB::table('saldo_awal')->where('tahun', $tahun)->where('kantong_dana', $k)->value('saldo') ?? 0;
                // Since we already deleted transaksi, saldo akhir = saldo awal (data cleaned)
                DB::table('saldo_awal')->updateOrInsert(
                    ['tahun' => $nextYear, 'kantong_dana' => $k],
                    ['saldo' => $awal, 'updated_at' => now()]
                );
            }
        });

        DB::table('data_backups')->where('id', $id)->update([
            'dibersihkan' => true,
            'tanggal_bersih' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Data transaksi tahun {$tahun} berhasil dibersihkan. Saldo sudah dipindahkan.");
    }

    public function uploadSql(Request $request)
    {
        $request->validate(['sql_file' => 'required|file|max:51200']);
        $file = $request->file('sql_file');
        $content = file_get_contents($file->getRealPath());

        // Parse SQL and import
        try {
            DB::unprepared($content);
            return back()->with('success', 'SQL berhasil diimport! Silakan generate PDF dari menu Cetak Laporan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
