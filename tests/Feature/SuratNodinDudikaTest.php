<?php

namespace Tests\Feature;

use App\Models\Asn;
use App\Models\DaftarDudika;
use App\Models\PesertaSuratUsulan;
use App\Models\SuratNodin;
use Tests\TestCase;

class SuratNodinDudikaTest extends TestCase
{
    private $testAsn = null;
    private $dudikas = null;
    private $createdSuratId = null;

    public function setUp(): void
    {
        parent::setUp();

        // phpunit.xml forces an empty :memory: SQLite DB. This project's
        // migrations cannot run fresh (pre-existing ordering issue on
        // surat_tugas). Use the existing file database which already
        // contains all tables (incl. daftar_dudika + dudika_id column).
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
    }

    protected function tearDown(): void
    {
        if ($this->createdSuratId) {
            PesertaSuratUsulan::where('surat_nodin_id', $this->createdSuratId)->delete();
            if ($surat = SuratNodin::find($this->createdSuratId)) {
                $surat->delete();
            }
            $this->createdSuratId = null;
        }

        if ($this->testAsn) {
            $this->testAsn->delete();
            $this->testAsn = null;
        }

        parent::tearDown();
    }

    private function basePayload(array $peserta): array
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

    private function resolveDudikas()
    {
        // Reuse existing DUDIKA rows (already seeded) to avoid FK/unique issues.
        if ($this->dudikas === null) {
            $this->dudikas = DaftarDudika::orderBy('id')
                ->take(3)
                ->get()
                ->keyBy('nama_dudika')
                ->all();
        }
        return $this->dudikas;
    }

    public function test_single_dudika_is_stored_and_appears_on_print(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Dudika Single',
            'jk' => 'L',
            'nip' => 'DUD-SINGLE',
        ]);

        $d = $this->resolveDudikas();
        $keys = array_keys($d);
        $dudika1 = $d[$keys[0]];

        $response = $this->post(route('surat-nodins.store'), $this->basePayload([
            [
                'pegawai_id' => [$this->testAsn->id],
                'dudika_id' => [$dudika1->id],
                'tgl_awal_kegiatan' => '2026-09-26',
                'tgl_akhir_kegiatan' => '2026-09-26',
            ],
        ]));

        $response->assertRedirect();

        $surat = SuratNodin::orderByDesc('id')->first();
        $this->createdSuratId = $surat->id;

        $peserta = PesertaSuratUsulan::where('surat_nodin_id', $surat->id)->first();
        $this->assertNotNull($peserta);
        $this->assertEquals($dudika1->id, $peserta->dudika_id);
        $this->assertEquals($dudika1->nama_dudika, $peserta->tempat_kegiatan);

        $print = $this->get(route('surat-nodins.print', $surat));
        $print->assertStatus(200);
        $print->assertSee($dudika1->nama_dudika);
        $print->assertSee('Tempat');
    }

    public function test_multiple_dudika_each_becomes_tempat_kegiatan(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Dudika Multi',
            'jk' => 'L',
            'nip' => 'DUD-MULTI',
        ]);

        $d = $this->resolveDudikas();
        $keys = array_keys($d);
        $dudika1 = $d[$keys[0]];
        $dudika2 = $d[$keys[1]];

        $response = $this->post(route('surat-nodins.store'), $this->basePayload([
            [
                'pegawai_id' => [$this->testAsn->id],
                'dudika_id' => [$dudika1->id, $dudika2->id],
                'tgl_awal_kegiatan' => '2026-09-26',
                'tgl_akhir_kegiatan' => '2026-09-26',
            ],
        ]));

        $response->assertRedirect();

        $surat = SuratNodin::orderByDesc('id')->first();
        $this->createdSuratId = $surat->id;

        $pesertas = PesertaSuratUsulan::where('surat_nodin_id', $surat->id)->get();
        $this->assertCount(2, $pesertas, 'Harus ada 2 rekamat (satu per DUDIKA).');

        $tempats = $pesertas->pluck('tempat_kegiatan')->toArray();
        $this->assertContains($dudika1->nama_dudika, $tempats);
        $this->assertContains($dudika2->nama_dudika, $tempats);

        $dudikaIds = $pesertas->pluck('dudika_id')->toArray();
        $this->assertContains($dudika1->id, $dudikaIds);
        $this->assertContains($dudika2->id, $dudikaIds);

        $print = $this->get(route('surat-nodins.print', $surat));
        $print->assertStatus(200);
        $print->assertSee($dudika1->nama_dudika);
        $print->assertSee($dudika2->nama_dudika);
    }

    public function test_tanggal_kegiatan_wajib_diisi(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Tanpa Tanggal',
            'jk' => 'L',
            'nip' => 'TGL-KOSONG',
        ]);

        $response = $this->from(route('surat-nodins.create'))->post(
            route('surat-nodins.store'),
            $this->basePayload([
                [
                    'pegawai_id' => [$this->testAsn->id],
                    'dudika_id' => [],
                    'tgl_awal_kegiatan' => '',
                    'tgl_akhir_kegiatan' => '',
                ],
            ])
        );

        $response->assertRedirect(route('surat-nodins.create'));
        $response->assertSessionHasErrors(['peserta.0.tgl_awal_kegiatan']);
        $response->assertSessionHasErrors(['peserta.0.tgl_akhir_kegiatan']);
    }

    public function test_tanggal_selesai_tidak_boleh_lebih_awal(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Tanggal Terbalik',
            'jk' => 'L',
            'nip' => 'TGL-TERBALIK',
        ]);

        $response = $this->from(route('surat-nodins.create'))->post(
            route('surat-nodins.store'),
            $this->basePayload([
                [
                    'pegawai_id' => [$this->testAsn->id],
                    'dudika_id' => [],
                    'tgl_awal_kegiatan' => '2026-10-10',
                    'tgl_akhir_kegiatan' => '2026-10-01',
                ],
            ])
        );

        $response->assertSessionHasErrors(['peserta.0.tgl_akhir_kegiatan']);
    }

    public function test_kartu_kosong_tidak_divalidasi(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Kartu Kosong',
            'jk' => 'L',
            'nip' => 'TGL-KARTU-KOSONG',
        ]);

        $d = $this->resolveDudikas();
        $keys = array_keys($d);

        $response = $this->post(route('surat-nodins.store'), $this->basePayload([
            [
                'pegawai_id' => [$this->testAsn->id],
                'dudika_id' => [$d[$keys[0]]->id],
                'tgl_awal_kegiatan' => '2026-09-26',
                'tgl_akhir_kegiatan' => '2026-09-26',
            ],
            [
                'pegawai_id' => [],
                'siswa_id' => [],
                'dudika_id' => [],
                'tgl_awal_kegiatan' => '',
                'tgl_akhir_kegiatan' => '',
            ],
        ]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $surat = SuratNodin::orderByDesc('id')->first();
        $this->createdSuratId = $surat->id;

        $this->assertSame(1, PesertaSuratUsulan::where('surat_nodin_id', $surat->id)->count());
    }

    public function test_no_dudika_keeps_typed_tempat(): void
    {
        $this->testAsn = Asn::create([
            'nama' => 'Pegawai Dudika None',
            'jk' => 'P',
            'nip' => 'DUD-NONE',
        ]);

        $response = $this->post(route('surat-nodins.store'), $this->basePayload([
            [
                'pegawai_id' => [$this->testAsn->id],
                'dudika_id' => [],
                'tempat_kegiatan' => ['Lokasi Langsung'],
                'tgl_awal_kegiatan' => '2026-09-26',
                'tgl_akhir_kegiatan' => '2026-09-26',
            ],
        ]));

        $response->assertRedirect();

        $surat = SuratNodin::orderByDesc('id')->first();
        $this->createdSuratId = $surat->id;

        $peserta = PesertaSuratUsulan::where('surat_nodin_id', $surat->id)->first();
        $this->assertNull($peserta->dudika_id);
        $this->assertEquals('Lokasi Langsung', $peserta->tempat_kegiatan);

        $print = $this->get(route('surat-nodins.print', $surat));
        $print->assertStatus(200);
        $print->assertSee('Lokasi Langsung');
    }
}
