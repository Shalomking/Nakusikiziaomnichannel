<?php

class MVC_Library_Hash {
	public function encode($val, $salt = false, $padding = 30)
	{
		$hashids = new Hashids\Hashids($salt, $padding);
		return $hashids->encode($val);
	}

	public function decode($hash, $salt = false, $padding = 30)
	{
		$hashids = new Hashids\Hashids($salt, $padding);
		return !empty($hashids->decode($hash)) ? (count($hashids->decode($hash)) > 1 ? $hashids->decode($hash) : $hashids->decode($hash)[0] ) : false;
	}
}