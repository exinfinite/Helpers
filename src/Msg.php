<?php
namespace Exinfinite\Helpers;
class Msg {
    private $msg = [
        'text' => '',
        'status' => 'error',
    ];
    function success($txt) {
        return array_merge($this->msg, [
            'text' => (string) $txt,
            'status' => 'success',
        ]);
    }
    function error($txt) {
        return array_merge($this->msg, [
            'text' => (string) $txt,
            'status' => 'error',
        ]);
    }
}
?>