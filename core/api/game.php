<?php

/**
 * Модель игры
 *
 * Class Game
 */
class Game {

    public $id;
    public $rate;
    public $name;
    public $lead;
    public $latin;
    public $thumb;
    public $picture;
    public $created;
    public $mask;
    public $embed;
    public $desc;

    public $screen = array();
    public $links = array();
    public $related = array();

    // Native fields
    public $recommendation;

    public function isRecommended() {

        return $this->recommendation > 1000000;
    }

    public function isOnline() {

        return !empty($this->embed);
    }

    public function getGenreText($genres) {

        $toText = array();

        foreach ($genres as $genre) {

            if ($genre['id_parent']) {

                if (((int) $this->mask & (int) $genre['id']) == (int) $genre['id']) {
                    $toText[] = $genre['name'];
                }
            }
        }

        return implode(', ', $toText);

    }
}