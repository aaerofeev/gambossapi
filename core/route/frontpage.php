<?php

class FrontPage extends RouteCallback {

    /**
     * @var NativeApi
     */
    public $api;

    /**
     * @var Database
     */
    public $database;

    /**
     * @var int
     */
    public $type = 2;

    /**
     * @var int
     */
    protected $genre;

    /**
     * @var int
     */
    protected $brand;

    public function start() {

        parent::start();

        // Database
        $this->database = new Database(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);

        // API
        $this->api = new NativeApi($this->database);

        // Layout
        $this->layout = new Template('template/index');
        $this->layout->setRequest($this->request);
        $this->layout->content = NULL;
        $this->layout->title = array();

        $this->layout->description = 'Скачать игры
        для пк и мобильные приложения бесплатно и без регистрации по прямой ссылке.
        Онлайн и браузерные игры. Лучшие и свежие игры индустрии для мобильного телефона.
        Лучшие игры 2015 года.';

        $this->layout->ogTitle = '';
        $this->layout->ogType = 'website';
        $this->layout->ogImage = '';
        $this->layout->ogUrl = '';
        $this->layout->ogDescription = '';
        $this->layout->ogSitename = 'Каталог игр и приложений jezzy';

        $pcUrl = $this->request->url('type', 'pc');
        $onlineUrl = $this->request->url('type', 'browser');

        $this->layout->headText = "На нашем сайты вы можете скачать новые игры для
            <a href='{$pcUrl}'>компьютера</a>,<br/>так же <a href='{$onlineUrl}'>онлайн игры</a>
            бесплатно и без регистрации.";

        $this->layout->title[] = 'Скачать игры бесплатно и без регистрации';

        $this->layout->needJavascript = false;

        // Type parsing
        $typeLatin = $this->request->getParam('type');

        if ($typeLatin) {
            $this->type = $this->api->getGenreMask($typeLatin);
        }

        // Genre parsing
        $genreLatin = $this->request->getParam('genre');

        if ($genreLatin) {
            $this->genre = $this->api->getGenreMask($genreLatin);
        }

        // Brand parsing
        $brandLatin = $this->request->getParam('brand');

        if ($brandLatin) {
            $this->brand = $this->api->getGenreMask('partner-' . $brandLatin, 1);
        }

        // Layout values before
        $this->layout->genres = $this->api->getGenres($this->type);
        $this->layout->genresAll = $this->api->getGenresAll(0);
        $this->layout->menuGenres = $this->api->getGenres();

        $this->layout->topGames = $this->api->getTop(12, $this->genre | $this->type);
        $this->layout->newGames = $this->api->getNew(6, $this->type);
        $this->layout->topGamesAll = $this->api->getTop(6, $this->type);

        $this->layout->brands = $this->api->getBrands($this->type);

        $type = $this->api->getGenreById($this->type);
        $this->layout->typeLatin = $type['latin'];

        if ($this->brand) {
            $brand = $this->api->getGenreById($this->brand);
            $brand['latin'] = str_replace('partner-', '', $brand['latin']);
            $this->layout->brandLatin = $brand['latin'];
            $this->layout->brand = $brand;
            $this->layout->brandUrl = $this->request->url('brand', $this->layout->typeLatin, $brand['latin']);;
        } else {
            $this->layout->brandLatin = NULL;
        }

        $this->layout->queryWord = $this->request->getParam('query');

        $typeFormSearch = $this->api->getGenreById($this->type);
        $this->layout->searchFormUrl = $this->request->url('type', $typeFormSearch['latin']);

        $pages = $this->database->select('content', NULL, NULL, array('id'=>'DESC'), NULL, 10);
        $this->layout->pages = $pages;
    }

    public function main() {

        $content = new Template('template/gameListView');
        $content->setRequest($this->request);
        $content->list = $this->api->getRecommendation(40);
        $content->title = 'Мы рекомендуем';

        $content->typeLatin = $this->layout->typeLatin;
        $content->brandLatin = $this->layout->brandLatin;

        $this->layout->content = $content->render();

        if ($this->layout->brands) {
            $brandTitle = array();

            foreach ($this->layout->brands as $brand) {

                $brandTitle[] = $brand['name'];
            }

            $this->layout->title[] = 'Играть в игры от ' . implode(', ', $brandTitle);
        }

        $this->layout->title[] = 'Каталог игр и приложений jezzy';
    }

    public function genre() {

        $content = new Template('template/gameListView');
        $content->setRequest($this->request);

        $mask = 0;

        if ($this->type) {
            $mask |= $this->type;
        }

        if ($this->genre) {
            $mask |= $this->genre;
        }

        if ($this->brand) {
            $mask |= $this->brand;
        }

        $pageText = '';

        $page = $this->request->getParam('page');

        $content->title = array();

        $query = $this->request->getParam('query');

        if ($query) {

            $query = strip_tags($query);
            $query = htmlspecialchars($query);
            $content->title[] = "Поиск «{$query}»";
        }

        if ($this->request->getParam('brand')) {

            $genre = $this->api->getGenreById($this->brand);
            $content->title[] = 'Игры от издателя ' . $genre['name'];

            $pageText .= "Скачать все игры от издателя {$genre['name']} бесплатно и без регистрации. Играть онлайн в браузере.";

            $this->layout->ogImage = 'brand/' . $this->layout->brand['latin'] . '.png';

            $genreUrl = $this->layout->brandUrl;
            $this->layout->headText = "Скачать все игры от издателя <a href='{$genreUrl}'>{$genre['name']}</a>
            бесплатно и без регистрации.<br/>На нашем сайт вы можете играть в игры от {$genre['name']} онлайн в браузере.";
        }

        if ($this->request->getParam('genre')) {

            $genre = $this->api->getGenreById($this->genre);

            if (!$genre['id_parent']) {
                $content->title[] = $genre['name'];
            } else {
                $content->title[] = 'Игры по жанру ' . mb_strtolower($genre['name'], 'utf8');
            }

            $pageText .= "Скачать игры в жанре {$genre['name']} бесплатно и без регистрации. Играть онлайн в браузере.";

            if (!$this->brand) {

                $genreUrl = $this->request->url('genre', $this->layout->typeLatin, $genre['latin']);
                $genreName = mb_strtolower($genre['name'], 'utf8');
                $this->layout->headText = "Скачать все игры в жанре <a href='{$genreUrl}'>{$genreName}</a>
                бесплатно и без регистрации.<br/>На нашем сайт вы можете играть в {$genreName} онлайн в браузере.";
            }

        } elseif ($this->request->getParam('type') && !$this->brand) {

            $genre = $this->api->getGenreById($this->type);
            $content->title[] = $genre['name'];

            $pageText .= $genre['name'] . ' бесплатно и без регистрации. ';
        }

        $content->title = implode('. ', $content->title);
        $content->typeLatin = $this->layout->typeLatin;
        $content->brandLatin = $this->layout->brandLatin;

        $items = $this->api->getRecommendationGenre($mask, 20, $page, $query);
        $content->list = $items['list'];
        $content->pages = $items['pages'];
        $content->page = $items['page'];

        $this->layout->content = $content->render();
        $this->layout->title[] = $content->title;
        $this->layout->description = $pageText;
        $this->layout->searchFormUrl = $this->request->baseUrl($_SERVER['REQUEST_URI']);
    }

    public function sitemap() {

        $route = $this->request->getRoute();

        cacheEnable(true);

        if (($xmlCode = cacheGet($route)) == FALSE) {

            cacheEnable(false);// row data

            $sitemap = new Sitemap();
            $sitemap->setSimple($route == 'sitemapLow');

            $sitemap->addNode(array('main'));

            foreach ($this->layout->menuGenres as $type) {

                $sitemap->addNode(array('type', $type['latin']));

                foreach ($this->api->getBrands($type['id']) as $brand) {

                    $sitemap->addNode(array('brand', $type['latin'], $brand['latin']));

                    foreach ($this->api->getGenres($type['id']) as $genre) {

                        $sitemap->addNode(array('brandGenre', $type['latin'], $brand['latin'], $genre['latin']));
                    }
                }

                foreach ($this->api->getGenres($type['id']) as $genre) {

                    $sitemap->addNode(array('genre', $type['latin'], $genre['latin']));
                }
            }

            foreach ($this->api->getNew(PHP_INT_MAX) as $game) {

                $sitemap->addNode(array('game', $game->latin), 'hourly', $game->created);

                foreach ($game->screen as $screen) {

                    $sitemap->addImage($this->request->baseUrl($screen['picture']), $game->name, NULL);
                }
            }

            $pages = $this->database->select('content', NULL, NULL, array('id'=>'DESC'));

            foreach ($pages as $page) {

                $sitemap->addNode(array('contentView', $page->latin), 'hourly', $page->created);
                $sitemap->addImage($this->request->baseUrl($page->image), $page->caption, NULL);
            }

            $xmlCode = $sitemap->getXml($this->request);

            cacheEnable(true);
            cacheSet($xmlCode, $route, 60 * 15);// 15 min
        }

        $this->request->send('Content-type: text/xml;charset=utf8');
        echo $xmlCode;
        exit(0);
    }

    public function robots() {

        $sitemapUrl = $this->request->url('sitemap');
        $sitemapSimpleUrl = $this->request->url('sitemapLow');

        $this->request->send('Content-type: text/plain;charset=utf8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Sitemap: {$sitemapSimpleUrl}\n";
        echo "User-agent: Yandex\n";
        echo "Sitemap: {$sitemapSimpleUrl}\n";
        echo "User-agent: Googlebot\n";
        echo "Sitemap: {$sitemapUrl}\n";
        exit(0);
    }

    public function game($paramLink) {

        $game = $this->api->getGame($paramLink);

        if (!$game) {
            throw new Exception('Game not found', 404);
        }

        $game = array_pop($game);

        $content = new Template('template/game');
        $content->setRequest($this->request);
        $content->game = $game;
        $content->related = $this->api->getRelated($game);
        $content->typeLatin = $this->layout->typeLatin;
        $content->brandLatin = $this->layout->brandLatin;

        $gameText = '';
        $headText = $game->lead . ' ';

        if ($game->links) {
            $gameText .= "Скачать полную версию игры «{$game->name}» бесплатно и без регистрации.
            Загрузка будет произведена по прямой ссылке.
            Размер загружаемого файла {$game->links[0]['name']}. ";

            $gameUrl = $this->request->url('game', array($game->latin), array('type' => $content->typeLatin, 'brand' => $content->brandLatin));
            $headText .= "Скачай игру <a href='{$gameUrl}'>«{$game->name}»</a> бесплатно и без регистрации. ";
        }

        if ($game->embed) {
            $gameText .= "Играть онлайн в браузере «{$game->name}» вы сможете нажав на кнопку
            «Играть сейчас». ";
        }

        $this->layout->headText = $headText;

        $content->gameText = $gameText;

        $this->layout->description = $gameText . $game->lead;

        $this->layout->ogImage = array($game->picture);

        foreach ($game->screen as $image) {
            $this->layout->ogImage[] = $image['picture'];
        }

        $this->layout->ogUrl = $this->request->url('game', $game->latin);

        $this->layout->content = $content->render();
        $this->layout->title[] = $game->lead;
        $this->layout->title[] = $game->name;
        $this->layout->needJavascript = true;
    }

    public function finish() {

        foreach ($this->layout->title as &$title) {
            $title = trim($title, '.');
            $title = trim($title, "\t");
            $title = trim($title, ' ');
            $title = trim($title, "\n");
        }

        $this->layout->title = implode(' / ', array_reverse($this->layout->title));
        $this->layout->description = str_replace(array("\t", "\n"), '', strip_tags($this->layout->description));

        if (!$this->layout->ogTitle) {
            $this->layout->ogTitle = $this->layout->title;
        }

        if (!$this->layout->ogDescription) {
            $this->layout->ogDescription = $this->layout->description;
        }

        if (!$this->layout->ogUrl) {
            $this->layout->ogUrl = $this->request->serverUrl();
        }

        parent::finish();
    }

    public function content($link) {

        $page = $this->database->select('content', array('latin' => $link), NULL, NULL, NULL, 1);
        $page->body = $this->renderBody($page->body);

        $this->layout->ogImage = $page->image;
        $this->layout->description = $page->lead;
        $this->layout->title[] = $page->caption;
        $this->layout->ogType = 'article';

        $content = new Template('template/content');
        $content->setRequest($this->request);

        $content->page = $page;
        $this->layout->content = $content->render();
        $this->layout->needJavascript = true;
    }

    public function parseGames($template, &$links = NULL, $bbCode = true) {

        $games = array();

        if ($bbCode) {
            $pattern = '#\[GAME\]http:\/\/.+?\/game\/(.+?)\.html.+?\[\/GAME\]#isu';
        } else {
            $pattern = '#http:\/\/.+?\/game\/(.+?)\.html#isu';
        }

        if (preg_match_all($pattern, $template, $links, PREG_SET_ORDER)) {

            foreach ($links as $link) {

                $game = $this->api->getGame($link[1]);

                if (isset($game[0])) {

                    $games[] = $game[0];
                }
            }
        }

        return $games;
    }

    public function renderBody($text) {

        if (preg_match_all('#\[COLLECTION=(?<name>.+?)\](?<games>.+?)\[\/COLLECTION\]#isu', $text, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $collection) {

                $collectionTemplate = new Template('template/service/gameCollection');
                $collectionTemplate->setRequest($this->request);
                $template = $collectionTemplate->render(array(
                    'name' => $collection['name'],
                    'list' => $this->parseGames($collection['games'], $linkText, false)
                ));

                $text = str_replace($collection[0], $template, $text);

            }
        }

        if (preg_match_all('#\[RATING=(?<name>.+?)\](?<games>.+?)\[\/RATING\]#isu', $text, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $collection) {

                $collectionTemplate = new Template('template/service/gameRating');
                $collectionTemplate->setRequest($this->request);
                $template = $collectionTemplate->render(array(
                    'name' => $collection['name'],
                    'list' => $this->parseGames($collection['games'], $linkText, false)
                ));

                $text = str_replace($collection[0], $template, $text);

            }
        }

        $games = $this->parseGames($text, $links);

        foreach ($links as $k => $game) {

            $collectionTemplate = new Template('template/service/gameView');
            $collectionTemplate->setRequest($this->request);
            $template = $collectionTemplate->render(array(
                'game' => $games[$k]
            ));

            $text = str_replace($game[0], $template, $text);
        }

        return $text;
    }
}