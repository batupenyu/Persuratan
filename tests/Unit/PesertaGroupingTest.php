<?php

namespace Tests\Unit;

use App\Support\PesertaGrouping;
use PHPUnit\Framework\TestCase;

class PesertaGroupingTest extends TestCase
{
    private function row($pegawaiId, $siswaId, $tempat): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'siswa_id' => $siswaId,
            'tempat' => $tempat,
        ];
    }

    public function test_kartu_pegawai_dan_siswa_tetap_satu_kartu(): void
    {
        $rows = [
            $this->row(1, 10, 'Tempat A'),
            $this->row(1, 10, 'Tempat B'),
            $this->row(1, 11, 'Tempat A'),
            $this->row(1, 11, 'Tempat B'),
        ];

        $groups = PesertaGrouping::group($rows);

        $this->assertCount(1, $groups);
        $this->assertCount(4, $groups[0]);
    }

    public function test_kartu_pegawai_tanpa_siswa_tetap_satu_kartu(): void
    {
        $rows = [
            $this->row(1, null, 'Tempat A'),
            $this->row(1, null, 'Tempat B'),
            $this->row(1, null, 'Tempat C'),
        ];

        $this->assertCount(1, PesertaGrouping::group($rows));
    }

    public function test_kartu_dua_pegawai_tanpa_siswa_tetap_satu_kartu(): void
    {
        $rows = [
            $this->row(1, null, 'Tempat A'),
            $this->row(1, null, 'Tempat B'),
            $this->row(2, null, 'Tempat A'),
            $this->row(2, null, 'Tempat B'),
        ];

        $this->assertCount(1, PesertaGrouping::group($rows));
    }

    public function test_kartu_pegawai_dan_siswa_lengkap_tetap_satu_kartu(): void
    {
        $rows = [];

        foreach ([1, 2] as $pegawaiId) {
            foreach ([10, 11] as $siswaId) {
                foreach (['Tempat A', 'Tempat B'] as $tempat) {
                    $rows[] = $this->row($pegawaiId, $siswaId, $tempat);
                }
            }
        }

        $this->assertCount(1, PesertaGrouping::group($rows));
    }

    public function test_kartu_hanya_siswa_tetap_satu_kartu(): void
    {
        $rows = [
            $this->row(null, 10, 'Tempat A'),
            $this->row(null, 10, 'Tempat B'),
        ];

        $this->assertCount(1, PesertaGrouping::group($rows));
    }

    public function test_kartu_berbeda_tidak_menumpuk(): void
    {
        $rows = [
            $this->row(1, 10, 'Tempat A'),
            $this->row(1, 11, 'Tempat A'),
            $this->row(1, 10, 'Tempat A'),
            $this->row(1, 11, 'Tempat A'),
            $this->row(1, 12, 'Tempat C'),
        ];

        $this->assertCount(2, PesertaGrouping::group($rows));
    }

    public function test_kartu_dengan_tempat_sama_tetap_terpisah(): void
    {
        $rows = [
            $this->row(1, 10, 'Tempat A'),
            $this->row(2, 11, 'Tempat A'),
        ];

        $this->assertCount(2, PesertaGrouping::group($rows));
    }

    public function test_data_lama_satu_pegawai_per_kartu(): void
    {
        $rows = [
            $this->row(1, null, "Dudika A\nDudika B"),
            $this->row(2, null, "Dudika C\nDudika D"),
            $this->row(3, null, "Dudika E"),
        ];

        $this->assertCount(3, PesertaGrouping::group($rows));
    }

    public function test_daftar_kosong(): void
    {
        $this->assertSame([], PesertaGrouping::group([]));
    }
}
