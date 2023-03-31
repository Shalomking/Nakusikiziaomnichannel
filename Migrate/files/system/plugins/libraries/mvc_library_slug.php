<?php

use Cocur\Slugify\Slugify;

class MVC_Library_Slug
{
	public function create($string)
	{
		$slugify = new Slugify();
		return $slugify->slugify($string, "-");
	}
}