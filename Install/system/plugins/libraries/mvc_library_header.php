<?php

class MVC_Library_Header
{
	public function allow($param = "*")
	{
		return header("Access-Control-Allow-Origin: {$param}");
	}

	public function redirect($location)
	{
		return die(header("location: $location"));
	}
}