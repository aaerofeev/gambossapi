<?php

class GameCollection {

    /**
     * Получить заголовки из игр в виде строки
     *
     * @param Game[] $games
     * @param int $limit
     * @param string $separator
     * @return string
     */
    public static function getGamesNames($games, $limit, $separator = ', ') {

        $titles = array();

        foreach ($games as $game) {

            if ($game->name) {

                $titles[] = $game->name;
                $limit --;
            }

            if ($limit == 0) {
                break;
            }
        }

        return implode($separator, $titles);
    }
}