<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo $this->baseUrl('image/favicon.png') ?>" />
    <link href="<?php echo $this->baseUrl('bootstrap/css/bootstrap.min.css') ?>" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->baseUrl('lightbox/ekko-lightbox.min.css') ?>" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->baseUrl('style/main.css') ?>" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->baseUrl('style/admin.css') ?>" media="screen" rel="stylesheet" type="text/css" />

    <?php if ($this->needJavascript): ?>
    <script type="text/javascript" src="<?php echo $this->baseUrl('jquery/jquery-1.11.2.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl('bootstrap/js/bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl('lightbox/ekko-lightbox.min.js') ?>"></script>
    <?php endif ?>

    <title><?php echo $this->title ?></title>
</head>
<body>

<div class="container decorated">

    <nav>
        <ul>
            <li><a href="<?php echo $this->url('main') ?>">Главная</a></li>

            <?php foreach ($this->menuGenres as $menu): ?>
            <li><a href="<?php echo $this->url('type', $menu['latin']) ?>"><?php echo $menu['name'] ?></a></li>
            <?php endforeach ?>
        </ul>
        <span class="nav-text">
            <?php echo $this->headText ?>
        </span>
    </nav>

</div>

<div class="container decorated">

    <div class="row">

        <div class="col-md-12">

            <?php echo $this->content ?>

        </div>
    </div>

    <footer>
        <div class="row">
            <div class="col-md-2">jezzy.ru <?php echo date('Y') ?> г.</div>
            <div class="col-md-10 main-text-footer">
                <?php echo $this->headText ?>
            </div>
        </div>
    </footer>
</div>

</body>
</html>