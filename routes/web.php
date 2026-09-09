<?php

use App\Http\Controllers\AsnController;
use App\Http\Controllers\DataSiswaController;
use App\Http\Controllers\DrhSatyalancanaController;
use App\Http\Controllers\LampiranController;
use App\Http\Controllers\LaporanCutiController;
use App\Http\Controllers\LaporanNodinController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\SpdController;
use App\Http\Controllers\SpmtController;
use App\Http\Controllers\SptjmController;
use App\Http\Controllers\SuratCutiController;
use App\Http\Controllers\FormCutiController;
use App\Http\Controllers\SuratDispensasiController;
use App\Http\Controllers\PersetujuanMagangController;
use App\Http\Controllers\SuratAktifMengajarController;
use App\Http\Controllers\SuratBersediaController;
use App\Http\Controllers\SuratKeteranganController;
use App\Http\Controllers\SuratKp4Controller;
use App\Http\Controllers\SuratKp4OldController;
use App\Http\Controllers\SuratMewakiliController;
use App\Http\Controllers\SuratNodinController;
use App\Http\Controllers\SuratPanggilanSiswaController;
use App\Http\Controllers\SuratPenarikanSiswaController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\SuratPernyataanController;
use App\Http\Controllers\SuratRekomendasiController;
use App\Http\Controllers\SuratResmiController;
use App\Http\Controllers\SuratSantunanController;
use App\Http\Controllers\SuratSkorsingController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\SuratTugasPklController;
use App\Http\Controllers\SuratUndanganController;
use App\Http\Controllers\HariLiburController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('asns/export', [AsnController::class, 'export'])->name('asns.export');
Route::get('asns/export/xlsx', [AsnController::class, 'exportXlsx'])->name('asns.export.xlsx');
Route::get('asns/import', [AsnController::class, 'import'])->name('asns.import');
Route::post('asns/import', [AsnController::class, 'importStore'])->name('asns.import.store');
Route::get('asns/print', [AsnController::class, 'print'])->name('asns.print');
Route::delete('asns/destroy-all', [AsnController::class, 'destroyAll'])->name('asns.destroy.all');
Route::resource('asns', AsnController::class);

Route::get('data-siswa/export/csv', [DataSiswaController::class, 'exportCsv'])->name('data-siswa.export.csv');
Route::get('data-siswa/export/xlsx', [DataSiswaController::class, 'exportXlsx'])->name('data-siswa.export.xlsx');
Route::get('data-siswa/import', [DataSiswaController::class, 'import'])->name('data-siswa.import');
Route::post('data-siswa/import', [DataSiswaController::class, 'importStore'])->name('data-siswa.import.store');
Route::delete('data-siswa/destroy-all', [DataSiswaController::class, 'destroyAll'])->name('data-siswa.destroy.all');
Route::resource('data-siswa', DataSiswaController::class)->parameter('data-siswa', 'siswa');

Route::resource('spds', SpdController::class)->except(['show', 'destroy']);
Route::delete('spds/{spd}', [SpdController::class, 'destroy'])->name('spds.destroy');
Route::get('spds/{spd}/print', [SpdController::class, 'print'])->name('spds.print');

Route::resource('logos', LogoController::class)->except(['show']);
Route::get('logos/{logo}/image', [LogoController::class, 'show'])->name('logos.image');
Route::get('api/kop-surat', [LogoController::class, 'apiKopSurat'])->name('api.kop-surat');

Route::resource('sptjms', SptjmController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('sptjms/{sptjm}', [SptjmController::class, 'destroy'])->name('sptjms.destroy');
Route::get('sptjms/{sptjm}/print', [SptjmController::class, 'print'])->name('sptjms.print');
Route::resource('drh-satyalancana', DrhSatyalancanaController::class)
    ->only(['index', 'create', 'store', 'edit', 'update'])
    ->parameter('drh-satyalancana', 'drh');
Route::delete('drh-satyalancana/{drh}', [DrhSatyalancanaController::class, 'destroy'])->name('drh-satyalancana.destroy');
Route::get('drh-satyalancana/{drh}/print', [DrhSatyalancanaController::class, 'print'])->name('drh-satyalancana.print');
Route::resource('surat-tugas-pkls', SuratTugasPklController::class)
    ->except(['show', 'destroy'])
    ->parameter('surat-tugas-pkls', 'surat_tugas_pkl');
Route::delete('surat-tugas-pkls/{surat_tugas_pkl}', [SuratTugasPklController::class, 'destroy'])->name('surat-tugas-pkl.destroy');
Route::get('surat-tugas-pkls/{surat_tugas_pkl}/print', [SuratTugasPklController::class, 'print'])->name('surat-tugas-pkl.print');
Route::resource('lampirans', LampiranController::class)
    ->except(['show']);
Route::delete('lampirans/{lampiran}', [LampiranController::class, 'destroy'])->name('lampirans.destroy');
Route::get('lampirans/{lampiran}/print', [LampiranController::class, 'print'])->name('lampirans.print');
Route::resource('surat-tugas', SuratTugasController::class)
    ->except(['show', 'destroy'])
    ->parameter('surat-tugas', 'surat_tugas');
Route::delete('surat-tugas/{surat_tugas}', [SuratTugasController::class, 'destroy'])->name('surat-tugas.destroy');
Route::get('surat-tugas/{surat_tugas}/print', [SuratTugasController::class, 'print'])->name('surat-tugas.print');
Route::resource('spmts', SpmtController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('spmts/{spmt}', [SpmtController::class, 'destroy'])->name('spmts.destroy');
Route::get('spmts/{spmt}/print', [SpmtController::class, 'print'])->name('spmts.print');
Route::resource('surat-cutis', SuratCutiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-cutis/{surat_cuti}', [SuratCutiController::class, 'destroy'])->name('surat-cutis.destroy');
Route::get('surat-cutis/{surat_cuti}/print', [SuratCutiController::class, 'print'])->name('surat-cutis.print');
Route::resource('form-cutis', FormCutiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('form-cutis/{form_cuti}', [FormCutiController::class, 'destroy'])->name('form-cutis.destroy');
Route::get('form-cutis/{form_cuti}/print', [FormCutiController::class, 'print'])->name('form-cutis.print');
Route::resource('laporan-cutis', LaporanCutiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('laporan-cutis/{laporan_cuti}', [LaporanCutiController::class, 'destroy'])->name('laporan-cutis.destroy');
Route::get('laporan-cutis/{laporan_cuti}/print', [LaporanCutiController::class, 'print'])->name('laporan-cutis.print');
Route::resource('laporan-nodins', LaporanNodinController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('laporan-nodins/{laporan_nodin}', [LaporanNodinController::class, 'destroy'])->name('laporan-nodins.destroy');
Route::get('laporan-nodins/{laporan_nodin}/print', [LaporanNodinController::class, 'print'])->name('laporan-nodins.print');
Route::get('laporan-nodins/{laporan_nodin}/photo-lampiran', [LaporanNodinController::class, 'photoLampiran'])->name('laporan-nodins.photo-lampiran');
Route::get('laporan-nodins/{laporan_nodin}/photos', [LaporanNodinController::class, 'photos'])->name('laporan-nodins.photos');
Route::post('laporan-nodins/{laporan_nodin}/photos', [LaporanNodinController::class, 'storePhoto'])->name('laporan-nodins.photos.store');
Route::get('laporan-nodins/{laporan_nodin}/photos/{photo}/edit', [LaporanNodinController::class, 'editPhoto'])->name('laporan-nodins.photos.edit');
Route::put('laporan-nodins/{laporan_nodin}/photos/{photo}', [LaporanNodinController::class, 'updatePhoto'])->name('laporan-nodins.photos.update');
Route::delete('laporan-nodins/{laporan_nodin}/photos/{photo}', [LaporanNodinController::class, 'destroyPhoto'])->name('laporan-nodins.photos.destroy');
Route::resource('surat-dispensasis', SuratDispensasiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-dispensasis/{surat_dispensasi}', [SuratDispensasiController::class, 'destroy'])->name('surat-dispensasis.destroy');
Route::get('surat-dispensasis/{surat_dispensasi}/print', [SuratDispensasiController::class, 'print'])->name('surat-dispensasis.print');
Route::resource('surat-keterangans', SuratKeteranganController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-keterangans/{surat_keterangan}', [SuratKeteranganController::class, 'destroy'])->name('surat-keterangans.destroy');
Route::get('surat-keterangans/{surat_keterangan}/print', [SuratKeteranganController::class, 'print'])->name('surat-keterangans.print');
Route::resource('surat-aktif-mengajars', SuratAktifMengajarController::class)
    ->only(['index', 'create', 'store', 'edit', 'update'])
    ->parameter('surat-aktif-mengajars', 'surat_aktif_mengajar');
Route::delete('surat-aktif-mengajars/{surat_aktif_mengajar}', [SuratAktifMengajarController::class, 'destroy'])->name('surat-aktif-mengajars.destroy');
Route::get('surat-aktif-mengajars/{surat_aktif_mengajar}/print', [SuratAktifMengajarController::class, 'print'])->name('surat-aktif-mengajars.print');
Route::resource('persetujuan-magangs', PersetujuanMagangController::class)
    ->only(['index', 'create', 'store', 'edit', 'update'])
    ->parameter('persetujuan-magangs', 'persetujuan_magang');
Route::delete('persetujuan-magangs/{persetujuan_magang}', [PersetujuanMagangController::class, 'destroy'])->name('persetujuan-magangs.destroy');
Route::get('persetujuan-magangs/{persetujuan_magang}/print', [PersetujuanMagangController::class, 'print'])->name('persetujuan-magangs.print');
Route::resource('surat-kp4s', SuratKp4Controller::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-kp4s/{surat_kp4}', [SuratKp4Controller::class, 'destroy'])->name('surat-kp4s.destroy');
Route::get('surat-kp4s/{surat_kp4}/print', [SuratKp4Controller::class, 'print'])->name('surat-kp4s.print');

Route::resource('surat-kp4-olds', SuratKp4OldController::class)
    ->only(['index', 'create', 'store', 'edit', 'update'])
    ->parameter('surat-kp4-olds', 'surat_kp4_old');
Route::delete('surat-kp4-olds/{surat_kp4_old}', [SuratKp4OldController::class, 'destroy'])->name('surat-kp4-olds.destroy');
Route::get('surat-kp4-olds/{surat_kp4_old}/print', [SuratKp4OldController::class, 'print'])->name('surat-kp4-olds.print');
Route::get('surat-kp4-olds/{surat_kp4_old}/print-page2', [SuratKp4OldController::class, 'printPage2'])->name('surat-kp4-olds.print-page2');
Route::resource('surat-panggilan-siswas', SuratPanggilanSiswaController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-panggilan-siswas/{surat_panggilan_siswa}', [SuratPanggilanSiswaController::class, 'destroy'])->name('surat-panggilan-siswas.destroy');
Route::get('surat-panggilan-siswas/{surat_panggilan_siswa}/print', [SuratPanggilanSiswaController::class, 'print'])->name('surat-panggilan-siswas.print');
Route::resource('surat-skorsings', SuratSkorsingController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-skorsings/{surat_skorsing}', [SuratSkorsingController::class, 'destroy'])->name('surat-skorsings.destroy');
Route::get('surat-skorsings/{surat_skorsing}/print', [SuratSkorsingController::class, 'print'])->name('surat-skorsings.print');
Route::resource('surat-pengantars', SuratPengantarController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-pengantars/{surat_pengantar}', [SuratPengantarController::class, 'destroy'])->name('surat-pengantars.destroy');
Route::get('surat-pengantars/{surat_pengantar}/print', [SuratPengantarController::class, 'print'])->name('surat-pengantars.print');
Route::resource('surat-rekomendasis', SuratRekomendasiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-rekomendasis/{surat_rekomendasi}', [SuratRekomendasiController::class, 'destroy'])->name('surat-rekomendasis.destroy');
Route::get('surat-rekomendasis/{surat_rekomendasi}/print', [SuratRekomendasiController::class, 'print'])->name('surat-rekomendasis.print');
Route::resource('surat-resmis', SuratResmiController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-resmis/{surat_resmi}', [SuratResmiController::class, 'destroy'])->name('surat-resmis.destroy');
Route::get('surat-resmis/{surat_resmi}/print', [SuratResmiController::class, 'print'])->name('surat-resmis.print');
Route::resource('surat-santunans', SuratSantunanController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-santunans/{surat_santunan}', [SuratSantunanController::class, 'destroy'])->name('surat-santunans.destroy');
Route::get('surat-santunans/{surat_santunan}/print', [SuratSantunanController::class, 'print'])->name('surat-santunans.print');
Route::resource('surat-pernyataans', SuratPernyataanController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-pernyataans/{surat_pernyataan}', [SuratPernyataanController::class, 'destroy'])->name('surat-pernyataans.destroy');
Route::get('surat-pernyataans/{surat_pernyataan}/print', [SuratPernyataanController::class, 'print'])->name('surat-pernyataans.print');
Route::resource('surat-undangans', SuratUndanganController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-undangans/{surat_undangan}', [SuratUndanganController::class, 'destroy'])->name('surat-undangans.destroy');
Route::get('surat-undangans/{surat_undangan}/print', [SuratUndanganController::class, 'print'])->name('surat-undangans.print');
Route::resource('surat-mewakili', SuratMewakiliController::class)
    ->only(['index', 'create', 'store', 'edit', 'update'])
    ->parameter('surat-mewakili', 'surat_mewakili');
Route::delete('surat-mewakili/{surat_mewakili}', [SuratMewakiliController::class, 'destroy'])->name('surat-mewakili.destroy');
Route::get('surat-mewakili/{surat_mewakili}/print', [SuratMewakiliController::class, 'print'])->name('surat-mewakili.print');
Route::resource('surat-nodins', SuratNodinController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-nodins/{surat_nodin}', [SuratNodinController::class, 'destroy'])->name('surat-nodins.destroy');
Route::get('surat-nodins/{surat_nodin}/print', [SuratNodinController::class, 'print'])->name('surat-nodins.print');
Route::get('surat-nodins/{surat_nodin}/lampiran', [SuratNodinController::class, 'lampiran'])->name('surat-nodins.lampiran');
Route::get('surat-nodins/{surat_nodin}/lampiran-tabel-peserta', [SuratNodinController::class, 'lampiranTabelPeserta'])->name('surat-nodins.lampiran-tabel-peserta');
Route::get('surat-nodins/{surat_nodin}/photo-lampiran', [SuratNodinController::class, 'photoLampiran'])->name('surat-nodins.photo-lampiran');
Route::get('surat-nodins/{surat_nodin}/photos', [SuratNodinController::class, 'photos'])->name('surat-nodins.photos');
Route::post('surat-nodins/{surat_nodin}/photos', [SuratNodinController::class, 'storePhoto'])->name('surat-nodins.photos.store');
Route::get('surat-nodins/{surat_nodin}/photos/{photo}/edit', [SuratNodinController::class, 'editPhoto'])->name('surat-nodins.photos.edit');
Route::put('surat-nodins/{surat_nodin}/photos/{photo}', [SuratNodinController::class, 'updatePhoto'])->name('surat-nodins.photos.update');
Route::delete('surat-nodins/{surat_nodin}/photos/{photo}', [SuratNodinController::class, 'destroyPhoto'])->name('surat-nodins.photos.destroy');
Route::resource('surat-penarikan-siswas', SuratPenarikanSiswaController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-penarikan-siswas/{surat_penarikan_siswa}', [SuratPenarikanSiswaController::class, 'destroy'])->name('surat-penarikan-siswas.destroy');
Route::get('surat-penarikan-siswas/{surat_penarikan_siswa}/print', [SuratPenarikanSiswaController::class, 'print'])->name('surat-penarikan-siswas.print');
Route::resource('hari-liburs', HariLiburController::class)
    ->except(['show']);
Route::resource('surat-bersedias', SuratBersediaController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
Route::delete('surat-bersedias/{surat_bersedia}', [SuratBersediaController::class, 'destroy'])->name('surat-bersedias.destroy');
Route::get('surat-bersedias/{surat_bersedia}/print', [SuratBersediaController::class, 'print'])->name('surat-bersedias.print');
