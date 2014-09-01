<?php

global $_win1251utf8;

$_win1251utf8 = array(
"\xC0"=>"\xD0\x90","\xC1"=>"\xD0\x91","\xC2"=>"\xD0\x92","\xC3"=>"\xD0\x93","\xC4"=>"\xD0\x94",
"\xC5"=>"\xD0\x95","\xA8"=>"\xD0\x81","\xC6"=>"\xD0\x96","\xC7"=>"\xD0\x97","\xC8"=>"\xD0\x98",
"\xC9"=>"\xD0\x99","\xCA"=>"\xD0\x9A","\xCB"=>"\xD0\x9B","\xCC"=>"\xD0\x9C","\xCD"=>"\xD0\x9D",
"\xCE"=>"\xD0\x9E","\xCF"=>"\xD0\x9F","\xD0"=>"\xD0\xA0","\xD1"=>"\xD0\xA1","\xD2"=>"\xD0\xA2",
"\xD3"=>"\xD0\xA3","\xD4"=>"\xD0\xA4","\xD5"=>"\xD0\xA5","\xD6"=>"\xD0\xA6","\xD7"=>"\xD0\xA7",
"\xD8"=>"\xD0\xA8","\xD9"=>"\xD0\xA9","\xDA"=>"\xD0\xAA","\xDB"=>"\xD0\xAB","\xDC"=>"\xD0\xAC",
"\xDD"=>"\xD0\xAD","\xDE"=>"\xD0\xAE","\xDF"=>"\xD0\xAF","\xAF"=>"\xD0\x87","\xB2"=>"\xD0\x86",
"\xAA"=>"\xD0\x84","\xA1"=>"\xD0\x8E","\xE0"=>"\xD0\xB0","\xE1"=>"\xD0\xB1","\xE2"=>"\xD0\xB2",
"\xE3"=>"\xD0\xB3","\xE4"=>"\xD0\xB4","\xE5"=>"\xD0\xB5","\xB8"=>"\xD1\x91","\xE6"=>"\xD0\xB6",
"\xE7"=>"\xD0\xB7","\xE8"=>"\xD0\xB8","\xE9"=>"\xD0\xB9","\xEA"=>"\xD0\xBA","\xEB"=>"\xD0\xBB",
"\xEC"=>"\xD0\xBC","\xED"=>"\xD0\xBD","\xEE"=>"\xD0\xBE","\xEF"=>"\xD0\xBF","\xF0"=>"\xD1\x80",
"\xF1"=>"\xD1\x81","\xF2"=>"\xD1\x82","\xF3"=>"\xD1\x83","\xF4"=>"\xD1\x84","\xF5"=>"\xD1\x85",
"\xF6"=>"\xD1\x86","\xF7"=>"\xD1\x87","\xF8"=>"\xD1\x88","\xF9"=>"\xD1\x89","\xFA"=>"\xD1\x8A",
"\xFB"=>"\xD1\x8B","\xFC"=>"\xD1\x8C","\xFD"=>"\xD1\x8D","\xFE"=>"\xD1\x8E","\xFF"=>"\xD1\x8F",
"\xB3"=>"\xD1\x96","\xBF"=>"\xD1\x97","\xBA"=>"\xD1\x94","\xA2"=>"\xD1\x9E");
function win1251_utf8($a) {
    global $_win1251utf8;
    if (is_array($a)){
        foreach ($a as $k=>$v) {
            if (is_array($v)) {
                $a[$k] = utf8_win1251($v);
            } else {
                $a[$k] = strtr($v, $_win1251utf8);
            }
        }
        return $a;
    } else {
        return strtr($a, $_win1251utf8);
    }
}


Class MapH3M{
    private $version = 0.02;
    
    public $file;
    public $file_size;
    public $filecontent = '';
    
    // 0E 00 00 00 - RoE
    // 15 00 00 00 - AB
    // 1C 00 00 00 - SoD
    public $map_id;
    public $heroy_is_here;
    public $map_size;
    public $is_caves = false;
    
    public $name = '';
    private $name_size = 0;
    public $description = '';
    private $description_size = 0;
    
    public $difficalty;
    // 0 - easy, 1 - normal, 2 - hard, 3 - expert, 4 - impossible
    
    public $index; // variable of a read map file
    
    public $players = array();
    public $plColons = ''; // help variable (must be deleted)
    
    public $victoryCond = array();
    public $loosCond = array();
    public $teams = array();
    public $freeHeroes = array();
    public $artefacts = array();
    public $heroes = array();
    
    public function getVersion(){
        return $this->version;
    }
    
    public function __construct($filename){
        if($filename)
            {
            $this->file = $filename;
            $this->readFile();
            $this->ungzip(); // map is in the gzip archive. unarchive it
            $this->parse();
            $this->displayBaseInfoMap();
        }
    }
    
    public function ungzip(){
        $this->filecontent = gzinflate(substr($this->filecontent,10,-8));
    }
    
    public function readFile(){
        $handle = fopen($this->file, "rb");
        $this->file_size = filesize($this->file);
        $this->filecontent = fread($handle, $this->file_size);
        fclose($handle);
    }
    
    public function parse(){
        # ----------------------------- #
        #    1 global options of map    #
        # ----------------------------- #
        $count = 0;
        for($i = 0; $i < $this->file_size; $i++){
            $asciiCharacter = $this->filecontent[$i];
            $base10value = ord($asciiCharacter);
            if($i < 4){
                $this->map_id .= dechex($base10value);
            }
            // heroy on the map (???)
            if($i == 4){
                $this->heroy_is_here = $base10value;
            }

            // width and height of map
            if($i > 4 && $i < 9){
                $this->map_size = $this->map_size + $base10value;
            }
            // with caves or without
            if($i === 9){
                if($base10value){
                    $this->is_caves = true;
                }
            }
            // how long is name
            if($i >  9 && $i < 14){
                $this->name_size = $this->name_size + $base10value;
            }
            // get all name
            if($i > 13 && $i < (14 + $this->name_size)){
                $this->name .= chr($base10value);
            }
            // how long is description
            if($i >  13 + $this->name_size && $i <  18 + $this->name_size){
                $this->description_size = $this->description_size + $base10value;
            }
            // get all description
            if($i > 17+ $this->name_size && $i < 17+ $this->name_size +  $this->description_size){
                $this->description .= chr($base10value);
            }
            
            if($i === (18 + $this->name_size + $this->description_size)){
                $this->difficalty = $base10value;
                $this->index = ++$i;
                break;
            }
        }
        
        $this->detectMapId();
    }
    
    // parameters of players is depends from map type (i guess)
    public function detectMapId(){
        # Red, Blue, Tan, Green, Orange, Purple, Teal, Pink
        switch($this->map_id){
            // commented for now
            
            // SoD 
            //case '1c000': break;
            // AB 
            //case '15000': break;
            // wog
            case '33000': 
                $this->players['Red'] = $this->getPlayerParamsWog();
                $this->players['Blue'] = $this->getPlayerParamsWog();
                $this->players['Tan'] = $this->getPlayerParamsWog();
                $this->players['Green'] = $this->getPlayerParamsWog();
                $this->players['Orange'] = $this->getPlayerParamsWog();
                $this->players['Purple'] = $this->getPlayerParamsWog();
                $this->players['Teal'] = $this->getPlayerParamsWog();
                $this->players['Pink'] = $this->getPlayerParamsWog();
                break;
            // ROE (RoS)
            case 'e000': 
                // все для считывания ROE
                $this->players['Red'] = $this->getPlayerParamsRos();
                $this->players['Blue'] = $this->getPlayerParamsRos();
                $this->players['Tan'] = $this->getPlayerParamsRos();
                $this->players['Green'] = $this->getPlayerParamsRos();
                $this->players['Orange'] = $this->getPlayerParamsRos();
                $this->players['Purple'] = $this->getPlayerParamsRos();
                $this->players['Teal'] = $this->getPlayerParamsRos();
                $this->players['Pink'] = $this->getPlayerParamsRos();
                break;
            default: 
                // default map condition (by ROE)
                $this->players['Red'] = $this->getPlayerParamsRos();
                $this->players['Blue'] = $this->getPlayerParamsRos();
                $this->players['Tan'] = $this->getPlayerParamsRos();
                $this->players['Green'] = $this->getPlayerParamsRos();
                $this->players['Orange'] = $this->getPlayerParamsRos();
                $this->players['Purple'] = $this->getPlayerParamsRos();
                $this->players['Teal'] = $this->getPlayerParamsRos();
                $this->players['Pink'] = $this->getPlayerParamsRos();
                break;
        }
        // Special Victory Condition
        $this->victoryCondition();
        // Special loss condition
        $this->loosCondition();
        // Teams
        $this->teams();
        // Free Heroes
        $this->freeHeroes();
        // free 31 bytes ???
        $this->freeBytes();
        // Artefacts
        $this->artefacts();
        // Rumors
        $this->rumors();
        // Heroes Params
        $this->heroyOptions();
    }
    
    
    public function getPlayerParamsWog(){
        // default code check
        echo '<br>====================<br>';
        for($i = $this->index; $i<= $this->index+50; $i++){
            echo ord($this->filecontent[$i]).' ';
        }
        echo '<br>====================<br>';
        // --------------------------------------
        
        $player['level_limit'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['human'] = ord($this->filecontent[$this->index]);$this->index++;
        
        $player['comp'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['behavior'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['towns_has_options'] = ord($this->filecontent[$this->index]);$this->index++;
        
        $player['towns'] = sprintf("%08b",ord($this->filecontent[$this->index])).'('.ord($this->filecontent[$this->index]).')';$this->index++;
        $player['towns'] .=  ' '.sprintf("%08b",ord($this->filecontent[$this->index])).'('.ord($this->filecontent[$this->index]).')';$this->index++;
        
        $player['random_town'] = ord($this->filecontent[$this->index]);$this->index++;
        
        
        
        $player['head_town'] = ord($this->filecontent[$this->index]);$this->index++;

        $player['head_town_heroy_create'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_type_town'] = dechex(ord($this->filecontent[$this->index]));$this->index++;
        
        $player['head_town_x'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_y'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_z'] = ord($this->filecontent[$this->index]);$this->index++;
        
        $player['hm'] = ord($this->filecontent[$this->index]);$this->index++;
        
        if($this->plColons == ''){
            $this->plColons = array_keys($player);
        }
        
        return $player;
    }
    
    /**
     * Just Finished
     */
    public function getPlayerParamsRos(){
        //$player['level_limit'] = '(none)';
        // 1-3
        $player['human'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['comp'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['behavior'] = ord($this->filecontent[$this->index]);$this->index++;
        // 4-5
        $player['towns'] = sprintf("%08b",ord($this->filecontent[$this->index])).'('.ord($this->filecontent[$this->index]).')';$this->index++;
        $player['towns'] .=  ' '.sprintf("%08b",ord($this->filecontent[$this->index])).'('.ord($this->filecontent[$this->index]).')';$this->index++;
        
        //$player['random_town'] = '';
        $player['head_town'] = ord($this->filecontent[$this->index]);$this->index++;
        
        $player['head_town_x'] = '';
        $player['head_town_y'] = '';
        $player['head_town_z'] = '';
        if($player['head_town']){
            $player['head_town_x'] = ord($this->filecontent[$this->index]);$this->index++;
            $player['head_town_y'] = ord($this->filecontent[$this->index]);$this->index++;
            $player['head_town_z'] = ord($this->filecontent[$this->index]);$this->index++;
        }
        
        $player['create_hero'] = ord($this->filecontent[$this->index]);$this->index++;
        
        //$player['head_town_heroy_create'] = '(1)';
        
        $player['heroes'] = dechex(ord($this->filecontent[$this->index]));$this->index++;
        if($player['heroes'] != 'ff'){
            $player['hero_avatar'] = (ord($this->filecontent[$this->index]));$this->index++;    
            $player['name_length'] = (ord($this->filecontent[$this->index]));$this->index++;
            $player['name'] = '';
            if($player['name_length'] != 0){
                $player['name_length'] += (ord($this->filecontent[$this->index]));$this->index++;
                $player['name_length'] += (ord($this->filecontent[$this->index]));$this->index++;
                $player['name_length'] += (ord($this->filecontent[$this->index]));$this->index++;
                
                for($i = $this->index; $i<$this->index+$player['name_length']; $i++){
                    $player['name'] .= chr(ord($this->filecontent[$i]));
                }
                $this->index = $this->index + $player['name_length'];
            }
            if($player['name'] == ''){
                $player['var1'] = (ord($this->filecontent[$this->index]));$this->index++;
                $player['var2'] = (ord($this->filecontent[$this->index]));$this->index++;    
                $player['var3'] = (ord($this->filecontent[$this->index]));$this->index++;    
            }
        }
        
        if($this->plColons == ''){
            $this->plColons = array_keys($player);
        }
        
        return $player;
    }
    
    /**
     *  HARDCODE OUTPUT (only for now)
     */
    public function displayBaseInfoMap(){
        $output = '==============================='.'<br>';
        $output .= $this->file_size.'('.$this->index.')'.'<br>';
        $output .= '==============================='.'<br>';
        $output .= $this->file.'('.$this->map_id.')'.'<br>';
        $output .= '==============================='.'<br>';
        
        $output .= 'Heroy on the map: '.$this->heroy_is_here.'<br>';
        $output .= 'Size: '.$this->map_size.'&times;'.$this->map_size.'<br>';
        $output .= 'Podzemelie: '.(($this->is_caves) ? 'yes' : 'no' ).'<br>';
        
        $output .= 'Name: '.win1251_utf8($this->name).' ('.$this->name_size.')'.'<br>';
        $output .= 'Description: '.win1251_utf8($this->description).' ('.$this->description_size.')'.'<br>';
        
        $output .= 'Difficulty: ';
        switch($this->difficalty){
            case '0': $output .= 'Easy';break;
            case '1': $output .= 'Normal';break;
            case '2': $output .= 'Hard';break;
            case '3': $output .= 'Expert';break;
            case '4': $output .= 'Impossible';break;
        }
        $output .= '<br>';
        
        // Players Start
        $output .= '<br>Players:<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>Colors</th>';
        foreach($this->plColons as $name){
            $output .= '<th>'.str_replace('_',' ',$name).'</th>';
        }
        $output .= '</tr>';
        foreach($this->players as $color => $player){
            $output .= '<tr><td>';
            $output .= '<strong>'.ucfirst($color).'</strong><br>';
            $output .= '</td>';
            foreach($player as $option_name => $option_value){
                $output .= '<td>';
                $output .= $option_value;
                $output .= '</td>';
            }
            $output .= '</tr>';
        }
        $output .= '</table>';
        
        
        // Special Victory condition Start
        $output .= '<br>Special Victory Condition :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>Colors</th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        foreach($this->victoryCond as $option_name => $option_value){
            $output .= '<td>';
            $output .= $option_name.' : '.$option_value.'<br>';
            $output .= '</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        
        
        // Special loss condition Start
        $output .= '<br>Special loss Condition :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>Colors</th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        foreach($this->loosCond as $option_name => $option_value){
            $output .= '<td>';
            $output .= $option_name.' : '.$option_value.'<br>';
            $output .= '</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        
        
        // Teams Start
        $output .= '<br>Teams :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>Teams</th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        foreach($this->teams as $option_name => $option_value){
            $output .= '<td>';
            $output .= $option_name.' : '.$option_value.'<br>';
            $output .= '</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
       
        
        // Free Heroes Start
        $output .= '<br>Free Heroes :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th></th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        foreach($this->freeHeroes as $option_name => $option_value){
            $output .= '<td>';
            $output .= $option_name.' : '.$option_value.'<br>';
            $output .= '</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        
        
        // artefacts Start
        $output .= '<br>Artefacts :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>Artefacts</th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        foreach($this->artefacts as $option_name => $option_value){
            $output .= '<td>';
            $output .= $option_name.' : '.$option_value.'<br>';
            $output .= '</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        
        // rumors Start
        $output .= '<br>Rumors :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>Rumors</th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        $output .= '<td>Count</td>';
        $output .= '<td>'.$this->rumors['count'].'</td>';
        $output .= '</tr>';
        
        if(isset($this->rumors['all'])){
            $output .= '<tr>';
            foreach($this->rumors['all'] as $option_name => $option_value){
                $output .= '<td>';
                $output .= $option_value['name'].' : '.$option_value['desc'];
                $output .= '</td>';
            }
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        
        // Heroes Params Start
        $output .= '<br>Heroes Params :<br>';
        $output .= $this->reverse($this->heroes);
        
        echo $output;
    }
    
    private function reverse($arr){
        $out = '';
        foreach($arr as $key => $val){
            if(gettype($val) === 'array'){
                $out .= $key .' : ' . $this->reverse($val).' ';
            }else{
                $out .= $key .' : ' . $val.' ';
            }
        }
        return $out.'<br>';
    }
    
    public function victoryCondition(){
        // 1    Special Victory Condition:
        $this->victoryCond['type'] = dechex(ord($this->filecontent[$this->index]));$this->index++;
        if($this->victoryCond['type'] == 'ff') {
            $this->victoryCond['name'] = 'None';
            return;
        }
        
        $this->victoryCond['usial_end'] = ord($this->filecontent[$this->index]);$this->index++;
        $this->victoryCond['comp_has'] = ord($this->filecontent[$this->index]);$this->index++;

        switch($this->victoryCond['type']){
            case 'ff': break; // not
            case '0': // 00 - Acquire a specific artifact
                $this->victoryCond['name'] = 'Acquire a specific artifact';
                $this->victoryCond['art'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '1': // 01 - Accumulate creatures
                $this->victoryCond['name'] = 'Accumulate creatures';
                $this->victoryCond['unit'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['unit_count'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '2': // 02 - Accumulate resources
                $this->victoryCond['name'] = 'Accumulate resources';
                $this->victoryCond['resource'] = ord($this->filecontent[$this->index]);$this->index++;
                // 0 - Wood     4 - Crystal
                // 1 - Mercury  5 - Gems   
                // 2 - Ore      6 - Gold   
                // 3 - Sulfur            
                $this->victoryCond['resource_count'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '3': // 03 - Upgrade a specific town
                $this->victoryCond['name'] = 'Upgrade a specific town';
                $this->victoryCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['hall_lvl'] = ord($this->filecontent[$this->index]);$this->index++;
                // Hall Level:   0-Town, 1-City,    2-Capitol
                $this->victoryCond['castle_lvl'] = ord($this->filecontent[$this->index]);$this->index++;
                // Castle Level: 0-Fort, 1-Citadel, 2-Castle
                break;
            case '4': // 04 - Build the grail structure
                $this->victoryCond['name'] = 'Build the grail structure';
                $this->victoryCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '5': // 05 - Defeat a specific Hero
                $this->victoryCond['name'] = 'Defeat a specific Hero';
                $this->victoryCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '6': // 06 - Capture a specific town
                $this->victoryCond['name'] = 'Capture a specific town';
                $this->victoryCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '7': // 07 - Defeat a specific monster
                $this->victoryCond['name'] = 'Defeat a specific monster';
                $this->victoryCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '8': // 08 - Flag all creature dwelling
                $this->victoryCond['name'] = 'Flag all creature dwelling';
                break;
            case '9': // 09 - Flag all mines
                $this->victoryCond['name'] = 'Flag all mines';
                break;
            case 'a': // 0A - Transport a specific artifact
                $this->victoryCond['name'] = 'Transport a specific artifact';
                $this->victoryCond['art'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->victoryCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            default: // ff - not
        }
   
    }
    
    public function loosCondition(){
        // 1    Special loss condition       
        $this->loosCond['type'] = dechex(ord($this->filecontent[$this->index]));$this->index++;
        if($this->loosCond['type'] == 'ff') {
            $this->loosCond['name'] = 'None';
            return;
        }

        switch($this->loosCond['type']){
            case 'ff': break; // not
            case '0': // 00 - Lose a specific town
                $this->loosCond['name'] = 'Lose a specific town';
                $this->loosCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->loosCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->loosCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '1': // 01 - Lose a specific hero
                $this->loosCond['name'] = 'Lose a specific hero';
                $this->loosCond['x'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->loosCond['y'] = ord($this->filecontent[$this->index]);$this->index++;
                $this->loosCond['z'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            case '2': // 02 - Accumulate resources
                $this->loosCond['name'] = 'Time expires';
                $this->loosCond['resource'] = ord($this->filecontent[$this->index]);$this->index++;
                break;
            default: // ff - not
        }
    }
    
    public function teams(){
        $this->teams['type'] = ord($this->filecontent[$this->index]);$this->index++;
        if($this->teams['type']){
            $this->teams['red'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['blue'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['tan'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['green'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['orange'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['purple'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['teal'] = ord($this->filecontent[$this->index]);$this->index++;
            $this->teams['pink'] = ord($this->filecontent[$this->index]);$this->index++;
        }
    }
    
    public function freeHeroes(){
        $heroes = 16; // default 20
        for($i = $this->index; $i<$this->index+$heroes; $i++){
            $this->freeHeroes['all_heroes'] .= ' / '.decbin(ord($this->filecontent[$i]));
        }
        $this->index += $heroes;
        $this->freeHeroes['free_bytes'] = ord($this->filecontent[$this->index]);$this->index++;
        $this->freeHeroes['free_bytes'] .= ord($this->filecontent[$this->index]);$this->index++;
        $this->freeHeroes['free_bytes'] .= ord($this->filecontent[$this->index]);$this->index++;
        $this->freeHeroes['free_bytes'] .= ord($this->filecontent[$this->index]);$this->index++;
        
        $this->freeHeroes['count_heroes'] = ord($this->filecontent[$this->index]);$this->index++;
    }
    
    public function freeBytes($heroes = 31){
        //$heroes = 31; // default 31
//        for($i = $this->index; $i<$this->index+$heroes; $i++){
//            $this->freeHeroes['clear_bytes'] .= ' / '.ord($this->filecontent[$i]);
//        }
        $this->index += $heroes;
    }
    
    public function artefacts(){
        $artefacts = 18; // default 20
        for($i = $this->index; $i<$this->index+$artefacts; $i++){
            $this->artefacts['all_arts'] .= ' / '.decbin(ord($this->filecontent[$i]));
        }
        $this->index += $artefacts;
    }
    
    public function rumors(){
        $this->rumors['count'] = $this->filecontent[$this->index];$this->index++;
        $this->rumors['count'] += $this->filecontent[$this->index];$this->index++;
        $this->rumors['count'] += $this->filecontent[$this->index];$this->index++;
        $this->rumors['count'] += $this->filecontent[$this->index];$this->index++;
        if($this->rumors['count']){
            for($i = $this->rumors['count']; $i<$this->index+$this->rumors['count']; $i++){
                $rumor = array();
                $name_i = 0;
                $rumor['name_length'] = $this->filecontent[$this->index];$this->index++;
                $rumor['name_length'] += $this->filecontent[$this->index];$this->index++;
                $rumor['name_length'] += $this->filecontent[$this->index];$this->index++;
                $rumor['name_length'] += $this->filecontent[$this->index];$this->index++;
                for($name_i = $rumor['name_length']; $name_i < $this->index+$rumor['name_length']; $name_i++)
                {
                    $rumor['name'] .= $this->filecontent[$name_i];
                }
                $this->index += $name_i;
                
                $rumor['desc_length'] = ord($this->filecontent[$this->index]);$this->index++;
                $rumor['desc_length'] += ord($this->filecontent[$this->index]);$this->index++;
                $rumor['desc_length'] += ord($this->filecontent[$this->index]);$this->index++;
                $rumor['desc_length'] += ord($this->filecontent[$this->index]);$this->index++;
                for($name_i = $rumor['desc_length']; $name_i < $this->index+$rumor['desc_length']; $name_i++)
                {
                    $rumor['desc'] .= $this->filecontent[$name_i];
                }
                $this->index += $name_i;
             
                $this->rumors['all'][] = $rumor;
            }
        }
    }
    
    public function heroyOptions(){
        // 156
        $count = 156;
        for($i = 0; $i < $count; $i++){
            $this->heroes[$i]['on'] = ord($this->filecontent[$this->index]);$this->index++;
            $heroy = array();
            if($this->heroes[$i]['on']){
                // expirience
                $heroy['exp_on'] = ord($this->filecontent[$this->index]);$this->index++;
                if($heroy['exp_on'] > 0){
                    $heroy['exp'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['exp'] += ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['exp'] += ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['exp'] += ord($this->filecontent[$this->index]);$this->index++;
                }
                // secondary skills
                $heroy['skill_sec_on'] = ord($this->filecontent[$this->index]);$this->index++;
                $heroy['skills'] = array();
                if($heroy['skill_sec_on'] > 0){
                    $heroy['skill_count'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['skill_count'] += ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['skill_count'] += ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['skill_count'] += ord($this->filecontent[$this->index]);$this->index++;
                    $skills = array();
                    for($skill_count = 0; $skill_count < $heroy['skill_count']; $skill_count++)
                    {
                        $skills[] = array(
                            'skill_id' => ord($this->filecontent[$this->index]),
                            'skill_lvl' => ord($this->filecontent[$this->index+1]),
                        );
                        // skill_lvl 0 - base, 1 - advanced, 2 - expert
                        $this->index += 2;
                    }
                    $heroy['skills'] = $skills;
                }
                // artefacts
                $heroy['artefacts_on'] = ord($this->filecontent[$this->index]);$this->index++;
                $heroy['artefacts'] = array();
                if($heroy['artefacts_on'] > 0){
                    $artefacts = array();
                    $artefacts['head'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['head'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['shoulders'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['shoulders'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['neck'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['neck'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['right_hand'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['right_hand'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['left_hand'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['left_hand'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['body'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['body'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['right_ring'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['right_ring'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['left_ring'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['left_ring'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['legs'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['legs'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['other1'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other1'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other2'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other2'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other3'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other3'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other4'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other4'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['machine1'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine1'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine2'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine2'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine3'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine3'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine4'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['machine4'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['book'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['book'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['other5'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    $artefacts['other5'][] = dechex(ord($this->filecontent[$this->index]));$this->index++;
                    
                    $artefacts['bag_count'] = ord($this->filecontent[$this->index]);$this->index++;
                    $artefacts['bag_count'] += ord($this->filecontent[$this->index]);$this->index++;
                    if($artefacts['bag_count'] > 0)
                    {
                        for($bag_count = 0;$bag_count < $artefacts['bag_count']; $bag_count++)
                        {
                            $artefacts['bag'][$bag_count][] = ord($this->filecontent[$this->index]);$this->index++;
                            $artefacts['bag'][$bag_count][] = ord($this->filecontent[$this->index]);$this->index++;
                        }
                    }
                    $heroy['artefacts'] = $artefacts;
                }
                // bio
                $heroy['bio_on'] = ord($this->filecontent[$this->index]);$this->index++;
                $heroy['bio'] = '';
                if($heroy['bio_on'] > 0){
                    $heroy['bio_length'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['bio_length'] += ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['bio_length'] += ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['bio_length'] += ord($this->filecontent[$this->index]);$this->index++;
                    for($bio_length = 0; $bio_length < $heroy['bio_length']; $bio_length++)
                    {
                       $heroy['bio'] .= chr(ord($this->filecontent[$this->index]));$this->index++;
                    }
                }
                // gender
                $heroy['gender_on'] = ord($this->filecontent[$this->index]);$this->index++;
                $heroy['gender'] = 'ff';
                if($heroy['gender_on'] > 0){
                    $heroy['gender'] = ord($this->filecontent[$this->index]);$this->index++;
                }
                // spells
                $heroy['spells_on'] = ord($this->filecontent[$this->index]);$this->index++;
                $heroy['spells'] = '';
                if($heroy['spells_on'] > 0){
                    $heroy['spells'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['spells'] .= ord($this->filecontent[$this->index]);$this->index++;
                }
                // first skills
                $heroy['skills_first_on'] = ord($this->filecontent[$this->index]);$this->index++;
                $heroy['skills_first'] = array();
                if($heroy['skills_first_on'] > 0){
                    $heroy['attack'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['defence'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['strength'] = ord($this->filecontent[$this->index]);$this->index++;
                    $heroy['science'] = ord($this->filecontent[$this->index]);$this->index++;
                }
            }
            $this->heroes[$i]['params'] = $heroy;
        }
        
        
    }
}
