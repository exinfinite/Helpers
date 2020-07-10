<?php
namespace Exinfinite\Helpers;
class Metatag {
    protected $meta = [
        'title' => '',
        'description' => '',
        'image' => '',
        'canonical' => '',
        'author' => '',
        'copyright' => '',
    ];
    function __construct(Array $dft = []) {
        $this->setMetas(array_intersect_key($dft, $this->meta));
    }
    function set($key, $val) {
        if (
            array_key_exists($key, $this->meta) &&
            is_string($val)
        ) {
            $this->meta[$key] = $val;
        }
    }
    function get($key) {
        return (
            is_string($key) &&
            array_key_exists($key, $this->meta)
        ) ? $this->meta[$key] : '';
    }
    function setMetas(Array $datas) {
        foreach ($datas as $k => $v) {
            $this->set($k, $v);
        }
    }
    function all() {
        return $this->meta;
    }
}
?>