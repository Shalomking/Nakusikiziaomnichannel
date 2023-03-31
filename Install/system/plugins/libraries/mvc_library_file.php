<?php

class MVC_Library_File
{
	public function put($file, $content)
	{
		return file_put_contents($file, $content);
	}

	public function get($file)
	{
		return file_get_contents($file);
	}

	public function delete($file)
	{
		if(file_exists($file))
			return unlink($file);
		else
			return false;
	}

	public function exists($file)
	{
		return file_exists($file);
	}

	public function move($old, $new)
	{
		return rename($old, $new);
	}
}