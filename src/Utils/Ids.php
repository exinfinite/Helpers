<?php
namespace Exinfinite\Helpers\Utils;
use \Illuminate\Support\Collection;
use \Ramsey\Uuid\Uuid as uuid_gen;

class Ids {
    static function uuid() {
        $uuid = uuid_gen::uuid1();
        return $uuid->toString();
    }
    static function uid($len = 10, $prefix = '', $posifix = '') {
        $len_min = 4;
        $seeds = str_split('23456789ABCDEFGHJKLMNPQRSTUVWXYZ');
        shuffle($seeds);
        $limit = count($seeds) - 1;
        $prefix = is_string($prefix) ? $prefix : '';
        $posifix = is_string($posifix) ? $posifix : '';
        return Collection::times(
            max($len_min, (int) $len),
            function ($n) use ($seeds, $limit) {
                return $seeds[mt_rand(0, $limit)];
            }
        )->pipe(
            function ($r) use ($prefix, $posifix) {
                return implode('', [$prefix, $r->implode(''), $posifix]);
            }
        );
    }
}
?>