<?php
namespace Exinfinite\Helpers;

class Jsonld {
    private $tpl = "<script type='application/ld+json'>%s</script>";
    private $json = [];
    function __construct($site_name, $site_url, $site_logo = '') {
        $this->site_name = $site_name;
        $this->site_url = $site_url;
        $this->site_logo = $site_logo;
        $this->website();
        $this->organization();
    }
    function website() {
        $this->json['website'] = [
            "@context" => "http://schema.org",
            "@type" => "WebSite",
            "name" => "{$this->site_name}",
            "url" => "{$this->site_url}",
        ];
    }
    function organization() {
        $this->json['organization'] = [
            "@context" => "http://schema.org",
            "@type" => "Organization",
            "url" => "{$this->site_url}",
            "logo" => "{$this->site_logo}",
        ];
    }
    function breadcrumb($item_list = []) {
        $json_key = 'breadcrumb';
        if (!array_key_exists($json_key, $this->json) || !is_array($this->json[$json_key])) {
            $this->json[$json_key] = [];
        }
        $idx = 1;
        $item_list_tmp = [];
        $list_item = function ($idx, $url, $name) use (&$item_list_tmp) {
            array_push($item_list_tmp, [
                "@type" => "ListItem",
                "position" => (int) $idx,
                "item" => [
                    "@id" => "{$url}",
                    "name" => "{$name}",
                ],
            ]);
        };
        $list_item($idx, $this->site_url, $this->site_name);
        foreach ($item_list as $url => $name) {
            $list_item(++$idx, $url, $name);
        }
        array_push($this->json[$json_key], [
            "@context" => "http://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $item_list_tmp,
        ]);
    }
    function search($uri, $param) {
        if (!is_string($param) || trim($param) == '') {
            return;
        }
        $this->json['search'] = [
            "@context" => "http://schema.org",
            "@type" => "WebSite",
            "url" => "{$this->site_url}",
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => implode('?', [$uri, "{$param}={{$param}}"]),
                "query-input" => "required name={$param}",
            ],
        ];
    }
    function render() {
        echo implode("\n",
            array_reduce($this->json, function ($container, $context) {
                array_push($container, sprintf($this->tpl, json_encode($context, JSON_UNESCAPED_UNICODE)));
                return $container;
            }, [])
        );
    }
}
