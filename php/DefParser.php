<?php
Class DefParser{
    private $version = 0.01;
    
    public $filename = '';
    public $file_size = 0;
    public $filecontent = '';
    public $index = 0;
    
    public function getVersion(){
        return $this->version;
    }
    
    public function __construct($filename){
        if(file_exists(realpath($filename)))
            {
            $this->filename = $filename;
            $this->readFile();
            $this->parse();
        }
    }
    
    public function readFile(){
        $handle = fopen($this->filename, "rb");
        $this->file_size = filesize($this->filename);
        $this->filecontent = fread($handle, $this->file_size);
        fclose($handle);
    }
    
    public function parse(){
        $def = array();
        $def['type'] = $this->getHex(4);
        $def['w'] = $this->getInteger(4);
        $def['h'] = $this->getInteger(4);
        $def['sequences'] = $this->getInteger(4);
        
        $test =
            $this->h3def_color_indexed();
//        $this->show_image($test, $def['w']);
        //echo'<pre>';print_r($test);die();
        
        $def['obj'] = array();
        $def['obj'][0]['type'] = $this->getInteger(4);
        $def['obj'][0]['length'] = $this->getInteger(4);
        //$def['obj'][0]['unknown1'] = $this->getInteger(4);
        //$def['obj'][0]['unknown2'] = $this->getInteger(4);
        $def['obj'][0]['unknown1'] = $this->getInteger2(4);
        $def['obj'][0]['unknown2'] = $this->getInteger2(4);

        $def['obj'][0]['name'] = $this->detectStr(15,'.pcx');
        
        
        $zero_count = 0;
        if(ord($this->filecontent[$this->index]) === 0){
            $this->index++;
            $zero_count++;
        }
        if(ord($this->filecontent[$this->index]) === 0){
            $this->index++;
            $zero_count++;
        }
        // after each name got 2 zero bytes and decrement for the first name
        $a = (strlen($def['obj'][0]['name']) + $zero_count) * ($def['obj'][0]['length']-1);
        //$this->debugger($a, 0, 1);
        $def['obj'][0]['offset'] = $this->getText($a);
        //$this->index = $this->index + 2;
//        struct h3def_frame_header {
//            uint32_t data_size;
//            uint32_t type;
//            uint32_t width;
//            uint32_t height;
//            uint32_t img_width;
//            uint32_t img_height;
//            uint32_t x;
//            uint32_t y;
//        };
//        
        
        $b = $def['obj'][0]['length'];

        $def['obj_frame'] = [];

            $def['obj_frame']['data_size'] = $this->getInteger2(4);
            $def['obj_frame']['type'] = $this->getInteger2(4);
            $def['obj_frame']['width'] = $this->getInteger2(4);
            $def['obj_frame']['height'] = $this->getInteger2(4);
            $def['obj_frame']['img_width'] = $this->getInteger2(4);
            $def['obj_frame']['img_height'] = $this->getInteger2(4);
            $def['obj_frame']['x'] = $this->getInteger2(4);
            $def['obj_frame']['y'] = $this->getInteger2(4);

            for($j = 0; $j < $def['obj'][0]['length']; $j++){
                $s = $this->h3def_color_indexed(256+32);
                $this->show_image($s, $def['w']);
            }



        
//        for($i = 0; $i < $b; $i++){
//            $def['obj_frame']['hm'][$i] = $this->getInteger2(4);
            //$def['obj_frame']['hm2'] += $def['obj_frame']['hm'][$i];
//        }
        //$this->debugger(1500, 1, 1);
        //$this->index = $this->index+100;
        //$this->index = $this->index+14*4;
//        $this->debugger(50, 1, 1);
        
//        $a = substr($this->filecontent, $this->index, 1024);
//        $a = $this->bmpTest($a);
        
//        echo'<pre>';print_r($a);die();


        echo '<pre>';
        print_r($def);
        die();
        
        
        
//        $s = $this->h3def_color_indexed(256+32);
//        $this->show_image($s, $def['w']);
//        $s = $this->h3def_color_indexed(256+32);
//        $this->show_image($s, $def['w']);
//        $s = $this->h3def_color_indexed(256+32);
//        $this->show_image($s, $def['w']);


        //echo'<pre>';print_r(1);die();
        //$this->debugger(1500, 1, 1);
//        echo'<pre>';print_r($this->index);
//        echo'<pre>';print_r($this->file_size);
//        echo'<pre>';print_r($this->file_size - $this->index);
        
//        $output = '';
//        for($i = 0; $i < 100;$i++){
//            $output[$i] = $this->getInteger2(1);
//        }
//        foreach($output as $key => $d){
//            if($key > 31 && $key % 32 === 0) echo '<br>';
//            //echo '<span style="color:#'.$test[$d]['r'].$test[$d]['g'].$test[$d]['b'].';">▓</span>';
//        }
        
//        echo'<pre>';print_r($output);
        
//        $images = array();
//        for($i = 0; $i < $b; $i++){
//            $images[$i] = $this->h3def_color_indexed(768);
//        }
//        echo'<pre>';print_r($images[0]);die();
//        $this->show_image( $images[0], $def['w']);
        
//        echo'<pre>';print_r($def);
        
//        for($i = 0; $i < $this->file_size; $i++){
//            echo chr(ord($this->filecontent[$i]));
//        }
    }
    
    public function bmpTest($read){
        $temp = unpack( "H*", $read );
        echo'<pre>';print_r($temp);die();
        $hex = $temp[1];
        $header = substr( $hex, 0, 104 );
        $body = str_split( substr( $hex, 108 ), 6 );
        echo'<pre>';print_r($header);die();
        //if( substr( $header, 0, 4 ) == "424d" )
        {
            //echo'<pre>';print_r(1);die();
            $header = substr( $header, 4 );
            // Remove some stuff?
            $header = substr( $header, 32 );
            // Get the width
            $width = hexdec( substr( $header, 0, 2 ) );
            // Remove some stuff?
            $header = substr( $header, 8 );
            // Get the height
            $height = hexdec( substr( $header, 0, 2 ) );
            unset( $header );
        }
        
        $x = 0;
        $y = 1;
        $image = imagecreatetruecolor( $width, $height );
        foreach( $body as $rgb )
        {
            $r = hexdec( substr( $rgb, 4, 2 ) );
            $g = hexdec( substr( $rgb, 2, 2 ) );
            $b = hexdec( substr( $rgb, 0, 2 ) );
            $color = imagecolorallocate( $image, $r, $g, $b );
            imagesetpixel( $image, $x, $height-$y, $color );
            $x++;
            if( $x >= $width )
            {
                $x = 0;
                $y++;
            }
        }
        return $image;
    }
    
    private function debugger($bytes,$die = false, $table = false){
        echo '--start debugger---<br>';
        $space = ' ';
        
        if($table){
            $counter = 0;
            $output = '<table border="1"><tr><th>count</th><th>ord</th><th>hex</th><th>str</th></tr>';
            for($i = $this->index; $i < $this->index + $bytes; $i++){
                if($counter > 3 && $counter % 4 === 0 ) $output .= '<tr><td colspan="4">---'.($counter/4).'---</td></tr>';
                $output .= '<tr><td>'.$counter.'/'.$i.'</td>'
                    . '<td>'.ord($this->filecontent[$i]).$space.'</td>'
                    . '<td>'.dechex(ord($this->filecontent[$i])).$space.'</td>'
                    . '<td>'.chr(ord($this->filecontent[$i])).$space.'</td></tr>';
                $counter++;
            }
            $output .= '</table>';
            echo $output;
        }else{
            
            echo '--1(ord)--<br>';
            for($i = $this->index; $i < $this->index + $bytes; $i++){
                echo ord($this->filecontent[$i]).$space;
            }
            echo '<br>--2(hex)--<br>';
            for($i = $this->index; $i < $this->index + $bytes; $i++){
                echo dechex(ord($this->filecontent[$i])).$space;
            }
            echo '<br>--3(str)--<br>';
            for($i = $this->index; $i < $this->index + $bytes; $i++){
                echo chr(ord($this->filecontent[$i])).$space;
            }
            
        }
        
        
        
        echo '<br>--end debugger---<br><br>';
        if($die) die();
    }
    
    private function show_image($col_arr = array(), $size = 0){
        $output = '';
        $count = 0;
        foreach($col_arr as $color){
            if($count > $size-1 && $count % $size === 0) $output .= '<br>';
            $output .= '<span style="color:#'.$color['r'].$color['g'].$color['b'].';">▓</span>';
            $count++;
        }
        echo $output.'<br><br>';
    }
    
    private function detectStr($bytes, $str = '.pcx'){
        $detected_str = '';
        for($i = $this->index; $i < $this->index + $bytes; $i++){
            if(strpos($detected_str,$str) !== false){
                break;
            }
            $detected_str .= chr(ord($this->filecontent[$i]));
        }
        $this->index = $this->index + strlen($detected_str);
        return $detected_str;
    }
    
    private function detectTypeDef(){
        $defType = '';
        switch($defType){
            // 0x40	Spell animation
            case '40000': break;
            //0x41	(Unused)
            case '41000': break;
            //0x42	Creature (combat screen)
            case '42000': break;
            //0x43	Map object
            case '43000': break;
            //0x44	Hero (map screen)
            case '44000': break;
            //0x45	Terrain texture
            case '45000': break;
            //0x46	Cursor
            case '46000': break;
            //0x47	Town screen buildings/game interface buttons
            case '47000': break;
            //0x48	(Unused)
            case '48000': break;
            //0x49	Hero (combat screen)
            case '49000': break;
            default: // error
        }
    }
    
    public function h3def_color_indexed($based = 768){
        $colors = array();
        // 256 * 3
        $counter = 0;
        for($i = $this->index; $i < $this->index+$based;$i = $i+3){
            $colors[$counter] = array(
                'r' => dechex(ord($this->filecontent[$i])),
                'g' => dechex(ord($this->filecontent[$i+1])),
                'b' => dechex(ord($this->filecontent[$i+2])),
            );
            $colors[$counter]['example'] = '<span style="color:#'.$colors[$counter]['r'].$colors[$counter]['g'].$colors[$counter]['b'].';">▓</span>';
            $counter++;
        }
        $this->index = $this->index + 768;
        return $colors;
    }
    
    private function getHex($bytes){
        $str_len = '';
        for($i = 0; $i < $bytes; $i++){
            $str_len .= dechex(ord($this->filecontent[$this->index]));$this->index++;
        }
        return $str_len;
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
    
    private function getText($bytes){
        $str = '';
        for($i = $this->index; $i < $this->index + $bytes; $i++){
            $str .= chr(ord($this->filecontent[$i]));
        }
        $this->index += $bytes;
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
    
    private function getInteger2($bytes){
        $str_len = 0;
        for($i = 0; $i < $bytes; $i++){
            $str_len += (ord($this->filecontent[$this->index]));
            $this->index++;
        }
        return $str_len;
    }
}