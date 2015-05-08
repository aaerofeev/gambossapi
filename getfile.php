<?php 
    header("http/1.1 302 Found");
    header("Location: http://gameboss.ru/getfile.php?url=".$_GET['url']);
?>