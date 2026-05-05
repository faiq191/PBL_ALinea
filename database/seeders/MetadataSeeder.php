<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;
use App\Models\Type;
use App\Models\Demographic;
use App\Models\Year;

class MetadataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. GENRE - Mencakup Literatur Klasik & Populer
        $genres = [
            'Fiksi Ilmiah',
            'Fantasi',
            'Misteri',
            'Horor',
            'Thriller',
            'Romansa',
            'Psikologi',
            'Filsafat',
            'Sejarah',
            'Biografi',
            'Klasik',
            'Puisi',
            'Drama',
            'Petualangan',
            'Kriminal',
            'Self-Improvement',
            'Bisnis',
            'Teknologi',
            'Sains',
            'Seni',
            'Religi',
            'Manga',
            'Light Novel',
            'Komik',
            'Kesehatan'
        ];

        foreach ($genres as $name) {
            Genre::firstOrCreate(['name' => $name]);
        }

        // 2. TIPE BUKU
        $types = [
            'Novel',
            'Komik',
            'Manga',
            'Light Novel',
            'Majalah',
            'Jurnal',
            'Ensiklopedia',
            'Antologi',
            'Buku Pelajaran'
        ];

        foreach ($types as $name) {
            Type::firstOrCreate(['name' => $name]);
        }

        // 3. DEMOGRAFIS PEMBACA
        $demographics = [
            'Anak-anak',
            'Remaja',
            'Dewasa',
            'Lansia',
            'Shounen',
            'Seinen',
            'Shoujo',
            'Josei',
            'Semua Umur'
        ];

        foreach ($demographics as $name) {
            Demographic::firstOrCreate(['name' => $name]);
        }

        // 4. TAHUN RILIS (Mencakup buku klasik hingga masa depan)
        for ($y = 1850; $y <= 2026; $y++) {
            Year::firstOrCreate(['year' => $y]);
        }
    }
}
