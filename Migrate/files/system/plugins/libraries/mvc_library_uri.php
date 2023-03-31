<?php
/**
 * Name: MVC Framework
 * About: Titan Systems MVC Framework
 * Copyright: 2020, All Rights Reserved.
 * Author: Titan Systems <mail@titansystems.ph>
 */

class MVC_Library_URI
{
    public $path = null;
 
    public function __construct()
    {
        $this->path = mvc::instance()->url_segments;
    }
 
    public function segment($index)
    {
        if (!empty($this->path[$index-1])) {
            return isset(parse_url($this->path[$index-1])["path"]) ? parse_url($this->path[$index-1])["path"] : "/";
        } else {
            return false;
        }
    }
 
    public function uri_to_assoc($index)
    {
        $assoc = [];
        for ($x = count($this->path), $y=$index-1; $y<$x; $y+=2) {
            $assoc_idx = $this->path[$y];
            $assoc[$assoc_idx] = isset($this->path[$y+1]) ? $this->path[$y+1] : null;
        }
        return $assoc;
    }
 
    public function uri_to_array($index=0)
    {
        if (is_array($this->path)) {
            return array_slice($this->path, $index);
        } else {
            return false;
        }
    }
}
