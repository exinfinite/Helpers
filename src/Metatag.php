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
    public function __construct(Array $dft = []) {
        $this->setMetas(array_intersect_key($dft, $this->meta));
    }
    public function set($key, $val, $allow_empty = true) {
        if (
            array_key_exists($key, $this->meta) &&
            is_string($val)
        ) {
            if (!$allow_empty === true && trim($val) == '') {
                return;
            }
            $this->meta[$key] = $val;
        }
    }
    public function get($key) {
        return (
            is_string($key) &&
            array_key_exists($key, $this->meta)
        ) ? $this->meta[$key] : '';
    }
    public function setMetas(Array $datas) {
        foreach ($datas as $k => $v) {
            $this->set($k, $v);
        }
    }
    public function all() {
        return $this->meta;
    }
}
?>