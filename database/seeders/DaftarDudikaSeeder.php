<?php

namespace Database\Seeders;

use App\Models\DaftarDudika;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaftarDudikaSeeder extends Seeder
{
    public function run(): void
    {
        $dudikas = [
            'BENGKEL BUANA PUTRA MANDIRI',
            'BENGKEL KAHAR MOTOR KOBA',
            'BPS BATENG',
            'PT. ANUGRAH LAUT BANGKA',
            'EKO BERKAH SERVICE',
            'PT. MENTARI SAWIT MAKMUR',
            'PT. DOK DAN PERKAPALAN AIR KANTUNG',
            'DEDE COMPUTER - PANGKALPINANG',
            'BENGKEL AHASS HONDA DAYA MOTOR KOBA',
            'BENGKEL RESMI YAMAHA BENGKEL SARI MOTOR',
            'BENGKEL RAMA MOTOR KURAU',
            'BENGKEL RESMI YAMAHA (CV. SUMBER JADI PANGKALPINANG)',
            'BENGKEL HERLAN MOTOR KURAU',
            'BENGKEL MOTOR 1945 PANGKALPINANG',
            'BENGKEL NOPI MOTOR KURAU',
            'ARTAMEDIA - PANGKALPINANG',
            'DUTA COMPUTER - PANGKALPINANG',
            'ISB ATMA LUHUR',
            'SOULMATE COMPUTER - PANGKALPINANG',
            'BENGKEL AKIONG',
            'BENGKEL ATHU',
            'BENGKEL BUANA PUTRA MANDIRI',
            'PT. ISTANA AGUNG (TOYOTA)',
            'BENGKEL SRH',
            'EVAN COMPUTER',
            'FALAH COMPUTER',
            'PT. BESAOH',
            'BENGKEL LAS KEJORA',
            'SENTRA COMPUTER - PANGKALPINANG',
            'PT. PUTRA BANGKA TANI',
            'PT. PLN CABANG KOBA',
            'BKPSDMD',
            'PT. PAHALA HARAPAN LESTARI',
            'BENGKEL AFU',
            'PT. DOK DAN PERKAPALAN AIR KANTUNG',
            'PT. PAHALA HARAPAN LESTARI',
            'GRAND SAFRAN HOTEL',
            'PT. DAK',
            'BENGKEL AIYRA MOTOR TERENTANG',
            'BENGKEL JULI HANDY MOTOR KOBA',
            'BLK PROV. BABEL',
            'DINAS PERHUBUNGAN BANGKA TENGAH',
            'BENGKEL ANDI MOTOR TRUBUS',
            'BENGKEL BAMBANG MOTOR KOBA',
            'PT SAMUDRA MITRAJAYA MANDIRI',
            'PT. SUMBER TAMBAK ABADI',
            'PT. SENTOSA JAYA PURNAMA',
            'CV. HERI TEKNIK',
            'BENGKEL AKIONG',
            'BENGKEL PAUN',
            'BENGKEL VEKO',
            'PLN ICON PLUS - PANGKALAN BARU',
            "WELL'S COMPUTER - PANGKALPINANG",
            'PT. PUTRA BANGKA TANI',
            'RSUD ABU HANIFAH',
            'KEPALA SEKOLAH,',
            'KANTOR DESA PENYAK',
            'PT. BESAOH',
            'BENGKEL ATHU',
            'BENGKEL SRH',
        ];

        // Deduplicate while preserving order, since duplicates provide no extra value for a reference list
        $unique = array_values(array_unique($dudikas));

        foreach ($unique as $nama) {
            DaftarDudika::firstOrCreate(
                ['nama_dudika' => $nama],
                ['nama_dudika' => $nama]
            );
        }

        $this->command->info(count($unique).' data DUDIKA berhasil diimport.');
    }
}
