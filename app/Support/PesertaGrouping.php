<?php

namespace App\Support;

class PesertaGrouping
{
    /**
     * Mengelompokkan baris peserta menjadi kelompok
     * yang sama dengan kartu peserta pada form create.
     *
     * Penyimpanan peserta membuat kombinasi
     * (pegawai x siswa x tempat), sehingga satu kartu
     * pada form create dapat tersimpan menjadi banyak
     * baris. Kelompok lama hanya memakai rentang
     * tanggal, sehingga dua kartu yang berbeda dapat
     * berakhir menyatu menjadi satu.
     *
     * Baris dikelompokkan dengan mencari Rectangle
     * (pegawai, siswa, tempat) yang terisi penuh:
     * satu kelompok hanya diperluas ke baris yang
     * nilainya masih berada di dalam kelompok tersebut.
     *
     * @param  array<int, array{pegawai_id: mixed, siswa_id: mixed, tempat: mixed}>  $rows
     * @return array<int, array<int, array>>
     */
    public static function group(array $rows): array
    {
        $rows = array_values($rows);

        $count = count($rows);

        if ($count === 0) {
            return [];
        }

        $items = [];
        $indexST = [];
        $indexPT = [];
        $indexPS = [];

        foreach ($rows as $i => $row) {
            $p = self::key($row['pegawai_id'] ?? null, 'p');
            $s = self::key($row['siswa_id'] ?? null, 's');
            $t = self::key(self::tempatKey($row['tempat'] ?? null), 't');

            $items[$i] = ['p' => $p, 's' => $s, 't' => $t];

            $indexST[$s . '|' . $t][] = $i;
            $indexPT[$p . '|' . $t][] = $i;
            $indexPS[$p . '|' . $s][] = $i;
        }

        $assigned = [];
        $groups = [];

        for ($i = 0; $i < $count; $i++) {

            if (isset($assigned[$i])) {
                continue;
            }

            $setP = [$items[$i]['p'] => true];
            $setS = [$items[$i]['s'] => true];
            $setT = [$items[$i]['t'] => true];

            /*
             * Perluas kelompok sampai tidak ada
             * nilai baru dari baris yang cocok.
             */
            do {
                $changed = false;

                foreach (array_keys($setS) as $s) {
                    foreach (array_keys($setT) as $t) {
                        foreach ($indexST[$s . '|' . $t] ?? [] as $j) {
                            if (!isset($setP[$items[$j]['p']])) {
                                $setP[$items[$j]['p']] = true;
                                $changed = true;
                            }
                        }
                    }
                }

                foreach (array_keys($setP) as $p) {
                    foreach (array_keys($setT) as $t) {
                        foreach ($indexPT[$p . '|' . $t] ?? [] as $j) {
                            if (!isset($setS[$items[$j]['s']])) {
                                $setS[$items[$j]['s']] = true;
                                $changed = true;
                            }
                        }
                    }
                }

                foreach (array_keys($setP) as $p) {
                    foreach (array_keys($setS) as $s) {
                        foreach ($indexPS[$p . '|' . $s] ?? [] as $j) {
                            if (!isset($setT[$items[$j]['t']])) {
                                $setT[$items[$j]['t']] = true;
                                $changed = true;
                            }
                        }
                    }
                }
            } while ($changed);

            $group = [];

            foreach ($items as $j => $item) {
                if (isset($assigned[$j])) {
                    continue;
                }

                if (
                    isset($setP[$item['p']])
                    && isset($setS[$item['s']])
                    && isset($setT[$item['t']])
                ) {
                    $group[] = $rows[$j];
                    $assigned[$j] = true;
                }
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Nilai penanda untuk null.
     */
    private static function key($value, string $prefix): string
    {
        if ($value === null || $value === '') {
            return $prefix . ':none';
        }

        return $prefix . ':' . $value;
    }

    /**
     * Normalisasi nilai tempat kegiatan.
     */
    private static function tempatKey($tempat): ?string
    {
        if ($tempat === null) {
            return null;
        }

        $tempat = trim((string) $tempat);

        return $tempat === '' ? null : $tempat;
    }
}
