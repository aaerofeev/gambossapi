<?php
require_once 'gameapi.php';
require_once 'game.php';

/**
 * Описание api
 * @see http://partner.gameboss.ru/faq_xml.html
 * @see Cache
 */
class GameBossApi implements GameApi {

    const CATALOG_NEVOSOFT = 1024;

    protected $partnerId;
    protected $api = 'http://gameboss.ru/x1.php';
    protected $apiDum = 'http://gameboss.ru/x2.php';

    protected $cache;
    public $cacheLifetime = 72000;

    public function __construct($partnerId) {

        $this->partnerId = $partnerId;
    }


    public function getGenres($idParent = NULL) {

        $genres = array(
            1 => 'Логические',
            2 => 'Аркадные',
            4 => 'Стрелялки',
            8 => 'Cимуляторы',
            16 => 'Настольные',
            32 => 'Детские',
            64 => 'Я ищу',
        );

        $genresSeo = array();

        foreach ($genres as $id => $genre) {

            $genresSeo[] = array(
                'id' => $id,
                'name' => $genre,
                'latin' => urlize($genre),
            );
        }

        return $genresSeo;
    }

    /**
     * @param int $limit
     * @param int $genre
     * @return Game[]
     */
    public function getNew($limit, $genre = 127) {

        $list = cacheGet("gameboss_new_{$genre}_{$limit}");

        if (!$list) {

            $list = $this->getDump(array(
                'partner' => $this->partnerId,
                'limit' => $limit,
                'order' => 'date',
                'encoding' => 'utf8',
                'genre' => $genre,
                'short' => 1,
            ));

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    /**
     * @param string $name
     * @return Game[]
     */
    public function getGame($name) {

        $item = cacheGet("gameboss_game_{$name}");

        if (!$item) {

            $gamePartner = $this->getApi(array(
                'type' => 'game_descr',
                'game' => $name,
                'partner' => $this->partnerId
            ));

            if (!$gamePartner) {
                return FALSE;
            }

            $item = $this->getDump(array(
                'partner' => $this->partnerId,
                'game' => $name,
                'short' => 1,
                'full' => 1,
                'image' => 1,
            ));

            if (!$item) {
                return FALSE;
            }

            $item[0]->links[0]['url'] = $gamePartner[0]->links[0]['url'];

            cacheSet($item);
        }

        return $item;
    }

    protected function getApi($arguments = array()) {

        $url = $this->api . '?' . http_build_query($arguments);
        $xml = simplexml_load_file($url);

        $games = array();

        foreach ($xml->result->entry as $item) {

            $attributes = $item->attributes();

            $game = new Game();
            $game->id = (int) $attributes->num;
            $game->latin = str_replace('_', '-', (string) $attributes->page);
            $game->created = (string) $attributes->flag_new;
            $game->lead = (string) urldecode($attributes->descr);
            $game->name = (string) urldecode($attributes->name);

            $game->links[] = array(
                'name' => humanFileSize((string) $item->attributes()->size * 1024),
                'url' => (string) $attributes->url
            );

            $game->thumb = Image::load((string) $attributes->ico);
            $game->picture = Image::load((string) $attributes->pic);

            $game->lead = mb_convert_encoding($game->lead, "utf-8", "windows-1251");
            $game->name = mb_convert_encoding($game->name, "utf-8", "windows-1251");

            $games[] = $game;
        }

        return $games;
    }

    protected function getDump($arguments = array()) {

        $url = $this->apiDum . '?' . http_build_query($arguments);
        $xml = simplexml_load_file($url);

        $games = array();

        foreach ($xml->result->ITEM as $item) {

            $game = new Game();
            $game->mask = (int) $item->TYPE;
            $game->id = (int) $item->ID;
            $game->rate = (int) $item->RATE;
            $game->latin = str_replace('_', '-', (string) $item->NAME_URL);
            $game->created = (string) $item->ADDED;
            $game->size = (int) $item->SIZE;
            $game->lead = (string) $item->SHORTDESCR;
            $game->name = (string) $item->NAME;

            $game->links[] = array(
                'name' => humanFileSize($item->SIZE * 1024),
                'url' => (string) $item->DOWNLOAD_LINK,
            );

            $game->thumb = Image::load((string) $item->SMALL_PIC);
            $game->picture = Image::load((string) $item->MEDIUM_PIC);

            $desc = (string) $item->FULLDESCR;

            if ($desc) {
                $game->desc = $desc;
            }

            $screen = $item->SCREENSHOT;

            if ($screen) {

                $game->screen = array();

                foreach ($screen as $photo) {
                    $game->screen[] = array(
                        'thumb' => Image::load((string) $photo->THUMBNAIL),
                        'picture' => Image::load((string) $photo->IMAGE),
                    );
                }
            }

            $games[] = $game;
        }

        return $games;
    }

    public function getIdentity($id) {

        return 'gb-' . $id;
    }
}

