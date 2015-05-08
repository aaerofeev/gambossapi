<?php

class AlawarApi implements GameApi {

    const CATALOG_ALAWAR = 2048;

    const PARENT_CASUAL = 2;
    const PARENT_ONLINE = 512;
    const PARENT_ANDROID = -3;

    protected $partnerId;
    protected $api = 'http://export.alawar.ru/games_agsn_xml.php';

    public function __construct($partnerId) {

        $this->partnerId = $partnerId;
    }

    public function getGenres($idParent = NULL) {

        $genres = $this->getGenresAll();
        $genresFiltered = array();

        if ($idParent) {
            foreach ($genres as $genre) {

                if ($genre['parent'] == $idParent) {
                    $genresFiltered[] = $genre;
                }
            }
        } else {
            $genresFiltered = $genres;
        }

        return $genresFiltered;
    }

    public function getGenresAll() {

        return array(
            array('id' => 8192, 'name' => 'Шарики', 'latin' => 'tetris-lines', 'parent' => static::PARENT_CASUAL),
            array('id' => 4, 'name' => 'Поиск предметов', 'latin' => 'logic-puzzle', 'parent' => static::PARENT_CASUAL),
            array('id' => 64, 'name' => 'Настольные', 'latin' => 'action-puzzle', 'parent' => static::PARENT_CASUAL),
            array('id' => 8, 'name' => 'Бизнес', 'latin' => 'arcade', 'parent' => static::PARENT_CASUAL),
            array('id' => 16, 'name' => 'Стрелялки', 'latin' => 'shooter', 'parent' => static::PARENT_CASUAL),
            array('id' => 4096, 'name' => 'Бегалки', 'latin' => 'arkanoid', 'parent' => static::PARENT_CASUAL),
            array('id' => static::PARENT_ONLINE, 'name' => 'Браузерные игры', 'latin' => 'browser', 'parent' => static::PARENT_ONLINE),
            //array('id' => '', 'name' => '', 'latin' => '', 'parent' => static::PARENT_CASUAL),
        );
    }

    public function getNew($limit, $genre = NULL) {

        return $this->getDump(array(
            'pid' => $this->partnerId,
            'lang' => 'ru',
            'dateActiveFrom' => date('Y-m-d', strtotime('1 month ago')),
        ));
    }

    public function getDump($arguments) {

        $url = $this->api . '?' . http_build_query($arguments);
        //$url = '/root/alawar.xml';
        $xmlContent = file_get_contents($url);
        $xml = simplexml_load_string($xmlContent);

        $root = $xml->Languages->Language[0];

        // Collector
        $games = array();

        // Casual PC games
        $casualPcGames = $root->Catalogs->Catalog[0];
        $genresCatalog = array();
        $gamesRating = array();

        foreach ($casualPcGames->Dictionaries->Dictionary as $dictionary) {

            if ($dictionary->attributes()->Code == 'Genre') {

                foreach ($dictionary->DictionaryItem as $genre) {

                    foreach ($genre->Elements->Element as $element) {

                        $genresCatalog[(string) $element->attributes()->ID] = (string) $genre->attributes()->Code;
                    }
                }
            } elseif ($dictionary->attributes()->Code == 'Rating') {

                foreach ($dictionary->DictionaryItem->Elements->Element as $rating) {

                    $gamesRating[(string) $rating->attributes()->ID] = (string) $rating->attributes()->Value;
                }
            }
        }

        foreach ($casualPcGames->Items->Item as $item) {

            $id = (string) $item->attributes()->ID;

            $game = new Game();
            $game->id = $id;
            $game->name = (string) $item->Name;

            foreach ($item->Properties->Property as $property) {

                $code = $property->attributes()->Code;
                $value = (string) $property;

                if ($code == 'SymbolCode') {

                    $game->latin = $value;
                } elseif ($code == 'ReleaseDate') {

                    $game->created = $value;
                } elseif ($code == 'Description80') {

                    $game->lead = $value;
                } elseif ($code == 'Description2000') {

                    $game->desc = strip_tags($value);
                }
            }

            $linkXml = $item->Files->File[0];
            $size = (string) $linkXml->attributes()->Size;
            $link = (string) $linkXml;

            $game->links[] = array(
                'name' => str_replace(array('Mb', 'Kb', 'Gb'), array('Мб', 'Кб', 'Гб'), $size),
                'url' => $link,
            );

            foreach ($item->Images->Image as $image) {

                $sizeName = (string) $image->attributes()->Type;

                if ($sizeName == 'logo190x140') {

                    $game->picture = Image::load((string) $image);
                } elseif ($sizeName == 'icon44x44') {

                    $game->thumb = Image::load((string) $image);
                }
            }

            foreach ($item->Screenshots->Screenshot as $screen) {

                $i = (string) $screen->attributes()->ID;
                $type = (string) $screen->attributes()->Type;

                if (!isset($game->screen[$i])) {

                    $game->screen[$i] = array();
                }

                if ($type == 'small') {
                    $game->screen[$i]['thumb'] = Image::load((string) $screen);
                } else {
                    $game->screen[$i]['picture'] = Image::load((string) $screen);
                }
            }

            if (isset($genresCatalog[$game->id])) {

                $game->mask = $genresCatalog[$game->id];
            }

            if (isset($gamesRating[$game->id])) {

                $game->rate = $gamesRating[$game->id];
            }

            $games[] = $game;
        }

        // Browser games
        $onlineGames = $root->Catalogs->Catalog[1];
        $gamesRating = array();

        foreach ($onlineGames->Dictionaries->Dictionary as $dictionary) {

            if ($dictionary->attributes()->Code == 'Rating') {

                foreach ($dictionary->DictionaryItem->Elements->Element as $rating) {

                    $gamesRating[(string) $rating->attributes()->ID] = (string) $rating->attributes()->Value;
                }
            }
        }

        foreach ($onlineGames->Items->Item as $item) {

            $id = (string) $item->attributes()->ID;

            $game = new Game();
            $game->id = $id;
            $game->name = (string) $item->Name;

            foreach ($item->Properties->Property as $property) {

                $code = $property->attributes()->Code;
                $value = (string) $property;

                if ($code == 'SymbolCode') {

                    $game->latin = $value;
                } elseif ($code == 'Description80') {

                    $game->lead = $value;
                } elseif ($code == 'Description2000') {

                    $game->desc = strip_tags($value);
                } elseif ($code == 'Embed') {

                    $game->embed = $value;
                }
            }

            $linkXml = $item->Files->File[0];
            $size = (string) $linkXml->attributes()->Size;
            $link = (string) $linkXml;

            $game->created = date('Y-m-d H:i:s', (string) $linkXml->attributes()->Timestamp);
            $game->mask = static::PARENT_ONLINE;

            $game->links[] = array(
                'name' => str_replace(array('Mb', 'Kb', 'Gb'), array('Мб', 'Кб', 'Гб'), $size),
                'url' => $link,
            );

            foreach ($item->Images->Image as $image) {

                $sizeName = (string) $image->attributes()->Type;

                if ($sizeName == 'logo190x140') {

                    $game->picture = Image::load((string) $image);
                } elseif ($sizeName == 'icon44x44') {

                    $game->thumb = Image::load((string) $image);
                }
            }

            foreach ($item->RelatedItems->RelatedItemCatalog as $relatedCatalog) {

                foreach ($relatedCatalog->RelatedItem as $related) {

                    $game->related[] = (string) $related->attributes()->ID;
                }
            }

            if (isset($gamesRating[$game->id])) {

                $game->rate = $gamesRating[$game->id];
            }

            $games[] = $game;
        }

        return $games;

        //$androidGames = $root->Catalogs->Catalog[2];
    }

    public function getIdentity($id) {

        return 'alw-' . $id;
    }

    public function getGame($name) {

        return array();
    }
}