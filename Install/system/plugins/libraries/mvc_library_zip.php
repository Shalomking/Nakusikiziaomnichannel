<?php

class MVC_Library_Zip
{
	public function open($path, $outputPath)
	{
		$zip = new \PhpZip\ZipFile();
		return $zip->openFile($path)
			->extractTo($outputPath)
			->close(); 
	}

	public function create($output, $folder, $path)
	{
		$zip = new \PhpZip\ZipFile();
		return $zip->addDirRecursive($path, $folder) 
	        ->saveAsFile($output) 
	        ->close(); 
	}
}