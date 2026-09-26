<?php

namespace Tests\Feature;

use App\Models\Asn;
use App\Models\DaftarDudika;
use App\Models\PesertaSuratUsulan;
use App\Models\SuratNodin;
use Tests\TestCase;

class SuratNodinDudikaTest extends TestCase
{
    private ?Asn $testAsn = null;
    private ?DaftarDudika $testDudika = null;
    private ?SuratNodin $createdSurat = null;

    protected function setUp(): void
    {
        parent::setUp();

        // phpunit.xml forces an empty :memory: SQLite DB. This project's
        // migrations cannot run fresh (pre-existing ordering issue on
        // surat_tugas). Point the test at the existing file database
        // which already contains all tables (incl. daftar_dudika + column).
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
    }

    protected function tearDown(): void
    {
        if ($this->createdSurat) {
            $this->createdSurat->pesertaSuratUsulans()->delete();
            $this->createdSurat->delete();
            $this->createdSurat = null;
        }

        if ($this->testDudika) {
            $this->testDudika->delete();
            $this->testDudika = null;
        }

        if ($this->testAsn) {
            $this->testAsn->delete();
            $this->testAsn = null;
        }

        parent::tearDown();
    }

    private function validBasePayload(array $peserta): array
    {
        return [
            'dari' => 'Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung',
            'tanggal' => '2026-09-26',
            'dari_plt' => 0,
            'dari_an' => 0,
            'penandatangan_plt' => 0,
            'penandatangan_an' => 0,
            'peserta' => $peserta,
        ];
    }

    public function test_selected_dudika_is_stored_and_appears_on_tempat_kegiatan(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Dudika Test',
            'jk' => 'L',
            'nip' => 'DUDIKA-9001',
        ]);

        $this->testDudika = DaftarDudika::create([
            'nama_dudika' => 'BENGKEL AHASS HONDA KOBA (TEST)',
        ]);

        $response = $this->post(route('surat-nodins.store'), $this->validBasePayload([
            [
                'pegawai_id' => [$this->testAsn->id],
                'dudika_id' => $this->testDudika->id,
                'tgl_awal_kegiatan' => '2026-09-26',
                'tgl_akhir_kegiatan' => '2026-09-26',
            ],
        ]));

        $response->assertRedirect();

        $surat = SuratNodin::orderByDesc('id')->first();
        $this->createdSurat = $surat;
        $this->assertNotNull($surat);

        $peserta = PesertaSuratUsulan::where('surat_nodin_id', $surat->id)->first();
        $this->assertNotNull($peserta, 'Peserta tidak tersimpan.');
        $this->assertEquals($this->testDudika->id, $peserta->dudika_id);
        $this->assertEquals('BENGKEL AHASS HONDA KOBA (TEST)', $peserta->tempat_kegiatan);

        $print = $this->get(route('surat-nodins.print', $surat));
        $print->assertStatus(200);
        $print->assertSee('BENGKEL AHASS HONDA KOBA (TEST)');
        $print->assertSee('Tempat Kegiatan');
    }

    public function test_no_dudika_leaves_tempat_kegiatan_as_typed_value(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Dudika Test 2',
            'jk' => 'P',
            'nip' => 'DUDIKA-9002',
        ]);

        $this->testDudika = DaftarDudika::create([
            'nama_dudika' => 'BENGKEL AHASS HONDA KOBA (TEST)',
        ]);

        $response = $this->post(route('surat-nodins.store'), $this->validBasePayload([
            [
                'pegawai_id' => [$this->testAsn->id],
                'dudika_id' => null,
                'tempat_kegiatan' => ['Lokasi Langsung'],
                'tgl_awal_kegiatan' => '2026-09-26',
                'tgl_akhir_kegiatan' => '2026-09-26',
            ],
        ]));

        $response->assertRedirect();

        $surat = SuratNodin::orderByDesc('id')->first();
        $this->createdSurat = $surat;
        $this->assertNotNull($surat);

        $peserta = PesertaSuratUsulan::where('surat_nodin_id', $surat->id)->first();
        $this->assertNull($peserta->dudika_id);
        $this->assertEquals('Lokasi Langsung', $peserta->tempat_kegiatan);

        $print = $this->get(route('surat-nodins.print', $surat));
        $print->assertStatus(200);
        $print->assertSee('Lokasi Langsung');
    }
}
