<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo $this->baseUrl('image/favicon.png') ?>" />
    <link href="<?php echo $this->baseUrl('bootstrap/css/bootstrap.min.css') ?>" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->baseUrl('lightbox/ekko-lightbox.min.css') ?>" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->baseUrl('style/main.css') ?>" media="screen" rel="stylesheet" type="text/css" />

    <?php if ($this->needJavascript): ?>
    <script type="text/javascript" src="<?php echo $this->baseUrl('jquery/jquery-1.11.2.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl('bootstrap/js/bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl('lightbox/ekko-lightbox.min.js') ?>"></script>
    <?php endif ?>

    <title><?php echo $this->title ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($this->description) ?>">
    <meta name="keywords" content="скачать, игры, свежие, новые, бесплатно, без регистрации, Россия, Казахстан, игровой, 2014, 2015, 2013">

    <meta property="og:title" content="<?php echo htmlspecialchars($this->ogTitle) ?>" />
    <meta property="og:locale" content="ru_RU" />
    <?php if ($this->ogType): ?>
    <meta property="og:type" content="<?php echo $this->ogType ?>" />
    <?php endif ?>
    <?php if ($this->ogDescription): ?>
        <meta property="og:description" content="<?php echo htmlspecialchars($this->ogDescription) ?>" />
    <?php endif ?>
    <meta property="og:url" content="<?php echo $this->ogUrl ?>" />
    <?php if ($this->ogImage): ?>
        <?php if (is_array($this->ogImage)):
            foreach ($this->ogImage as $image): ?>
            <meta property="og:image" content="<?php echo $this->baseUrl($image) ?>" />
        <?php endforeach; ?>
        <?php else: ?>
            <meta property="og:image" content="<?php echo $this->baseUrl($this->ogImage) ?>" />
        <?php endif ?>
    <?php endif ?>
    <?php if ($this->ogSitename): ?>
    <meta property="og:site_name " content="<?php echo $this->ogSitename ?>" />
    <?php endif ?>

    <link rel="search" type="application/opensearchdescription+xml" title="Поиск игр"
          href="<?php echo $this->baseUrl('search.xml') ?>">

    <meta name='wmail-verification' content='0c0f97dd449cdf371fce8dad3a37623e' />
    <meta name='yandex-verification' content='7244a0ccfcf0117f' />

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-19013059-15', 'auto');
        ga('send', 'pageview');

    </script>
</head>
<body>

<div class="container decorated">

    <header>

        <div class="row">
            <div class="col-md-2 col-lg-2 col-sm-4 col-xs-4">
                <a href="<?php echo $this->url('main') ?>" class="logotype">
                    <img src="<?php echo $this->baseUrl('image/logo.png') ?>" alt="Игровой каталог" class="img-responsive"/>
                </a>
            </div>

            <div class="col-md-6 col-lg-5 hidden-xs">
                <div class="main-text">
                    <span><?php echo $this->headText ?></span>
                </div>
            </div>

            <div class="col-md-3 col-lg-2 col-sm-4 hidden-xs">
                <div class="form-search-top">
                    <form method="GET" action="<?php echo $this->searchFormUrl ?>">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm"
                                   placeholder="Поиск игры" name="query"
                                   value="<?php echo htmlspecialchars($this->queryWord) ?>"/>
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-success btn-sm">Найти</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-1 col-lg-3 col-sm-4 col-xs-8">
                <div class="social-box text-right">
                    <a href="https://vk.com/jezzygame" target="_blank">
                        <img src="<?php echo $this->baseUrl('image/vk.png') ?>" alt="Мы вконтакте, интересные игры, весело!"/>
                    </a>
                    <a href="http://ok.ru/group/52407238328525" target="_blank">
                        <img src="<?php echo $this->baseUrl('image/ok.png') ?>" alt="Мы в одноклассниках, свежие игры, развлекайся!"/>
                    </a>
                </div>
            </div>
        </div>

    </header>

    <nav>
        <ul>
            <li><a href="<?php echo $this->url('main') ?>">Главная</a></li>

            <?php foreach ($this->menuGenres as $menu): ?>
            <li><a href="<?php echo $this->url('type', $menu['latin']) ?>"><?php echo $menu['name'] ?></a></li>
            <?php endforeach ?>
        </ul>
        <span class="nav-text hidden-xs hidden-sm hidden-md">
            Новинки игровой индустрии и популярные игры для мобильного телефона вы найдете на
            <a href="<?php echo $this->url('main') ?>">jezzy.ru</a>
        </span>
        <span class="nav-text hidden-xs hidden-sm hidden-lg">
            Новинки игровой индустрии и популярные игры для мобильного телефона
        </span>
    </nav>

</div>

<div class="container decorated layout">

    <div class="row">

        <?php if (count($this->topGamesAll)): ?>
            <div class="top-line row hidden-xs">
                <?php foreach ($this->topGamesAll as $k => $item): ?>
                    <div class="col-md-4 col-sm-6 col-xs-6 col-lg-3
                <?php if ($k > 3): ?> hidden-lg<?php endif ?>
                <?php if ($k > 2): ?> hidden-md<?php endif ?>
                <?php if ($k > 1): ?> hidden-xs hidden-sm<?php endif ?>">
                        <div class="box-preview"
                             title="<?php echo $item->lead ?>">
                            <a href="<?php echo $this->url('game', $item->latin) ?>" class="image">
                                <img src="<?php echo $this->baseUrl($item->picture) ?>" alt="<?php echo $item->name ?>"/>
                            </a>
                            <div class="caption">
                                <a href="<?php echo $this->url('game', $item->latin) ?>" class="name">
                            <span title="<?php echo htmlspecialchars($item->name) ?>">
                                <?php echo cutTextChar($item->name, 50) ?>
                            </span><br/>
                                    <span class="subname"><?php echo $item->getGenreText($this->genresAll) ?></span>
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="clearfix"></div>


        <div class="col-md-3 col-sm-5 flow-left col-xs-12">

            <?php if ($this->brandLatin): ?>
            <div class="brand-view title-view row">
                <div class="col-xs-5">
                    <a href="<?php echo $this->brandUrl ?>">
                        <img src="<?php echo $this->baseUrl('brand/' . $this->brand['latin'] . '.png') ?>"
                             alt="<?php echo $this->brand['name'] ?>" class="img-responsive"/>
                    </a>
                </div>
                <div class="col-xs-7 brand-text">
                    Новые игры от издателя <a href="<?php echo $this->brandUrl ?>"><?php echo $this->brand['name'] ?></a>
                    бесплатно и без регистрации.
                </div>
            </div>
            <?php endif ?>

            <?php if (count($this->genres)): ?>
            <div class="tile-view-caption">Выбор игры по жанру</div>
            <div class="game-catalog row tile-view">
                <ul class="col-md-12 col-lg-12">
                    <?php foreach ($this->genres as $genre): ?>
                    <li>
                        <?php if ($this->brandLatin):
                            $urlGenre = $this->url('brandGenre', $this->typeLatin, $this->brandLatin, $genre['latin']);
                        else:
                            $urlGenre = $this->url('genre', $this->typeLatin, $genre['latin']);
                        endif ?>
                        <a href="<?php echo $urlGenre ?>">
                            <?php echo $genre['name'] ?>
                        </a>
                    </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <?php endif ?>

            <div class="visible-xs">
                <div class="tile-view-caption">Поиск игры</div>
                <div class="form-search row tile-view">
                    <form method="GET" action="<?php echo $this->searchFormUrl ?>">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm"
                                   placeholder="Введите название" name="query"
                                   value="<?php echo htmlspecialchars($this->queryWord) ?>"/>
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-success btn-sm">Найти</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (count($this->brands)): ?>
                <div class="tile-view-caption">Издательства</div>
                <div class="game-brands row tile-view">
                    <?php foreach ($this->brands as $brand): ?>
                    <div class="col-xs-6">
                        <a href="<?php echo $this->url('brand', $this->typeLatin, $brand['latin']) ?>">
                            <img src="<?php echo $this->baseUrl('brand/' . $brand['latin'] . '.png') ?>"
                                 alt="<?php echo $brand['name'] ?>" class="img-responsive"/>
                        </a>
                    </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <div class="hidden-xs">
                <!-- новые для больших устройств -->
                <?php if (count($this->newGames)): ?>
                <div class="tile-view-caption">Новые игры</div>
                <div class="game-collection row tile-view clearfix">
                    <?php foreach ($this->newGames as $k => $game): ?>
                        <div class="col-md-12 col-lg-6 col-sm-6 col-xs-6
                        <?php if ($k > 2): ?> hidden-md<?php endif ?>
                        <?php if ($k > 3): ?> hidden-sm hidden-xs<?php endif ?>">
                            <div class="box-preview" title="<?php echo $game->lead ?>">
                                <a href="<?php echo $this->url('game', $game->latin) ?>">
                                    <img src="<?php echo $this->baseUrl($game->picture) ?>"
                                         alt="<?php echo $game->name ?>" class="img-responsive"/>
                                </a>
                                <div class="box text-center">
                                    <a href="<?php echo $this->url('game', $game->latin) ?>">
                                        <?php echo $game->name ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php if (($k + 1) % 2 == 0): ?>
                            <div class="clearfix"></div>
                        <?php endif ?>

                    <?php endforeach ?>
                </div>
                <?php else: ?>
                <div class="tile-view-caption">Новые игры</div>
                <div class="game-collection row tile-view clearfix text-center">
                    Пока нет новых игр
                </div>
                <?php endif ?>
            </div>

            <div class="hidden-xs">
                <!-- популярные для больших устройств -->
                <?php if (count($this->topGames)): ?>
                <div class="tile-view-caption">Популярные игры</div>
                <div class="inline-list row tile-view clearfix">
                    <?php foreach ($this->topGames as $k => $game): ?>
                        <div class="item-inline clearfix <?php if ($k > 5):?>hidden-md<?php endif ?>"
                             title="<?php echo $game->lead ?>">
                            <a href="<?php echo $this->url('game', $game->latin) ?>"
                               title="<?php echo htmlspecialchars($game->lead) ?>">
                                <?php echo $game->name ?>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php endif ?>
            </div>

            <!-- TODO позиция для баннера -->

            <div class="hidden-xs">
                <!-- популярные для больших устройств -->
                <?php if (count($this->pages)): ?>
                    <div class="inline-list row tile-view clearfix">
                        <?php foreach ($this->pages as $k => $page): ?>
                            <div class="item-inline clearfix <?php if ($k > 5):?>hidden-md<?php endif ?>"
                                 title="<?php echo $page->caption ?>">
                                <a href="<?php echo $this->url('contentView', $page->latin) ?>"
                                   title="<?php echo htmlspecialchars($page->lead) ?>">
                                    <?php echo $page->caption ?>
                                </a>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

            <p class="sideText hidden-xs"><?php echo $this->headText ?></p>

            <!-- TODO позиция для баннера -->

        </div>

        <div class="col-md-9 col-sm-7">

            <?php echo $this->content ?>

            <div class="visible-xs">
                <!-- новые для мобильных -->
                <?php if (count($this->newGames)): ?>
                    <div class="tile-view-caption">Новые игры</div>
                    <div class="game-collection row tile-view clearfix">
                        <?php foreach ($this->newGames as $k => $game): ?>
                            <div class="col-md-12 col-lg-6 col-sm-6 col-xs-6
                    <?php if ($k > 2): ?> hidden-md<?php endif ?>
                    <?php if ($k > 3): ?> hidden-sm hidden-xs<?php endif ?>">
                                <div class="box-preview" title="<?php echo $game->lead ?>">
                                    <a href="<?php echo $this->url('game', $game->latin) ?>">
                                        <img src="<?php echo $this->baseUrl($game->picture) ?>"
                                             alt="<?php echo $game->name ?>" class="img-responsive"/>
                                    </a>
                                    <div class="box text-center">
                                        <a href="<?php echo $this->url('game', $game->latin) ?>">
                                            <?php echo $game->name ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <?php if (($k + 1) % 2 == 0): ?>
                                <div class="clearfix"></div>
                            <?php endif ?>

                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="visible-xs">
                <!-- популярные для мобильных -->
                <?php if (count($this->topGames)): ?>
                    <div class="tile-view-caption">Популярные игры</div>
                    <div class="inline-list row tile-view clearfix">
                        <?php $i = 1; foreach ($this->topGames as $game): ?>
                            <div class="item-inline clearfix" title="<?php echo $game->lead ?>">
                                <a href="<?php echo $this->url('game', $game->latin) ?>"
                                    title="<?php echo htmlspecialchars($game->lead) ?>">
                                    <?php echo $game->name ?>
                                </a>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="visible-xs">
                <!-- популярные для больших устройств -->
                <?php if (count($this->pages)): ?>
                    <div class="inline-list row tile-view clearfix">
                        <?php foreach ($this->pages as $k => $page): ?>
                            <div class="item-inline clearfix <?php if ($k > 5):?>hidden-md<?php endif ?>"
                                 title="<?php echo $page->caption ?>">
                                <a href="<?php echo $this->url('contentView', $page->latin) ?>"
                                   title="<?php echo htmlspecialchars($page->lead) ?>">
                                    <?php echo $page->caption ?>
                                </a>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>

    </div>

    <footer>
        <div class="row">
            <div class="col-md-2 col-sm-3">jezzy.ru <?php echo date('Y') ?> г.</div>
            <div class="col-md-10 col-sm-9 main-text-footer">
                На нашем сайты вы можете скачать новые игры для
                <a href="<?php echo $this->url('type', 'pc') ?>">компьютера</a>,
                так же <a href="<?php echo $this->url('type', 'browser') ?>">онлайн игры</a>
                бесплатно и без регистрации.
            </div>
        </div>
    </footer>
</div>

</body>
</html>