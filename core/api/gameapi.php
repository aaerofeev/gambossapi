<?php

interface GameApi {

    public function getGenres($idParent = NULL);
    public function getNew($limit, $genre = NULL);
    public function getGame($name);
    public function getIdentity($id);
}