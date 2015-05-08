<?php

class Image {

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var Image
     */
    private static $instance;

    private function __construct($path) {

        $this->directory = $path;
    }

    public static function setup($path) {

        static::$instance = new Image($path);
    }

    public static function getInstance() {

        return static::$instance;
    }

    public function get($image, $imageSave, $overwrite = false) {

        if (file_exists($imageSave) && !$overwrite) {

            return str_replace(APPLICATION_ROOT, '/', $imageSave);
        }

        $imageSource = @file_get_contents($image);

        if ($imageSource) {

            file_put_contents($imageSave, $imageSource);
            return str_replace(APPLICATION_ROOT, '/', $imageSave);
        }

        return $image;
    }

    public function preLoad($image, $overwrite = false) {

        $path = parse_url($image, PHP_URL_PATH);
        $path = str_replace('/', '_', trim($path, '/'));
        $imageSave = $this->directory . '/' . $path;

        return $this->get($image, $imageSave, $overwrite);
    }

    public function preLoadByName($image, $overwrite = false) {

        $imageSave = $this->directory . '/' .
            pathinfo($image, PATHINFO_BASENAME);

        return $this->get($image, $imageSave, $overwrite);
    }

    public static function load($image, $overwrite = false) {

        return static::getInstance()->preLoad($image, $overwrite);
    }

    public static function loadByName($image, $overwrite = false) {

        return static::getInstance()->preLoadByName($image, $overwrite);
    }
}