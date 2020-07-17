<?php
namespace Exinfinite\Helpers\Render;
use function Exinfinite\Helpers\buildUrl;
use function Exinfinite\Helpers\startsWith;

class Dom extends \DOMDocument {
    function __construct($version = '1.0', $encoding = 'UTF-8') {
        parent::__construct($version, $encoding);
        $this->encoding = $encoding;
        $this->tidy = new \tidy;
    }
    function loadHTML($content, $options = 0) {
        libxml_use_internal_errors(true);
        parent::loadHTML(
            @mb_convert_encoding($this->tidy->repairString($content, [], 'utf8'), 'HTML-ENTITIES', $this->encoding),
            LIBXML_COMPACT
        );
        return $this;
    }
    function saveHtml() {
        return $this->tidy->repairString(
            @mb_convert_encoding(parent::saveHtml(), $this->encoding, 'HTML-ENTITIES'),
            ['show-body-only' => true],
            'utf8'
        );
    }
    function domByTagName($tag) {
        return $this->getElementsByTagName($tag);
    }
    function toLazy($nodes, $placeHolder = '', $base_url = '') {
        $lazyClass = 'lazy';
        foreach ($nodes as $node) {
            /* if (!in_array($lazyClass, explode(' ', $node->getAttribute('class')))) {
            continue;
            } */
            $newClass = array_unique(array_merge(explode(' ', $node->getAttribute('class')), [$lazyClass]));
            $oldsrc = $node->getAttribute('src');
            if (!startsWith($oldsrc, '//') && startsWith($oldsrc, '/')) {
                $oldsrc = buildUrl([$base_url, $oldsrc]);
            }
            $node->setAttribute("data-src", $oldsrc);
            $node->setAttribute("src", $placeHolder);
            $node->setAttribute("class", implode(' ', $newClass));
        }
    }
}
?>