<?php
namespace Exinfinite\Helpers\Render;
use function Exinfinite\Helpers\buildUrl;
use function Exinfinite\Helpers\startsWith;

class Dom extends \DOMDocument {
    function __construct($version = '1.0', $encoding = 'UTF-8') {
        parent::__construct($version, $encoding);
        $this->encoding = $encoding;
        $this->tidy = new \tidy;
        $this->parseRules = [
            'clean' => TRUE,
            'doctype' => 'omit',
            'show-body-only' => true,
            'indent' => 2,
            'output-html' => TRUE,
            'tidy-mark' => FALSE,
            'wrap' => 0,
            'new-blocklevel-tags' => 'article aside audio bdi canvas details dialog figcaption figure footer header hgroup main menu menuitem nav section source summary template track video',
            'new-empty-tags' => 'command embed keygen source track wbr',
            'new-inline-tags' => 'audio command datalist embed keygen mark menuitem meter output progress source time video wbr',
        ];
    }
    function loadHTML($content, $options = 0) {
        libxml_use_internal_errors(true);
        parent::loadHTML(
            @mb_convert_encoding(
                tidy_parse_string($content, $this->parseRules, 'utf8'),
                'HTML-ENTITIES',
                $this->encoding
            ),
            LIBXML_COMPACT
        );
        return $this;
    }
    function saveHtml() {
        return parent::saveHtml();
    }
    function domByTagName($tag) {
        return $this->getElementsByTagName($tag);
    }
    function toLazy($nodes, $placeHolder = null, $base_url = '') {
        $lazyClass = 'lazy';
        foreach ($nodes as $node) {
            $newClass = array_unique(array_merge(explode(' ', $node->getAttribute('class')), [$lazyClass]));
            $oldsrc = $node->getAttribute('src');
            if (!startsWith($oldsrc, '//') && startsWith($oldsrc, '/')) {
                $oldsrc = buildUrl([$base_url, $oldsrc]);
            }
            $node->setAttribute("data-src", $oldsrc);
            if (is_null($placeHolder)) {
                $node->removeAttributeNode($node->attributes->getNamedItem('src'));
            } else {
                $node->setAttribute("src", $placeHolder);
            }
            $node->setAttribute("class", implode(' ', $newClass));
        }
    }
}
?>