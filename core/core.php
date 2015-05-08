<?php

/**
 * Транслитерация русского в английский
 *
 * @param $text
 * @return mixed
 */
function transliterate($text) {

    $tr = array(
        'Ґ'=>'G','Ё'=>'YO','Є'=>'E','Ї'=>'YI','І'=>'I',
        'і'=>'i','ґ'=>'g','ё'=>'yo','№'=>'#','є'=>'e',
        'ї'=>'yi','А'=>'A','Б'=>'B','В'=>'V','Г'=>'G',
        'Д'=>'D','Е'=>'E','Ж'=>'ZH','З'=>'Z','И'=>'I',
        'Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
        'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T',
        'У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'TS','Ч'=>'CH',
        'Ш'=>'SH','Щ'=>'SCH','Ъ'=>'\'','Ы'=>'YI','Ь'=>'',
        'Э'=>'E','Ю'=>'YU','Я'=>'YA','а'=>'a','б'=>'b',
        'в'=>'v','г'=>'g','д'=>'d','е'=>'e','ж'=>'zh',
        'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l',
        'м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
        'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h',
        'ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'\'',
        'ы'=>'yi','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya');

    return strtr($text,$tr);
}

/**
 * Подготовка русского текста для использования в виде ссылке
 *
 * @param $text
 * @param string $space
 * @return string
 */
function urlize($text, $space = '-') {

    $text = mb_strtolower(transliterate($text));
    $text = preg_replace("|[^\\w_\\d_{$space}\\s_\\-]+|", NULL, $text);

    $tr = array(
        ' ' => $space,
        '-' => $space,
        '\n' => '',
        '\r' => '',
        '\'' => '',
        str_repeat($space, 6) => $space,
        str_repeat($space, 5) => $space,
        str_repeat($space, 4) => $space,
        str_repeat($space, 3) => $space,
        str_repeat($space, 2) => $space,
    );



    return substr(strtr($text, $tr), 0, 255);
}

/**
 * Байты в человека-понятный формат
 *
 * @param $size
 * @param string $unit
 * @return string
 */
function humanFileSize($size,$unit='') {

    if( (!$unit && $size >= 1<<30) || $unit == 'Гб')
        return number_format($size/(1<<30),1).'Гб';
    if( (!$unit && $size >= 1<<20) || $unit == 'Мб')
        return number_format($size/(1<<20),1).'Мб';
    if( (!$unit && $size >= 1<<10) || $unit == 'Кб')
        return number_format($size/(1<<10),1).'Кб';

    return number_format($size).' bytes';
}

/**
 * Почистить строку
 *
 * @param $string
 * @return string
 */
function safeString($string) {

    $string = strip_tags($string);
    return htmlspecialchars($string);
}

/**
 * Изввлечь данные из массива
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function extractArray($array, $keys) {

    $values = array();

    foreach ($keys as $key) {

        if (isset($array[$key])) {

            $values[$key] = $array[$key];
        } else {
            $values[$key] = NULL;
        }
    }

    return $values;
}

function openImage ($file) {
    //detect type and process accordinally
    $size = getimagesize($file);

    switch($size["mime"]){
        case "image/jpeg":
            $im = imagecreatefromjpeg($file); //jpeg file
            break;
        case "image/gif":
            $im = imagecreatefromgif($file); //gif file
            break;
        case "image/png":
            $im = imagecreatefrompng($file); //png file
            break;
        default:
            $im=false;
            break;
    }

    return $im;
}

function thumbnail($image, $box_w, $box_h, $imageDestination) {

    $img = openImage($image);

    //create the image, of the required size
    $new = imagecreatetruecolor($box_w, $box_h);
    if($new === false) {
        //creation failed -- probably not enough memory
        return null;
    }


    //Fill the image with a light grey color
    //(this will be visible in the padding around the image,
    //if the aspect ratios of the image and the thumbnail do not match)
    //Replace this with any color you want, or comment it out for black.
    //I used grey for testing =)
    $fill = imagecolorallocate($new, 200, 200, 205);
    imagefill($new, 0, 0, $fill);

    //compute resize ratio
    $hratio = $box_h / imagesy($img);
    $wratio = $box_w / imagesx($img);
    $ratio = min($hratio, $wratio);

    //if the source is smaller than the thumbnail size,
    //don't resize -- add a margin instead
    //(that is, dont magnify images)
    if($ratio > 1.0)
        $ratio = 1.0;

    //compute sizes
    $sy = floor(imagesy($img) * $ratio);
    $sx = floor(imagesx($img) * $ratio);

    //compute margins
    //Using these margins centers the image in the thumbnail.
    //If you always want the image to the top left,
    //set both of these to 0
    $m_y = floor(($box_h - $sy) / 2);
    $m_x = floor(($box_w - $sx) / 2);

    //Copy the image data, and resample
    //
    //If you want a fast and ugly thumbnail,
    //replace imagecopyresampled with imagecopyresized
    if(!imagecopyresampled($new, $img,
        $m_x, $m_y, //dest x, y (margins)
        0, 0, //src x, y (0,0 means top left)
        $sx, $sy,//dest w, h (resample to this size (computed above)
        imagesx($img), imagesy($img)) //src w, h (the full size of the original)
    ) {
        //copy failed
        imagedestroy($new);
        return null;
    }

    if (imagejpeg($new, $imageDestination)) {
        return $imageDestination;
    }
    
    return NULL;
}

function cutTextChar($text, $length, $char = '.') {

    if (mb_strlen($text, 'utf8') <= $length) {
        return $text;
    }

    $p = explode($char, $text);
    $c = 0;
    $j = 0;

    for ($i = 0; $i < count($p); $i ++) {

        $c += mb_strlen($p[$i], 'utf8');

        if ($c <= $length) {
            $j = $i;
        }
    }

    if ($j) {
        return implode($char, array_splice($p, 0, $j + 1)) . $char;
    }

    return cutText($text, $length);
}

function cutText($text, $length, $endSuccess = '...') {

    if (mb_strlen($text, 'utf8') > $length) {

        return mb_substr($text, 0, $length, 'utf8') . $endSuccess;
    }

    return $text;
}

function assemblyFile($files, $key) {

    $file = array();

    foreach ($files as $k => $v) {
        $file[$k] = $files[$k][$key];
    }

    return $file;
}