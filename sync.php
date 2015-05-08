<?php

require_once 'core/config.php';
require_once 'core/api/gamecollection.php';
require_once 'core/api/gameboss.php';
require_once 'core/api/alawar.php';
require_once 'core/api/native.php';

// Cache
$cache->setEnable(false);

// Database
$database = new Database(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);

// Duplicate
$stmtDuplicate = $database->db()->prepare('SELECT `id`, `identity`, `latin`, `embed`, `mask` FROM games
WHERE latin = ? AND `identity` NOT LIKE ? LIMIT 1');
$stmtDuplicateLink = $database->db()->prepare('SELECT COUNT(id) FROM links WHERE id_game = ? AND url = ?');

// Alawar
$alawarApi = new AlawarApi(ALAWAR_API);

// Sync process
echo "Sync alawar\n";

$alreadyExists = array();
$stmt = $database->db()->prepare('SELECT identity FROM games WHERE identity LIKE ? ORDER BY id DESC');
$stmt->execute(array('alw-%'));
$lastIdentities = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($lastIdentities as $row) {
    $alreadyExists[] = $row['identity'];
}

// Catalog sync
$catalogSync = array();
$catalogRoot = 2;

$stmtCatalog = $database->db()->prepare('SELECT * FROM catalog WHERE id_parent = ? AND id = ? LIMIT 1');

$stmtBin = $database->db()->prepare('SELECT SQRT(id) as pow, id FROM catalog ORDER BY id DESC LIMIT 1');
$stmtBin->execute();
$binStart = $stmtBin->fetch(PDO::FETCH_ASSOC);

if (!$binStart || $binStart['id'] < 4) {
    $binStart = array('pow' => 2);
}

foreach ($alawarApi->getGenres(AlawarApi::PARENT_CASUAL) as $apiGenre) {

    $apiIdGenre = $apiGenre['id'];

    $stmtCatalog->execute(array($catalogRoot, $apiIdGenre));

    $catalogRow = $stmtCatalog->fetch(PDO::FETCH_ASSOC);

    if (!$catalogRow) {

        throw new Exception('Catalog not found ' . $apiGenre['name']);
    }

    $catalogSync[$apiIdGenre] = $apiGenre['latin'];
}

$onlineCatalog = AlawarApi::PARENT_ONLINE;

// Games sync
foreach ($alawarApi->getNew(0) as $apiGame) {

    $identity = $alawarApi->getIdentity($apiGame->id);

    if (in_array($identity, $alreadyExists)) {
        echo "Exists {$apiGame->id}\n";
        continue;
    }

    $game = array();
    $game['latin'] = urlize($apiGame->name);
    $game['rate'] = $apiGame->rate;
    $game['name'] = $apiGame->name;
    $game['lead'] = $apiGame->lead;
    $game['desc'] = $apiGame->desc;
    $game['thumb'] = ltrim($apiGame->thumb, '/');
    $game['picture'] = ltrim($apiGame->picture, '/');
    $game['identity'] = $identity;
    $game['created'] = $apiGame->created;
    $game['embed'] = $apiGame->embed;

    if ($apiGame->mask == $onlineCatalog) {

        $game['mask'] = AlawarApi::CATALOG_ALAWAR | $onlineCatalog;

    } else {

        if ($apiGame->mask) {

            $catalog = (int) array_search($apiGame->mask, $catalogSync);
        } else {
            $catalog = 0;
        }

        $game['mask'] = AlawarApi::CATALOG_ALAWAR | $catalog | $catalogRoot;
    }

    $idGame = $database->insert('games', $game);

    if ($idGame) {
        foreach ($apiGame->screen as $screen) {

            $screen['id_game'] = $idGame;
            $screen['thumb'] = ltrim($screen['thumb'], '/');
            $screen['picture'] = ltrim($screen['picture'], '/');

            if ($screen['thumb'] || $screen['picture']) {
                $database->insert('screens', $screen);
            }
        }

        $link = array(
            'id_game' => $idGame,
            'url' => $apiGame->links[0]['url'],
            'name' => $apiGame->links[0]['name'],
        );

        if ($link['url']) {
            $database->insert('links', $link);
        }

        echo "Add game {$game['identity']}\n";
    } else {

        $stmtDuplicate->execute(array($game['latin'], 'nothing'));

        if ($stmtDuplicate->rowCount()) {

            $duplicate = $stmtDuplicate->fetchObject();

            $gameLoaded = $duplicate->id;
            $gameIdentity = $duplicate->identity;
            $gameLatin = $duplicate->latin;
            $gameMask = (int) $duplicate->mask;

            $stmtDuplicateLink->execute(array($gameLoaded, $apiGame->links[0]['url']));

            if ($stmtDuplicateLink->fetchColumn() == 0) {

                $link = array(
                    'id_game' => $idGame,
                    'url' => $apiGame->links[0]['url'],
                    'name' => $apiGame->links[0]['name'],
                );

                if ($link['url']) {
                    $database->insert('links', $link);
                }

                echo "Linked {$game['identity']} to {$gameIdentity}. ";

            } else {

                echo "Try link and skip {$game['identity']} as {$gameIdentity}. ";
            }

            $embed = $duplicate->embed;

            if (!$embed && $game['embed']) {

                $database->update('games', $gameLoaded, array(
                    'embed' => $game['embed'],
                    'mask' => $gameMask | (int) $game['mask'],
                ));

                echo "Embed {$game['identity']} to {$gameIdentity}. ";
            }

            echo "See game owner {$gameLatin}\n";

        } else {

            echo "Skip {$game['identity']}\n";
        }
    }

    //sleep(1);
}

// Gameboss API
$gamebossApi = new GameBossApi(GAMEBOSS_API);

// Sync process
$alreadyExists = array();
$stmt = $database->db()->prepare('SELECT identity FROM games WHERE identity LIKE ? ORDER BY id DESC');
$stmt->execute(array('gb-%'));
$lastIdentities = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($lastIdentities as $row) {
    $alreadyExists[] = $row['identity'];
}

// Catalog sync
$catalogSync = array();
$catalogRoot = 2;

$stmtCatalog = $database->db()->prepare('SELECT * FROM catalog WHERE id_parent = ? AND latin = ? LIMIT 5');

$stmtBin = $database->db()->prepare('SELECT SQRT(id) as pow, id FROM catalog ORDER BY id DESC LIMIT 1');
$stmtBin->execute();
$binStart = $stmtBin->fetch(PDO::FETCH_ASSOC);

if (!$binStart || $binStart['id'] < 4) {
    $binStart = array('pow' => 2);
}

foreach ($gamebossApi->getGenres() as $apiGenre) {

    $apiIdGenre = $apiGenre['id'];
    $latin = $apiGenre['latin'];

    $stmtCatalog->execute(array($catalogRoot, $latin));

    $catalogRow = $stmtCatalog->fetch(PDO::FETCH_ASSOC);

    if (!$catalogRow) {

        throw new Exception("Category not found {$latin}");
    }

    $catalogSync[$apiIdGenre] = $catalogRow['id'];
}

echo "Sync gameboss\n";

// Games sync
foreach ($gamebossApi->getNew(100) as $apiGame) {

    $identity = $gamebossApi->getIdentity($apiGame->id);

    if (in_array($identity, $alreadyExists)) {
        continue;
    }

    $apiGameFull = $gamebossApi->getGame(str_replace('-', '_', $apiGame->latin));

    if (!$apiGameFull || count($apiGameFull) == 0) {
        continue;
    }

    $mask = $catalogRoot | GameBossApi::CATALOG_NEVOSOFT;

    foreach ($catalogSync as $apiId => $dbId) {

        if ($apiId & $apiGameFull[0]->mask) {

            $mask |= (int) $dbId;
        }
    }

    $game = array();
    $game['latin'] = urlize($apiGameFull[0]->name);
    $game['rate'] = $apiGameFull[0]->rate;
    $game['name'] = $apiGameFull[0]->name;
    $game['lead'] = $apiGameFull[0]->lead;
    $game['desc'] = $apiGameFull[0]->desc;
    $game['thumb'] = ltrim($apiGameFull[0]->thumb, '/');
    $game['picture'] = ltrim($apiGameFull[0]->picture, '/');
    $game['identity'] = $identity;
    $game['mask'] = $mask;

    $idGame = $database->insert('games', $game);

    if ($idGame) {

        foreach ($apiGameFull[0]->screen as $screen) {

            $screen['id_game'] = $idGame;
            $screen['thumb'] = ltrim($screen['thumb'], '/');
            $screen['picture'] = ltrim($screen['picture'], '/');

            if ($screen['thumb'] && $screen['picture']) {
                $database->insert('screens', $screen);
            }
        }

        $link = array(
            'id_game' => $idGame,
            'url' => $apiGameFull[0]->links[0]['url'],
            'name' => $apiGameFull[0]->links[0]['name'],
        );

        if ($link['url']) {
            $database->insert('links', $link);
        }

        echo "Add game {$game['identity']}\n";
    } else {

        $stmtDuplicate->execute(array($game['latin'], 'gb-%'));

        if ($stmtDuplicate->rowCount()) {

            $duplicate = $stmtDuplicate->fetchObject();

            $gameLoaded = $duplicate->id;
            $gameIdentity = $duplicate->identity;
            $gameLatin = $duplicate->latin;

            $stmtDuplicateLink->execute(array($gameLoaded, $apiGameFull[0]->links[0]['url']));

            if ($stmtDuplicateLink->fetchColumn() == 0) {

                $link = array(
                    'id_game' => $gameLoaded,
                    'url' => $apiGameFull[0]->links[0]['url'],
                    'name' => $apiGameFull[0]->links[0]['name'],
                );

                if ($link['url']) {
                    $database->insert('links', $link);
                }

                echo "Linked {$game['identity']} to {$gameIdentity}. ";

            } else {

                echo "Try link and skip {$game['identity']} as {$gameIdentity}. ";
            }

            echo "See game owner {$gameLatin}\n";

        } else {

            echo "Skip {$game['identity']}\n";
        }
    }

    //sleep(1);
}

exit("Exit!\n");