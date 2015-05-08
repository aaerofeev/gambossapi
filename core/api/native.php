<?php

require_once 'gameapi.php';
require_once 'game.php';

class NativeApi implements GameApi {

    protected $cache;
    public $cacheLifetime = 72000;

    /**
     * @var Database
     */
    protected $source;

    public function __construct(Database $db) {

        $this->source = $db;
    }

    public function getGenreMask($name, $hidden = 0) {

        $genre = $this->getGenre($name, $hidden);

        if (!$genre) {
            throw new Exception('Genre not found');
        }

        return $genre['id'];
    }

    public function getGenre($name, $hidden = 0) {

        $list = cacheGet("native_getGenre_{$name}_{$hidden}");

        if (!$list) {

            $sql = 'SELECT * FROM catalog WHERE latin = ? AND hidden = ? LIMIT 1';

            $stmt = $this->source->db()->prepare($sql);
            $stmt->execute(array($name, $hidden));

            $list = $stmt->fetch(PDO::FETCH_ASSOC);

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    public function getGenreById($id) {

        $list = cacheGet("native_getGenreById_{$id}");

        if (!$list) {

            $sql = 'SELECT * FROM catalog WHERE id = ? LIMIT 1';

            $stmt = $this->source->db()->prepare($sql);
            $stmt->execute(array($id));

            $list = $stmt->fetch(PDO::FETCH_ASSOC);

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }


    public function getGenresAll($hidden = 0) {

        $list = cacheGet("native_getGenresAll");

        if (!$list) {

            $sql = 'SELECT * FROM catalog WHERE hidden = ? ORDER BY latin ASC';

            $stmt = $this->source->db()->prepare($sql);
            $stmt->execute(array($hidden));

            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    public function getBrands($filterParent = NULL) {

        $list = cacheGet("native_getBrands_{$filterParent}");

        if (!$list) {

            $exeValues = array();

            $sql = 'SELECT `id`, `name`, REPLACE(latin, "partner-", "") as `latin` FROM catalog
            WHERE latin LIKE :partner ORDER BY latin ASC';

            $exeValues[':partner'] = 'partner-%';

            if ($filterParent) {

                $sql = 'SELECT `id`, `name`, REPLACE(latin, "partner-", "") as `latin` FROM catalog
                WHERE :parent = (id_parent & :parent) AND latin LIKE :partner ORDER BY latin ASC';
                $exeValues[':parent'] = $filterParent;
            }

            $stmt = $this->source->db()->prepare($sql);
            $stmt->execute($exeValues);

            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    public function getGenres($idParent = NULL) {

        $list = cacheGet("native_getGenres_{$idParent}");

        if (!$list) {

            $sql = 'SELECT * FROM catalog WHERE id_parent IS NULL AND hidden = 0 ORDER BY latin ASC';
            $exeValues = array();

            if ($idParent) {
                $sql = 'SELECT * FROM catalog WHERE :parent = (id_parent & :parent) AND hidden = 0 ORDER BY latin ASC';
                $exeValues[':parent'] = $idParent;
            }

            $stmt = $this->source->db()->prepare($sql);
            $stmt->execute($exeValues);

            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    /**
     * @param $sql
     * @param array $values
     * @return Game[]
     */
    protected function getDump($sql, $values = array()) {

        $stmt = $this->source->db()->prepare($sql);
        $stmt->execute($values);

        $screenStmt = $this->source->db()->prepare('SELECT * FROM screens WHERE id_game = ?');
        $linksStmt = $this->source->db()->prepare('SELECT * FROM links WHERE id_game = ?');

        $games = array();

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $game = new Game();
            $game->id = $row['id'];
            $game->rate = $row['rate'];
            $game->name = $row['name'];
            $game->latin = $row['latin'];
            $game->lead = $row['lead'];
            $game->desc = $row['desc'];
            $game->mask = $row['mask'];
            $game->thumb = $row['thumb'];
            $game->picture = $row['picture'];
            $game->created = $row['created'];
            $game->recommendation = $row['recommendation'];
            $game->embed = $row['embed'];

            $linksStmt->execute(array($row['id']));

            foreach ($linksStmt->fetchAll(PDO::FETCH_ASSOC) as $link) {

                $game->links[] = array(
                    'name' => $link['name'],
                    'url' => $link['url'],
                );
            }

            $screenStmt->execute(array($row['id']));

            foreach ($screenStmt->fetchAll(PDO::FETCH_ASSOC) as $screen) {

                $game->screen[] = array(
                    'thumb' => $screen['thumb'],
                    'picture' => $screen['picture'],
                );
            }

            $games[] = $game;
        }

        return $games;
    }

    /**
     * @param $limit
     * @param null $genre
     * @return Game[]
     */
    public function getTop($limit, $genre = NULL) {

        $list = cacheGet("native_getTop_{$limit}_{$genre}");

        if (!$list) {

            if ($genre) {

                $list = $this->getDump("SELECT * FROM games WHERE (mask & :mask) = :mask AND removed = 0
                ORDER BY recommendation DESC LIMIT {$limit}", array(':mask' => $genre));

            } else {

                $list = $this->getDump("SELECT * FROM games WHERE 1 = 1 AND removed = 0
                ORDER BY recommendation DESC LIMIT {$limit}");
            }

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    /**
     * @param $limit
     * @param null $genre
     * @return Game[]
     */
    public function getNew($limit, $genre = NULL) {

        $list = cacheGet("native_getNew_{$limit}_{$genre}");

        if (!$list) {

            if ($genre) {

                $list = $this->getDump("SELECT * FROM games WHERE (mask & :mask) = :mask AND removed = 0
                ORDER BY id DESC LIMIT {$limit}", array(':mask' => $genre));

            } else {

                $list = $this->getDump("SELECT * FROM games WHERE 1 = 1 AND removed = 0
                  ORDER BY id DESC LIMIT {$limit}");
            }

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    /**
     * @param null $limit
     * @param null $genre
     * @return Game[]
     */
    public function getRecommendation($limit = NULL, $genre = NULL) {

        $list = cacheGet("native_getRecommendation_{$limit}_{$genre}");

        if (!$list) {

            if ($genre) {

                $list = $this->getDump("SELECT * FROM games WHERE (mask & :mask) = :mask AND removed = 0
                ORDER BY recommendation DESC LIMIT {$limit}", array(':mask' => $genre));

            } else {

                $list = $this->getDump("SELECT * FROM games WHERE 1 = 1 AND removed = 0
                ORDER BY recommendation DESC LIMIT {$limit}");
            }

            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    /**
     * @param $name
     * @return Game[]
     */
    public function getGame($name) {

        $list = cacheGet("native_getGame_$name");

        if (!$list) {
            $list = $this->getDump("SELECT * FROM games WHERE latin = :latin AND removed = 0 LIMIT 1", array(':latin' => $name));
            cacheSet($list, NULL, $this->cacheLifetime);
        }

        return $list;
    }

    public function getRecommendationGenre($genre, $limit, $page = 1, $query = NULL) {

        $condition = '(mask & :mask) = :mask';
        $params = array(':mask' => $genre);

        if ($query) {
            $condition .= ' AND (name LIKE :query OR lead LIKE :query)';
            $params[':query'] = '%' . $query . '%';
        }

        $stmt = $this->source->db()->prepare("SELECT COUNT(id) as cnt FROM games WHERE {$condition} AND removed = 0");
        $stmt->execute($params);
        $count = $stmt->fetchColumn(0);

        $pages = ceil($count / $limit);
        $page = min(max(1, $page), $pages);

        $offset = ($page - 1) * $limit;

        $list = $this->getDump("SELECT * FROM games WHERE {$condition} AND removed = 0 ORDER BY recommendation DESC LIMIT {$limit} OFFSET {$offset}", $params);

        return array('list' => $list, 'count' => $count, 'pages' => $pages, 'page' => $page);
    }

    public function getIdentity($id) {

        return 'nat-' . $id;
    }

    public function getAdmin($limit, $page, $genre = NULL, $q = NULL) {

        if ($genre) {

            $condition = '(mask & :mask) = :mask';
            $params = array(':mask' => $genre);

        } else {

            $condition = '1 = 1';
            $params = array();
        }

        if ($q) {

            $condition .= ' AND name LIKE :q';
            $params[':q'] = '%' . $q . '%';
        }

        $stmt = $this->source->db()->prepare("SELECT COUNT(id) as cnt FROM games
            WHERE {$condition} AND removed = 0");
        $stmt->execute($params);
        $count = $stmt->fetchColumn(0);

        $offset = ($page - 1) * $limit;

        $list = $this->getDump("SELECT * FROM games
            WHERE {$condition}  AND removed = 0 ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}", $params);

        return array('list' => $list, 'count' => $count, 'pages' => ceil($count / $limit));
    }

    public function getRelated(Game $game) {

        $titleRelatedCut = mb_substr($game->latin, 0, 5, 'utf8');
        $related = $this->getDump('SELECT * FROM games WHERE latin LIKE :cut
        AND id != :exc AND removed = 0 ORDER BY id DESC LIMIT 4',
            array(':cut' => $titleRelatedCut . '%', ':exc' => $game->id));

        if (count($related) < 4) {

            $exc = array($game->id);

            foreach ($related as $g) {
                $exc[] = $g->id;
            }

            $excId = implode(',', $exc);

            $relatedMask = $this->getDump("SELECT * FROM games
            WHERE mask = :mask AND id NOT IN ({$excId}) AND removed = 0 ORDER BY RAND() LIMIT 4",
                array(':mask' => $game->mask));

            $related = array_merge($related, $relatedMask);
        }

        return array_splice($related, 0, 4);
    }
}