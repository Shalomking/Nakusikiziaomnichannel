<?php

class MVC_Library_Smarty extends Smarty
{
    public function __construct()
    {
    	parent::__construct();
        $this->setTemplateDir("templates/")
	        ->setCompileDir("system/storage/smarty/compiled/")
	        ->setConfigDir("system/storage/smarty/configs/")
	        ->setCacheDir("system/storage/smarty/cache/");
    }
}
