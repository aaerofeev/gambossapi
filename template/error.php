<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="/image/favicon.png" />
    <link href="/bootstrap/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/style/main.css" media="screen" rel="stylesheet" type="text/css" />
    <title><?php echo $this->message ?></title>
<body>

<div class="container decorated">

    <nav>
        <ul>
            <li><a href="/">Главная</a></li>
        </ul>
        <span class="nav-text">
            Мы уже знаем об этой ошибке и занимаемся её решением
        </span>
    </nav>

</div>

<div class="container decorated">

    <h1>Ошибка <?php echo $this->code ?></h1>
    <p class="lead"><?php echo $this->message ?></p>

    <footer>
        <div class="row">
            <div class="col-md-2">jezzy.ru <?php echo date('Y') ?> г.</div>
        </div>
    </footer>
</div>

</body>
</html>