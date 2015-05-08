<?php

require_once 'core/route/routecallback.php';
require_once 'core/route/frontpage.php';
require_once 'core/route/adminpage.php';

$cache->setEnable(false);

// Request
$request = new Request($_SERVER['REQUEST_URI']);
$request->setBaseUrl(BASE_URL);

$adminPage = new AdminPage();

$frontPage = new FrontPage();
$request->setErrorCallback(array($frontPage, 'errorCallback'));

// Admin
$request->addRoute('admin', 'admin', 'admin.html', array($adminPage, 'main'));
// Game
$request->addRoute('addGame', 'admin/add', 'admin/add.html', array($adminPage, 'addGame'));
$request->addRoute('editGame', 'admin/edit/(.+?)', 'admin/edit/%s.html', array($adminPage, 'editGame'));
$request->addRoute('upGame', 'admin/up/(.+?)', 'admin/up/%s.html', array($adminPage, 'upGame'));
$request->addRoute('removeGame', 'admin/remove/(.+?)', 'admin/remove/%s.html', array($adminPage, 'removeGame'));
// Content
$request->addRoute('contentList', 'admin/content', 'admin/content.html', array($adminPage, 'contentList'));
$request->addRoute('addContent', 'admin/addContent', 'admin/addContent.html', array($adminPage, 'addContent'));
$request->addRoute('editContent', 'admin/content/edit/(.+?)', 'admin/content/edit/%s.html', array($adminPage, 'editContent'));
$request->addRoute('removeContent', 'admin/content/remove/(.+?)', 'admin/content/remove/%s.html', array($adminPage, 'removeContent'));

// Service
$request->addRoute('sitemap', 'sitemap', 'sitemap.xml', array($frontPage, 'sitemap'));
$request->addRoute('sitemapLow', 'sitemaplow', 'sitemaplow.xml', array($frontPage, 'sitemap'));
$request->addRoute('robots', 'robots', 'robots.txt', array($frontPage, 'robots'));

// FrontPage
$request->addRoute('main', 'index', 'index.html', array($frontPage, 'main'));
$request->addRoute('main', '', '', array($frontPage, 'main'));

$request->addRoute('game', 'game/(.+?)', 'game/%s.html', array($frontPage, 'game'));

$request->addRoute('contentView', 'catalog/(.+?)', 'catalog/%s.html', array($frontPage, 'content'));

$request->addRoute('brandGenre', '(?P<type>.+?)/made/(?P<brand>.+?)/genre/(?P<genre>.+?)', '%s/made/%s/genre/%s.html', array($frontPage, 'genre'));
$request->addRoute('genre', '(?P<type>.+?)/genre/(?P<genre>.+?)', '%s/genre/%s.html', array($frontPage, 'genre'));
$request->addRoute('brand', '(?P<type>.+?)/made/(?P<brand>.+?)', '%s/made/%s.html', array($frontPage, 'genre'));
$request->addRoute('type', '(?P<type>.+?)', '%s.html', array($frontPage, 'genre'));

$request->run();