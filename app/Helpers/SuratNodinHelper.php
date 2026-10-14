<?php

namespace App\Helpers;

use App\Models\DaftarDudika;
use App\Models\SuratNodin;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class SuratNodinHelper
{
    /**
     * Daftar bulan Indonesia.
     */
    public static function bulanIndonesia(): array
    {
        return [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    /**
     * Daftar hari Indonesia.
     */
    public static function hariIndonesia(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }

    /**
     * Normalisasi tanggal ke format Y-m-d.
     */
    public static function normalisasiTanggal($tanggal): string
    {
        if (empty($tanggal)) {
            return '';
        }

        try {
            return Carbon::parse($tanggal)->format('Y-m-d');
        } catch (\Throwable $e) {
            return (string) $tanggal;
        }
    }

    /**
     * Format tanggal Indonesia.
     */
    public static function formatTanggal(
        $date,
        string $format = '%d %B %Y'
    ): string {
        if (empty($date)) {
            return '-';
        }

        try {
            $carbon = $date instanceof CarbonInterface
                ? $date
                : Carbon::parse($date);
        } catch (\Throwable $e) {
            return (string) $date;
        }

        $out = $format;

        $out = str_replace(
            '%d',
            str_pad($carbon->format('d'), 2, '0', STR_PAD_LEFT),
            $out
        );
        $out = str_replace('%m', $carbon->format('m'), $out);
        $out = str_replace('%Y', $carbon->format('Y'), $out);

        if (str_contains($format, '%B')) {
            $out = str_replace(
                '%B',
                self::bulanIndonesia()[(int) $carbon->format('n')] ?? '',
                $out
            );
        }

        if (str_contains($format, '%A')) {
            $out = str_replace(
                '%A',
                self::hariIndonesia()[(int) $carbon->format('N')] ?? '',
                $out
            );
        }

        return $out;
    }

    /**
     * Format rentang tanggal.
     */
    public static function formatTanggalRange($awal, $akhir): string
    {
        $awal  = self::normalisasiTanggal($awal);
        $akhir = self::normalisasiTanggal($akhir);

        if ($awal && $akhir) {
            try {
                $cAwal  = Carbon::parse($awal);
                $cAkhir = Carbon::parse($akhir);
            } catch (\Throwable $e) {
                return '';
            }

            if ($cAwal->isSameDay($cAkhir)) {
                return self::formatTanggal($cAwal, '%d %B %Y');
            }

            $sameMonth = $cAwal->format('m') === $cAkhir->format('m');
            $sameYear  = $cAwal->format('Y') === $cAkhir->format('Y');

            if ($sameMonth && $sameYear) {
                return self::formatTanggal($cAwal, '%d')
                    . ' s.d. '
                    . self::formatTanggal($cAkhir, '%d %B %Y');
            }

            return self::formatTanggal($cAwal, '%d %B %Y')
                . ' s.d. '
                . self::formatTanggal($cAkhir, '%d %B %Y');
        }

        if ($awal) {
            return self::formatTanggal($awal, '%d %B %Y');
        }

        return '';
    }

    /**
     * Bangun grup peserta untuk ditampilkan di cetakan.
     *
     * Mengelompokkan peserta berdasarkan:
     * - Pegawai (jika ada)
     * - Tanggal kegiatan
     * - Nama pegawai/siswa
     *
     * Siswa yang menyertai pegawai akan digabung
     * sebagai daftar bernomor di baris yang sama.
     */
    public static function buildPesertaGroups(SuratNodin $suratNodin): array
    {
        $pesertaList = collect($suratNodin->pesertaSuratUsulans ?? [])
            ->filter(fn ($p) => $p->pegawai_id || $p->siswa_id || $p->siswa)
            ->values();

        $groups = [];

        foreach ($pesertaList as $peserta) {
            $awalRaw  = self::normalisasiTanggal($peserta->tgl_awal_kegiatan ?? null);
            $akhirRaw = self::normalisasiTanggal($peserta->tgl_akhir_kegiatan ?? null);

            /* -------- Tentukan tempat -------- */
            $tempat = self::resolveTempat($peserta);

            /* -------- Tentukan entity (pegawai / siswa) -------- */
            $pegawai = null;
            $siswa   = $peserta->siswa ?? $peserta->pesertaSiswa ?? null;

            if ($peserta->pegawai_id && $peserta->pegawai) {
                $pegawai = $peserta->pegawai;
            } elseif (!$siswa && empty($peserta->siswa_id)) {
                continue;
            }

            if ($pegawai) {
                $groupKey = 'pegawai_' . $peserta->pegawai_id . '_' . $awalRaw . '_' . $akhirRaw;
                $type     = 'pegawai';
                $entity   = $pegawai;
            } else {
                $siswaId  = $siswa->id ?? $peserta->siswa_id ?? 'unknown';
                $groupKey = 'siswa_' . $siswaId . '_' . $awalRaw . '_' . $akhirRaw;
                $type     = 'siswa';
                $entity   = $siswa ?? $peserta;
            }

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'tanggal_formatted' => self::formatTanggalRange($awalRaw, $akhirRaw),
                    'type'              => $type,
                    'entity'            => $entity,
                    'tempat_list'       => [],
                    'siswa_list'        => [],
                ];
            }

            if ($tempat && !in_array($tempat, $groups[$groupKey]['tempat_list'], true)) {
                $groups[$groupKey]['tempat_list'][] = $tempat;
            }

            /* -------- Gabungkan siswa ke grup pegawai -------- */
            if ($pegawai && $siswa) {
                $siswaKey = $siswa->id ?? $peserta->siswa_id ?? 'unknown';

                if (!array_key_exists($siswaKey, $groups[$groupKey]['siswa_list'])) {
                    $groups[$groupKey]['siswa_list'][$siswaKey] = $siswa;
                }
            }
        }

        return array_values($groups);
    }

    /**
     * Ambil nama tempat dari relasi dudika / kolom tempat_kegiatan.
     */
    protected static function resolveTempat($peserta): string
    {
        $dudikaVal = '';

        if (isset($peserta->dudika)) {
            $dudikaVal = is_object($peserta->dudika)
                ? ($peserta->dudika->nama_dudika
                    ?? $peserta->dudika->nama
                    ?? $peserta->dudika->name
                    ?? '')
                : $peserta->dudika;
        }

        if (empty($dudikaVal)) {
            $dudikaVal = $peserta->tempat_kegiatan ?? '-';
        }

        return trim((string) $dudikaVal);
    }

    /**
     * Apakah grup peserta memiliki siswa (untuk header tabel).
     */
    public static function hasSiswa(array $groups): bool
    {
        return collect($groups)
            ->contains(fn ($g) => ($g['type'] ?? null) === 'siswa');
    }

    /**
     * Normalisasi array id dari input form.
     */
    public static function normalizeIdArray($values): array
    {
        if (!is_array($values)) {
            $values = ($values !== null && $values !== '')
                ? [$values]
                : [];
        }

        return collect($values)
            ->flatten()
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Normalisasi array tempat kegiatan.
     */
    public static function normalizeTempatArray($values): array
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $result = collect($values)
            ->flatten()
            ->map(fn ($t) => trim((string) $t))
            ->filter(fn ($t) => $t !== '')
            ->unique()
            ->values()
            ->all();

        return empty($result) ? [null] : $result;
    }
}