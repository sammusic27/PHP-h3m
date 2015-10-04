<?php
Class MapH3M{
    private $version = 0.03;
    
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
    
    public $index = 0; // variable of a read map file
    
    public $playerColors = array('Red','Blue','Tan','Green','Orange','Purple','Teal','Pink');
    public $players = array();
    public $plColons = ''; // help variable (must be deleted)
    
    public $victoryCond = array();
    public $loosCond = array();
    public $teams = array();
    public $freeHeroes = array();
    public $artefacts = array();
    public $heroes = array();
    
    public $map = array();
    public $map_under = array();
    
    public function getVersion(){
        return $this->version;
    }
    
    public function __construct($filename){
        if(file_exists(realpath($filename)))
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
        $this->map_id = dechex(ord($this->filecontent[$this->index]));$this->index++;
        $this->map_id .= dechex(ord($this->filecontent[$this->index]));$this->index++;
        $this->map_id .= dechex(ord($this->filecontent[$this->index]));$this->index++;
        $this->map_id .= dechex(ord($this->filecontent[$this->index]));$this->index++;
        
        $this->heroy_is_here = (ord($this->filecontent[$this->index]));$this->index++;
        
        $this->map_size = $this->getInteger(4);
        $this->is_caves = (ord($this->filecontent[$this->index]));$this->index++;
        $this->name = $this->getString(4);
        $this->description = $this->getString(4);
        $this->difficalty = (ord($this->filecontent[$this->index]));$this->index++;
        
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
        // free 31 bytes (???)
        $this->freeBytes();
        // Artefacts
        //$this->artefacts();
        // Rumors
        $this->rumors();
        // Heroes Params ???
        //$this->heroyOptions();
        // Map
        $this->map();
        // Objects
        $this->objects();
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
        $output .= $this->tableArr($this->victoryCond, 'Special Victory Condition');
        
        // Special loss condition Start
        $output .= $this->tableArr($this->loosCond, 'Special loss condition Start');
        
        // Teams Start
        $output .= $this->tableArr($this->teams, 'Teams Start');
        
        // Free Heroes Start
        $output .= $this->tableArr($this->freeHeroes, 'Free Heroes');
        
        // artefacts Start
        $output .= $this->tableArr($this->artefacts, 'Artefacts');

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
        //$output .= '<br>Heroes Params :<br>';
        //$output .= $this->reverse($this->heroes);
        
        // Map
        $output .= '<br>Map :'.($this->map_size*$this->map_size * 7).'<br>';
        foreach($this->map as $key => $cell){
            if($key >= $this->map_size && $key % $this->map_size == 0) {
                $output .= '<br>';
            }
            switch($cell['surface']){ 
                case '0': $output .= '<span style="color:#503F0F;">█</span>'; break;
                case '1': $output .= '<span style="color:#DFCF8F;">█</span>'; break;
                case '2': $output .= '<span style="color:#004000;">█</span>'; break;
                case '3': $output .= '<span style="color:#B0C0C0;">█</span>'; break;
                case '4': $output .= '<span style="color:#4F806F;">█</span>'; break;
                case '5': $output .= '<span style="color:#807030;">█</span>'; break;
                case '6': $output .= '<span style="color:#008030;">█</span>'; break;
                case '7': $output .= '<span style="color:#4F4F4F;">█</span>'; break;
                case '8': $output .= '<span style="color:#0F5090;">█</span>'; break;
                case '9': $output .= '<span style="color:#000000;">█</span>'; break;
                default: $output .= '<span style="color:#000000;">E</span>';
            }
        }
        $output .= '<br>';
        if($this->is_caves){
           foreach($this->map_under as $key => $cell){
                if($key >= $this->map_size && $key % $this->map_size == 0) {
                    $output .= '<br>';
                }
                switch($cell['surface']){ 
                    case '0': $output .= '<span style="color:#503F0F;">█</span>'; break;
                    case '1': $output .= '<span style="color:#DFCF8F;">█</span>'; break;
                    case '2': $output .= '<span style="color:#004000;">█</span>'; break;
                    case '3': $output .= '<span style="color:#B0C0C0;">█</span>'; break;
                    case '4': $output .= '<span style="color:#4F806F;">█</span>'; break;
                    case '5': $output .= '<span style="color:#807030;">█</span>'; break;
                    case '6': $output .= '<span style="color:#008030;">█</span>'; break;
                    case '7': $output .= '<span style="color:#4F4F4F;">█</span>'; break;
                    case '8': $output .= '<span style="color:#0F5090;">█</span>'; break;
                    case '9': $output .= '<span style="color:#000000;">█</span>'; break;
                    default: $output .= '<span style="color:#000000;">E</span>';
                }
            } 
        }
        
        
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
    
    private function tableArr($arr, $name){
        $output = '<br>'.$name.' :<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>';
        $output .= '<th>'.$name.'</th><th></th>';
        $output .= '</tr>';
        $output .= '<tr>';
        foreach($arr as $option_name => $option_value){
            $output .= '<td>';
            $output .= $option_name.' : '.$option_value.'<br>';
            $output .= '</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        return $output;
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
        // ???
        //$this->freeHeroes['free_bytes'] = ord($this->filecontent[$this->index]);$this->index++;
        //$this->freeHeroes['free_bytes'] .= ord($this->filecontent[$this->index]);$this->index++;
        //$this->freeHeroes['free_bytes'] .= ord($this->filecontent[$this->index]);$this->index++;
        //$this->freeHeroes['free_bytes'] .= ord($this->filecontent[$this->index]);$this->index++;
        //$this->freeHeroes['count_heroes'] = ord($this->filecontent[$this->index]);$this->index++;
    }
    
    public function freeBytes($bytes = 31){
        $this->index += $bytes;
    }
    
    public function artefacts(){
        $artefacts = 16; // default 20
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

    public function map(){
        $count = $this->map_size*$this->map_size * 7;
        
        for($i = $this->index; $i < $this->index + $count; $i = $i+7){
            $this->map[] = array(
                'surface' => dechex(ord($this->filecontent[$i])),
                'surface_type' => dechex(ord($this->filecontent[$i+1])),
                'river' => dechex(ord($this->filecontent[$i+2])),
                'river_type' => dechex(ord($this->filecontent[$i+3])),
                'road' => dechex(ord($this->filecontent[$i+4])),
                'road_type' => dechex(ord($this->filecontent[$i+5])),
                'mirror' => dechex(ord($this->filecontent[$i+6]))
            );
        }
        $this->index += $count;
        
        if($this->is_caves){
            for($i = $this->index; $i < $this->index + $count; $i = $i+7){
                $this->map_under[] = array(
                    'surface' => dechex(ord($this->filecontent[$i])),
                    'surface_type' => dechex(ord($this->filecontent[$i+1])),
                    'river' => dechex(ord($this->filecontent[$i+2])),
                    'river_type' => dechex(ord($this->filecontent[$i+3])),
                    'road' => dechex(ord($this->filecontent[$i+4])),
                    'road_type' => dechex(ord($this->filecontent[$i+5])),
                    'mirror' => dechex(ord($this->filecontent[$i+6]))
                );
            }
            $this->index += $count;
        }
    }
    
    public function objects(){
//        echo $this->file;
        // default code check
//        echo '<br>====================<br>';
//        for($i = $this->index; $i<= $this->index+200; $i++){
//            echo chr(ord($this->filecontent[$i])).'';
//        }
//        echo '<br>====================<br>';
        // --------------------------------------
        
        
        $count = ord($this->filecontent[$this->index]);$this->index++;
        $count += ord($this->filecontent[$this->index]);$this->index++;
        $count += ord($this->filecontent[$this->index]);$this->index++;
        $count += ord($this->filecontent[$this->index]);$this->index++;

        $obj = array();
        
        for($i = 0; $i < $count; $i++){
            
            $obj[$i] = array();// 'id' => ord($this->filecontent[$this->index])
            $obj[$i]['name'] = $this->getString(4);
            // 1 - use, 0 - don't use
            // from right down corner
            $obj[$i]['use'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['use'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['use'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['use'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['use'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['use'][] = ord($this->filecontent[$this->index]); $this->index++;
            // 1 - active, 0 - don't active
            // from right down corner
            $obj[$i]['active'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['active'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['active'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['active'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['active'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['active'][] = ord($this->filecontent[$this->index]); $this->index++;
            // surface
            $obj[$i]['surface'][] = decbin(ord($this->filecontent[$this->index])); $this->index++;
            $obj[$i]['surface'][] = decbin(ord($this->filecontent[$this->index])); $this->index++;
            // surface categories
            $obj[$i]['surface_category'][] = decbin(ord($this->filecontent[$this->index])); $this->index++;
            $obj[$i]['surface_category'][] = decbin(ord($this->filecontent[$this->index])); $this->index++;
            // class
            $obj[$i]['class'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['class'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['class'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['class'][] = ord($this->filecontent[$this->index]); $this->index++;
            // number
            $obj[$i]['number'] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['number'] += ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['number'] += ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['number'] += ord($this->filecontent[$this->index]); $this->index++;
            // category
            // 0 - barrier
            // 1 - town, 2 - monsters, 3 - heroes
            // 4 - artefacts, 5 - treasures
            $obj[$i]['category'] = ord($this->filecontent[$this->index]); $this->index++;
            // show up or down
            $obj[$i]['show'] = ord($this->filecontent[$this->index]); $this->index++;
            // ???
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            $obj[$i]['hm'][] = ord($this->filecontent[$this->index]); $this->index++;
            
        }
        
//        echo'<pre>';
//        print_r($this->index);echo '<br>';
//        print_r($this->file_size);echo '<br>';
//        print_r($obj);
//        die();

//        $akk = strlen($this->filecontent);
//        $c = strlen($this->filecontent) - $this->index;
//        for($i = $this->index; $i < $akk+$c; $i++){
//            echo chr(ord($this->filecontent[$i])).' ';
//        }
//        echo '<br>';
//        for($i = $this->index; $i < $akk+$c; $i++){
//            echo (ord($this->filecontent[$i])).' ';
//        }
//        echo'<pre>';print_r(1);die();
        
        $count = ord($this->filecontent[$this->index]);$this->index++;
        $count += ord($this->filecontent[$this->index]);$this->index++;
        $count += ord($this->filecontent[$this->index]);$this->index++;
        $count += ord($this->filecontent[$this->index]);$this->index++;

        $obj_coord = array();
        for($i = 0; $i < $count; $i++){
            $obj_coord[$i]['x'] = ord($this->filecontent[$this->index]);$this->index++;
            $obj_coord[$i]['y'] = ord($this->filecontent[$this->index]);$this->index++;
            $obj_coord[$i]['z'] = ord($this->filecontent[$this->index]);$this->index++;
            $obj_coord[$i]['id'] = ord($this->filecontent[$this->index]);$this->index++;
            switch($obj[$obj_coord[$i]['id']]['category']){
                // 0 - barrier
                case '0': 
                    if(!$obj[$obj_coord[$i]['id']]['number']){
                        $bytes = 8;
                    }else{
                        $bytes = 0;
                        $obj_coord[$i]['obj'] = $this->getBuilding();
                    }
                    
                    break;
                // Town
                case '1': 
                        $bytes = 0; 
                        $obj_coord[$i]['obj'] = $this->getTown();
                    break;
                // Monster
                case '2': 
                        $bytes = 0;
                        $obj_coord[$i]['obj'] = $this->getMonster();
                    break;
                // Hero
                case '3': 
                        $bytes = 0;// 37; // ???
                        $obj_coord[$i]['obj'] = $this->getHero();
                    break;
                // Artefacts
                case '4': 
                    
                        $bytes = 0;
                        $obj_coord[$i]['obj'] = $this->getArtefact();
                    break;
                // Treasures
                case '5': $bytes = 8;
                    
                    break;
            }
            
            
            if($bytes > 0){
                for($j = 0; $j < $bytes; $j++){
                    $obj_coord[$i]['hm'][] = ord($this->filecontent[$this->index]);$this->index++;
                }
            }
        }
//        echo'<pre>';
//        print_r($obj);
//        print_r($obj_coord);
//        die();
    }
    
    private function getString($bytes){
        $str_len = 0;
        for($i = 0; $i < $bytes; $i++){
            $str_len += ord($this->filecontent[$this->index]);$this->index++;
        }
        $str = '';
        for($i = $this->index; $i < $this->index + $str_len; $i++){
            $str .= chr(ord($this->filecontent[$i]));
        }
        $this->index += $str_len;
        return $str;
    }
    
    private function getInteger($bytes){
        $str_len = '';
        for($i = 0; $i < $bytes; $i++){
            $str_len = sprintf("%08b",ord($this->filecontent[$this->index])).$str_len;
            //echo sprintf("%08b",ord($this->filecontent[$this->index])).'/';
            $this->index++;
        }
        return bindec($str_len);
    }
    
    private function getResource(){
        $res = array();
        $art['var0'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var1'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var2'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var3'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var4'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var5'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var6'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var7'] = ord($this->filecontent[$this->index]);$this->index++;
        return $res;
    }
    
    private function getArtefact(){
        $art = array();
        $art['var0'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var1'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var2'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var3'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var4'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var5'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var6'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['var7'] = ord($this->filecontent[$this->index]);$this->index++;
        $art['has_message'] = ord($this->filecontent[$this->index]);$this->index++;
        if($art['has_message']){
            $art['message'] = $this->getString(4);
            $art['has_garnizon'] = ord($this->filecontent[$this->index]);$this->index++;
            if($art['has_garnizon']){
                $art['garnizon'][0]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][0]['count'] = $this->getInteger(2);
                $art['garnizon'][1]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][1]['count'] = $this->getInteger(2);
                $art['garnizon'][2]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][2]['count'] = $this->getInteger(2);
                $art['garnizon'][3]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][3]['count'] = $this->getInteger(2);
                $art['garnizon'][4]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][4]['count'] = $this->getInteger(2);
                $art['garnizon'][5]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][5]['count'] = $this->getInteger(2);
                $art['garnizon'][6]['type'] = ord($this->filecontent[$this->index]);$this->index++;
                $art['garnizon'][6]['count'] = $this->getInteger(2);
            }
            
            $art['var9'] = ord($this->filecontent[$this->index]);$this->index++;
            $art['var10'] = ord($this->filecontent[$this->index]);$this->index++;
            $art['var11'] = ord($this->filecontent[$this->index]);$this->index++;
            $art['var12'] = ord($this->filecontent[$this->index]);$this->index++;
        }
        return $art;
    }
    
    private function getTown(){
        $town = array();
        $town['var0'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var1'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var2'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var3'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var4'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var5'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var6'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var7'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['player'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['has_name'] = ord($this->filecontent[$this->index]);$this->index++;
        if($town['has_name']){
            $town['name'] = $this->getString(4);
        }
        $town['has_garnizon'] = ord($this->filecontent[$this->index]);$this->index++;
        if($town['has_garnizon']){
            $town['garnizon'][0]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][0]['count'] = $this->getInteger(2);
            $town['garnizon'][1]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][1]['count'] = $this->getInteger(2);
            $town['garnizon'][2]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][2]['count'] = $this->getInteger(2);
            $town['garnizon'][3]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][3]['count'] = $this->getInteger(2);
            $town['garnizon'][4]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][4]['count'] = $this->getInteger(2);
            $town['garnizon'][5]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][5]['count'] = $this->getInteger(2);
            $town['garnizon'][6]['type'] = ord($this->filecontent[$this->index]);$this->index++;
            $town['garnizon'][6]['count'] = $this->getInteger(2);
        }
        $town['group'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['has_build'] = ord($this->filecontent[$this->index]);$this->index++;
        if($town['has_build']){
            $town['build'][0] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][1] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][2] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][3] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][4] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][5] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][6] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][7] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][8] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][9] = ord($this->filecontent[$this->index]);$this->index++;
            $town['build'][10] = ord($this->filecontent[$this->index]);$this->index++;
        }
        $town['fort'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var14'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var15'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var16'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var17'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var18'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var19'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var20'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var21'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var22'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var23'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var24'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var25'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var26'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var27'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var28'] = ord($this->filecontent[$this->index]);$this->index++;
        $town['var29'] = ord($this->filecontent[$this->index]);$this->index++;
        return $town;
    }
    
    private function getBuilding(){
        $build = array();
        $build['var0'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var1'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var2'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var3'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var4'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var5'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var6'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var7'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['player'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var9'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var10'] = ord($this->filecontent[$this->index]);$this->index++;
        $build['var11'] = ord($this->filecontent[$this->index]);$this->index++;
        return $build;
    }
    
    private function getMonster(){
        $monster = array();
        $monster['var0'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var1'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var2'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var3'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var4'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var5'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var6'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var7'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['count'] = $this->getInteger(2);
        $monster['aggression'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['has_message'] = ord($this->filecontent[$this->index]);$this->index++;
        if($monster['has_message']){
            $monster['message'] = $this->getString(4);
            // treasures
            $monster['wood'] = $this->getInteger(4);
            $monster['mercury'] = $this->getInteger(4);
            $monster['stone'] = $this->getInteger(4);
            $monster['sulfur'] = $this->getInteger(4);
            $monster['crystals'] = $this->getInteger(4);
            $monster['gems'] = $this->getInteger(4);
            $monster['gold'] = $this->getInteger(4);
            
            $monster['artefact'] = ord($this->filecontent[$this->index]);$this->index++;
        }
        $monster['not_escapes'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['fixed_count'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var14'] = ord($this->filecontent[$this->index]);$this->index++;
        $monster['var15'] = ord($this->filecontent[$this->index]);$this->index++;

        return $monster;
    }
    
    private function getHero(){
        $hero = array();
        $hero['var0'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var1'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var2'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var3'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var4'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var5'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var6'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var7'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['player'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['class'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['has_name'] = ord($this->filecontent[$this->index]);$this->index++;
        if($hero['has_name']){
            $hero['name'] = $this->getString(4);
        }
        $hero['exp'] = $this->getInteger(4);
        $hero['has_avatar'] = ord($this->filecontent[$this->index]);$this->index++;
        if($hero['has_avatar']){
            $hero['avatar_number'] = ord($this->filecontent[$this->index]);$this->index++;
        }
        $hero['var10'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var11'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var12'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var13'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var14'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var15'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var16'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var17'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var18'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var19'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var20'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var21'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var22'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var24'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var25'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var26'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var27'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var28'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var29'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var30'] = ord($this->filecontent[$this->index]);$this->index++;
        $hero['var31'] = ord($this->filecontent[$this->index]);$this->index++;
        return $hero;
    }
    
}