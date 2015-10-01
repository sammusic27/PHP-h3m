<?php
header('Content-Type: text/html; charset=utf-8');

?>

<style>
    body{
        font: 12px/15px Arial, Helvetica, sans-serif;
        color:#000:
    }
</style>
<?php

require_once 'php/MapH3M.php';

$file = new MapH3M('maps/Ascension.h3m');
$file->displayBaseInfoMap();

$file = new MapH3M('maps/clear.h3m');
$file->displayBaseInfoMap();

$file = new MapH3M('maps/Southern Cross.h3m');
$file->displayBaseInfoMap();

$file = new MapH3M('maps/[SAM]ResourceBattle.h3m');
$file->displayBaseInfoMap();