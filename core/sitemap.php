<?php

class Sitemap {

    protected $links = array();
    protected $loc = array();
    protected $simple = FALSE;

    public function setSimple($simple) {
        $this->simple = $simple;
    }

    public function addNode($loc, $changefreq = 'hourly', $lastmod = NULL) {

        if (in_array($loc, $this->loc)) {
            return $this;
        }

        $this->loc[] = $loc;

        $tmp = array(
            'loc' => $loc,
            'changefreq' => $changefreq,
            'xhtml:link' => array(),
            'image:image' => array(),
        );

        if ($lastmod && strtotime($lastmod) > 0) {
            $tmp['lastmod'] = date('c', strtotime($lastmod));
        }

        $this->links[] = $tmp;

        return $this;
    }

    public function addImage($loc, $caption, $geo) {

        if (in_array($loc, $this->loc)) {
            return $this;
        }

        $this->loc[] = $loc;

        $tmp = array(
            'image:loc' => $loc,
            'image:caption' => $caption,
            'image:geo_location' => $geo,
        );

        $this->links[count($this->links) - 1]['image:image'][] = $tmp;

        return $this;
    }

    public function addLang($href, $lang) {

        $tmp = array(
            'rel' => 'alternate',
            'hreflang' => $lang,
            'href' => $href,
        );

        $this->links[count($this->links) - 1]['xhtml:link'][] = $tmp;

        return $this;
    }

    public function getXml(Request $request) {

        $xml = new DOMDocument('1.0', 'utf-8');

        $urlset = $xml->createElement('urlset');

        foreach($this->links as $l) {

            $link = $xml->createElement('url');

            foreach($l as $attr => $val){

                if ($attr == 'xhtml:link') {

                    if (!$this->simple) {

                        foreach ($val as $langPack) {

                            $xLink = $xml->createElement('xhtml:link');

                            foreach ($langPack as $htmlAttr => $htmlValue) {

                                if (is_array($htmlValue) && $htmlAttr == 'href') {
                                    $htmlValue = call_user_func_array(array($request, 'url'), $htmlValue);
                                }

                                $xLink->setAttribute($htmlAttr, $htmlValue);
                            }

                            $link->appendChild($xLink);

                        }
                    }

                } elseif ($attr == 'image:image') {

                    if (!$this->simple) {

                        foreach ($val as $img) {

                            $e = $xml->createElement('image:image');

                            foreach ($img as $p => $v) {

                                if ($p == 'image:loc') {

                                    if (is_array($v)) {
                                        $v = call_user_func_array(array($request, 'url'), $v);
                                    }
                                }

                                $i = $xml->createElement($p, $v);
                                $e->appendChild($i);
                            }

                            $link->appendChild($e);
                        }
                    }

                } elseif ($attr == 'loc') {

                    if (is_array($val)) {
                        $val = call_user_func_array(array($request, 'url'), $val);
                    }

                    $e = $xml->createElement($attr,$val);
                    $link->appendChild($e);

                } else {

                    $e = $xml->createElement($attr,$val);
                    $link->appendChild($e);

                }
            }

            $urlset->appendChild($link);
        }

        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
        $urlset->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');

        $xml->appendChild($urlset);
        return $xml->saveXML();
    }
}