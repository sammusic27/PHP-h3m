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
    private $version = 0.01;
    
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
            //case '33000': break;
            // ROE (RoS)
            case 'e000': 
                // все для считывания ROE
                $this->players['Red'] = $this->getPlayerParamsRos();
                $this->players['Blue'] = $this->getPlayerParamsRos();
                break;
            default: 
                // default map condition (by ROE)
                $this->players['Red'] = $this->getPlayerParamsRos();
                $this->players['Blue'] = $this->getPlayerParamsRos();
                break;
        }
    }
    
    public function getPlayerParamsRos(){
        // default code check
        echo '<br>====================<br>';
        for($i = $count; $i<= $count+13; $i++){
            echo ord($this->filecontent[$i]).' ';
        }
        echo '<br>====================<br>';
        // --------------------------------------
        
        $player['level_limit'] = 'no';
        //$player['level_limit'] = ord($this->filecontent[$this->index]);$this->index++; ??? disable for now
        $player['human'] = ord($this->filecontent[$this->index]);$this->index++;
        // if human is not playable - comp is init
        $player['comp'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['behavior'] = ord($this->filecontent[$this->index]);$this->index++;
        
        $player['towns_has_options'] = 'player\'s town'; // 
        //$player['towns_has_options'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['towns'] = '';
        if($player['towns_has_options']){
            $player['towns'] = sprintf("%08b",ord($this->filecontent[$this->index])).'('.ord($this->filecontent[$this->index]).')';$this->index++;
            $player['towns'] .=  ' '.sprintf("%08b",ord($this->filecontent[$this->index])).'('.ord($this->filecontent[$this->index]).')';$this->index++;
        }
        
        $player['random_town'] = '-';
        //$player['random_town'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_heroy_create'] = 'создается всегда';
        //$player['head_town_heroy_create'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_type_town'] = '';
        //$player['head_town_type_town'] = dechex(ord($this->filecontent[$this->index]));$this->index++;
        $player['head_town_x'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_y'] = ord($this->filecontent[$this->index]);$this->index++;
        $player['head_town_z'] = ord($this->filecontent[$this->index]);$this->index++;
        
        if($this->plColons){
            $this->plColons = array_keys($player);
        }
        
        return $player;
    }
    
    /**
     *  HARDCODE OUTPUT (only for now)
     */
    public function displayBaseInfoMap(){
        $output = '==============================='.'<br>';
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
        
        $output .= '<br>Players:<br>';
        $output .= '<table border="1" style="font-size:11px">';
        $output .= '<tr>'
            . '<th>Color</th>'
            . '<th>level limit</th>'
            . '<th>human</th>'
            . '<th>comp</th>'
            . '<th>behavior</th>'
            . '<th>towns has options</th>'
            . '<th>towns</th>'
            . '<th>random town</th>'
            . '<th>head town</th>'
            . '<th>head town heroy create</th>'
            . '<th>head town type town</th>'
            . '<th>head town x</th>'
            . '<th>head town y</th>'
            . '<th>head town z</th>'
            . '</tr>';
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
        
        echo $output;
    }
}
