<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'image', 'color', 'boolean', 'url'])->default('text');
            $table->timestamps();
        });

        Schema::create('cms_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('isi')->nullable();
            $table->string('gambar_url', 500)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->tinyInteger('prioritas')->default(1);
            $table->boolean('aktif')->default(true);
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_gallery', function (Blueprint $table) {
            $table->id();
            $table->string('foto_url', 500);
            $table->string('keterangan', 300)->nullable();
            $table->date('tanggal_kegiatan')->nullable();
            $table->tinyInteger('urutan')->default(1);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_verses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->text('teks');
            $table->string('referensi', 100);
            $table->string('versi', 20)->default('TB');
            $table->enum('sumber', ['manual', 'api'])->default('manual');
            $table->timestamps();
        });

        // Seed default CMS settings
        $defaults = [
            ['key' => 'hero_title', 'value' => 'Gereja Kalimantan Evangelis', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Majelis Jemaat GKE Palangka - Sion', 'type' => 'text'],
            ['key' => 'hero_image', 'value' => '', 'type' => 'image'],
            ['key' => 'primary_color', 'value' => '#c9a84c', 'type' => 'color'],
            ['key' => 'youtube_live_url', 'value' => '', 'type' => 'url'],
            ['key' => 'youtube_rekaman_url', 'value' => '', 'type' => 'url'],
            ['key' => 'is_live', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'alamat', 'value' => 'Jl. Gereja No. 1, Palangka Raya', 'type' => 'text'],
            ['key' => 'telepon', 'value' => '', 'type' => 'text'],
            ['key' => 'email_kontak', 'value' => '', 'type' => 'text'],
            ['key' => 'facebook_url', 'value' => '', 'type' => 'url'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'url'],
            ['key' => 'youtube_url', 'value' => '', 'type' => 'url'],
            ['key' => 'google_maps_embed', 'value' => '', 'type' => 'text'],
            ['key' => 'jam_pelayanan', 'value' => 'Senin - Jumat: 08.00 - 15.00 WIB', 'type' => 'text'],
            ['key' => 'section_jadwal', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'section_warta', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'section_pengumuman', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'section_ayat', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'section_livestream', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'section_statistik', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'section_galeri', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'meta_title', 'value' => 'GKE Sion Palangka Raya', 'type' => 'text'],
            ['key' => 'meta_description', 'value' => 'Website resmi Majelis Jemaat GKE Palangka - Gereja Sion', 'type' => 'text'],
            ['key' => 'maintenance_public', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'maintenance_admin', 'value' => '0', 'type' => 'boolean'],
        ];

        foreach ($defaults as $d) {
            $d['created_at'] = now();
            $d['updated_at'] = now();
            DB::table('cms_settings')->insert($d);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_verses');
        Schema::dropIfExists('cms_gallery');
        Schema::dropIfExists('cms_announcements');
        Schema::dropIfExists('cms_settings');
    }
};
