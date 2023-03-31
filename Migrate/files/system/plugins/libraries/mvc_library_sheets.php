<?php

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class MVC_Library_Sheets 
{
	public function read($path)
	{
		$reader = ReaderEntityFactory::createXLSXReader();
		$reader->open($path);
		
		return $reader;
	}
}