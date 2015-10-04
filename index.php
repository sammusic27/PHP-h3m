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

require_once 'php/functions.php';
require_once 'php/MapH3M.php';
require_once 'php/DefParser.php';

//$file = new MapH3M('game/maps/Ascension.h3m');
//$file->displayBaseInfoMap();

//$file = new MapH3M('game/maps/clear.h3m');
//$file->displayBaseInfoMap();
//
//$file = new MapH3M('game/maps/Southern Cross.h3m');
//$file->displayBaseInfoMap();
//
//$file = new MapH3M('game/maps/[SAM]ResourceBattle.h3m');
//$file->displayBaseInfoMap();



if(isset($_GET['test']) && $_GET['test'] == 1){
    $deff = new DefParser('game/defs/DIRTRD.DEF');
}else{
    $deff = new DefParser('game/defs/CLRRVR.DEF');
}
//$deff = new DefParser('defs/DIRTRD.DEF');
//$deff = new DefParser('defs/COBBRD.DEF');

echo'<pre>';print_r($deff->filecontent);die();
