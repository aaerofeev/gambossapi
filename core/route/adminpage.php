<?php

class AdminPage extends RouteCallback {

    protected $auth = array(
        'admin' => '123321',
    );

    /**
     * @var NativeApi
     */
    public $api;

    /**
     * @var Database
     */
    public $database;

    public function checkAuth() {

        if (!isset($_SERVER['PHP_AUTH_USER'])) {

            $this->request->send('WWW-Authenticate: Basic realm="My Realm"');
        } else if (isset($this->auth[$_SERVER['PHP_AUTH_USER']])) {

            return $this->auth[$_SERVER['PHP_AUTH_USER']] == $_SERVER['PHP_AUTH_PW'];
        }

        return FALSE;
    }

    public function start() {

        parent::start();

        // Auth
        if (!$this->checkAuth()) {
            $this->request->send('HTTP/1.0 401 Unauthorized');
            exit;
        }

        // Database
        $this->database = new Database(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);

        // API
        $this->api = new NativeApi($this->database);

        // Layout
        $this->layout = new Template('template/admin');
        $this->layout->setRequest($this->request);
        $this->layout->content = NULL;
        $this->layout->title = array();
        $this->layout->description = '';
        $this->layout->needJavascript = false;

        $adminPage = $this->request->url('admin');
        $this->layout->headText = "<a href='{$adminPage}'>Панель администратора</a> - разделяй и властвуй";

        // Layout values before
        $this->layout->genres = array();
        $this->layout->menuGenres = $this->api->getGenres();
    }

    public function main() {

        $q = isset($_GET['q']) ? safeString($_GET['q']) : '';

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $list = $this->api->getAdmin(100, max(1, $page), NULL, $q);

        $content = new Template('template/admin/index');
        $content->setRequest($this->request);
        $content->title = 'Администратор';
        $content->list = $list['list'];
        $content->q = $q;
        $content->pages = $list['pages'];
        $content->page = $page;

        $this->layout->title[] = 'Администратор';
        $this->layout->content = $content->render();
    }

    public function getTree($idParent = NULL, $checkedKey = array()) {

        $catalog = array();

        foreach ($this->api->getGenres($idParent) as $category) {

            $catalog[$category['id']] = array(
                'name' => $category['name'],
                'child' => array(),
            );

            if (is_array($checkedKey)) {
                $catalog[$category['id']]['checked'] = isset($checkedKey[$category['id']]);
            } else {
                $catalog[$category['id']]['checked'] = ($checkedKey & (int) $category['id']) == $category['id'];
            }

            $catalog[$category['id']]['child'] = $this->getTree($category['id'], $checkedKey);
        }

        if ($idParent) {

            foreach ($this->api->getBrands($idParent) as $category) {

                $catalog[$category['id']] = array(
                    'name' => $category['name'],
                    'child' => array(),
                );

                if (is_array($checkedKey)) {
                    $catalog[$category['id']]['checked'] = isset($checkedKey[$category['id']]);
                } else {
                    $catalog[$category['id']]['checked'] = ($checkedKey & (int) $category['id']) == $category['id'];
                }
            }

        }


        return $catalog;
    }

    /**
     * Загрузить изображение
     *
     * @param $file
     * @param $destination
     * @return bool|string
     */
    public function loadImage($file, $destination) {

        if (!$file) {

            return FALSE;
        }

        if ($file['error'] != UPLOAD_ERR_OK) {

            return FALSE;
        }

        if (strpos($file['type'], 'image') === FALSE) {

            return FALSE;
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination;
        }

        return FALSE;
    }

    public function addGame() {

        $values = extractArray($_POST, array(
            'name', 'lead', 'desc',
            'links', 'screen', 'catalog',
            'image', 'embed', 'picture', 'thumb',
        ));

        $files = extractArray($_FILES, array('image', 'screen'));

        if (!is_array($values['links'])) {

            $value['links'] = array();
        } else {

            $links = array();

            foreach ($values['links'] as $link) {

                if ($link['url']) {
                    $links[] = $link;
                }
            }

            $values['links'] = $links;
        }

        if (!is_array($files['screen'])) {

            $values['screen'] = array();
        } else {

            foreach ($files['screen']['error'] as $key => $code) {

                $file = assemblyFile($files['screen'], $key);

                $imageDestination = sys_get_temp_dir() . DIRECTORY_SEPARATOR . ($key + 1) .
                    '_' . urlize($values['name'], '_') . '.' .
                    pathinfo($file['name'], PATHINFO_EXTENSION);

                if ($this->loadImage($file, $imageDestination)) {

                    $values['screen'][$key] = $imageDestination;
                }
            }
        }

        if ($_POST) {

            foreach ($files['image']['error'] as $key => $code) {

                $file = assemblyFile($files['image'], $key);

                $imageDestination = sys_get_temp_dir() . DIRECTORY_SEPARATOR .
                    $key . '_' . urlize($values['name'], '_') . '.' .
                    pathinfo($file['name'], PATHINFO_EXTENSION);

                if ($this->loadImage($file, $imageDestination)) {

                    $values[$key] = $imageDestination;
                }
            }

            $values['latin'] = urlize($values['name']);
            $values['mask'] = 0;

            foreach ($values['catalog'] as $bitCategory => $isOk) {

                if ($isOk) {
                    $values['mask'] |= (int) $bitCategory;
                }
            }

            $game = extractArray($values, array(
                'name', 'lead', 'desc', 'embed',
                'latin', 'mask', 'picture', 'thumb'
            ));

            $game['picture'] = ltrim(Image::loadByName($game['picture']), '/');

            if ($game['thumb']) {
                $game['thumb'] = ltrim(Image::loadByName($game['thumb']), '/');
            }

            $idGame = $this->database->insert('games', $game);

            if ($idGame) {

                $this->database->update('games', $idGame, array('identity' => $this->api->getIdentity($idGame)));

                foreach ($values['links'] as $link) {

                    $link['id_game'] = $idGame;
                    $this->database->insert('links', $link);
                }

                foreach ($values['screen'] as $screen) {

                    if (!$screen) {
                        continue;
                    }

                    $image = ltrim(Image::loadByName($screen), '/');

                    $tmp = array(
                        'thumb' => $image,
                        'picture' => $image,
                        'id_game' => $idGame,
                    );

                    $this->database->insert('screens', $tmp);
                }

            } else {
                throw new Exception('Ошибка добавления', 500);
            }

            $this->redirect($this->request->url('admin'));
        }

        if (!is_array($values['catalog'])) {

            $values['catalog'] = $this->getTree();
        } else {

            $catalog = $this->getTree(NULL, $values['catalog']);
            $values['catalog'] = $catalog;
        }

        $content = new Template('template/admin/formGame');
        $content->setRequest($this->request);
        $content->title = 'Добавить игру';

        $this->layout->title[] = 'Добавить игру';
        $this->layout->content = $content->render($values);
    }

    public static function drawCatalog($catalog, $idCatalog) {

        echo "<div class='catalog-{$idCatalog} block-catalog'>";

        foreach ($catalog as $id => $options) {

            $checked = '';

            if ($options['checked']) {
                $checked = 'checked';
            }

            echo "<label>
                <input type='checkbox' name='catalog[{$id}]' {$checked} value='1'/>
                {$options['name']}
            </label>";

            static::drawCatalog($options['child'], $id);
        }

        echo "</div>";
    }

    public function editGame($link) {

        $game = $this->api->getGame($link);
        $values = (array) array_pop($game);

        if ($_POST) {

            $postValues = extractArray($_POST, array(
                'name', 'lead', 'desc',
                'links', 'screen', 'catalog',
                'image', 'embed', 'picture', 'thumb',
            ));

            $files = extractArray($_FILES, array('image', 'screen'));

            if (is_array($postValues['links'])) {

                $this->database->delete('links', array('id_game' => $values['id']), 'id');

                foreach ($postValues['links'] as $link) {

                    if ($link['url']) {

                        $link['id_game'] = $values['id'];
                        $this->database->insert('links', $link);
                    }
                }
            }

            if (is_array($files['screen'])) {

                foreach ($files['screen']['error'] as $key => $code) {

                    $file = assemblyFile($files['screen'], $key);

                    $imageDestination = sys_get_temp_dir() . DIRECTORY_SEPARATOR . ($key + 1) .
                        '_' . urlize($postValues['name'], '_') . '.' .
                        pathinfo($file['name'], PATHINFO_EXTENSION);

                    if ($this->loadImage($file, $imageDestination)) {
                        $image = ltrim(Image::loadByName($imageDestination, true), '/');

                        $tmp = array(
                            'thumb' => $image,
                            'picture' => $image,
                            'id_game' => $values['id'],
                        );

                        $this->database->insert('screens', $tmp);
                    }
                }
            }

            if (is_array($files['image'])) {

                foreach ($files['image']['error'] as $key => $code) {

                    $file = assemblyFile($files['image'], $key);

                    $imageDestination = sys_get_temp_dir() . DIRECTORY_SEPARATOR .
                        $key . '_' . urlize($postValues['name'], '_') . '.' .
                        pathinfo($file['name'], PATHINFO_EXTENSION);

                    if ($this->loadImage($file, $imageDestination)) {
                        $postValues[$key] = ltrim(Image::loadByName($imageDestination, true), '/');
                    }
                }
            }

            $postValues['latin'] = urlize($postValues['name']);
            $postValues['mask'] = 0;

            foreach ($postValues['catalog'] as $bitCategory => $isOk) {

                if ($isOk) {
                    $postValues['mask'] |= (int) $bitCategory;
                }
            }

            $game = extractArray($postValues, array(
                'name', 'lead', 'desc', 'embed',
                'latin', 'mask', 'picture', 'thumb'
            ));

            if (!$game['picture']) {
                unset($game['picture']);
            }

            if (!$game['thumb']) {
                unset($game['thumb']);
            }

            if ($this->database->update('games', $values['id'], $game)) {

                $this->redirect($this->request->url('admin'));
            } else {
                throw new Exception('Ошибка добавления', 500);
            }
        }

        $values['catalog'] = $this->getTree(NULL, $values['mask']);

        $content = new Template('template/admin/formGame');
        $content->setRequest($this->request);
        $content->title = 'Редактировать игру';

        $this->layout->title[] = 'Редактировать игру';
        $this->layout->content = $content->render($values);
    }

    public function removeGame($link) {

        $game = $this->api->getGame($link);
        $return = $this->request->getParam('return');

        /*
        $idGame = $this->database->delete('games', array('latin' => $link));
        $this->database->delete('catalog_games', array('id_game' => $idGame), NULL);
        $this->database->delete('links', array('id_game' => $idGame), NULL);
        $this->database->delete('screens', array('id_game' => $idGame), NULL);
        */

        if (isset($game[0])) {

            $this->database->update('games', $game[0]->id, array('removed' => true));
        }

        $this->redirect($return);
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

        parent::finish();
    }

    public function upGame($link) {

        $return = $this->request->getParam('return');
        $game = $this->api->getGame($link);

        if (empty($game)) {
            throw new Exception('Game not found', 500);
        }

        $recommendedValue = time();

        if ($game[0]->isRecommended()) {
            $recommendedValue = $game[0]->rate;
        }

        $this->database->update('games', $game[0]->id, array('recommendation' => $recommendedValue));
        $this->redirect($return);
    }

    public function contentList() {

        $list = $this->database->select('content', NULL, NULL, array('id'=>'DESC'));

        $content = new Template('template/admin/content');
        $content->setRequest($this->request);

        $content->list = $list;

        $this->layout->content = $content->render();
        $this->layout->title[] = 'Контентные страницы';
    }

    public function addContent() {

        $values = extractArray($_POST, array('caption', 'lead', 'image', 'body'));

        $content = new Template('template/admin/formContent');
        $content->setRequest($this->request);

        if ($_POST) {

            $imageDestination = sys_get_temp_dir() . DIRECTORY_SEPARATOR .
                urlize($values['caption'], '_') . '.' .
                pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $values['image'] = $this->loadImage($_FILES['image'], $imageDestination);
            $values['latin'] = urlize($values['caption']);

            if ($values['image']) {
                $values['image'] = ltrim(Image::loadByName($values['image']), '/');
            }

            if ($this->database->insert('content', $values)) {

                $this->redirect($this->request->url('contentView', $values['latin']));

            } else {
                throw new Exception('Content add problem', 500);
            }
        }

        $content->title = 'Добавить страницу';

        $this->layout->title[] = $content->title;
        $this->layout->content = $content->render($values);
    }

    public function editContent($link) {

        $values = $this->database->select('content', array('latin' => $link), NULL, NULL, NULL, 1, PDO::FETCH_ASSOC);

        $content = new Template('template/admin/formContent');
        $content->setRequest($this->request);

        if ($_POST) {

            $postValues = extractArray($_POST, array('caption', 'lead', 'image', 'body'));

            $imageDestination = sys_get_temp_dir() . DIRECTORY_SEPARATOR .
                urlize($postValues['caption'], '_') . '.' .
                pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $postValues['image'] = $this->loadImage($_FILES['image'], $imageDestination);
            $postValues['latin'] = urlize($postValues['caption']);

            if ($postValues['image']) {
                $postValues['image'] = ltrim(Image::loadByName($postValues['image'], true), '/');
            } else {
                unset($postValues['image']);
            }

            if ($this->database->update('content', $values['id'], $postValues)) {

                $this->redirect($this->request->url('contentView', $postValues['latin']));

            } else {
                throw new Exception('Content edit problem', 500);
            }
        }

        $content->title = 'Редактировать страницу';

        $this->layout->title[] = $content->title;
        $this->layout->content = $content->render($values);
    }

    public function removeContent($link) {

        $return = $this->request->getParam('return');
        $this->database->delete('content', array('latin' => $link));
        $this->redirect($return);
    }
}