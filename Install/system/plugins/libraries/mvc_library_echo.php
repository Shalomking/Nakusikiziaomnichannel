<?php
/**
 * TitanEcho
 * @author Titan Systems <mail@titansystems.ph>
 * @description Library for communicating with TitanEcho
 */

class MVC_Library_Echo
{
	public $_cache = false;
	public $_guzzle = false;

	public function token($refreshOldToken = false, $check = false)
	{
		$this->_cache->container("system.echo", true);

		if($refreshOldToken):
			try {
				json_decode($this->_guzzle->post(titansys_echo . "/delete", [
					"form_params" => [
		            	"purchase_code" => system_purchase_code,
		            	"token" => $refreshOldToken
		            ],
	                "allow_redirects" => true,
	                "http_errors" => false
	            ])->getBody()->getContents());

	            $this->_cache->delete("token");
			} catch(Exception $e){
				return false;
			}
		else:
			if($check):
				if($this->_cache->has("token")):
					try {
						$check = json_decode($this->_guzzle->post(titansys_echo . "/check", [
							"form_params" => [
				            	"purchase_code" => system_purchase_code,
				            	"token" => $this->_cache->get("token")
				            ],
			                "allow_redirects" => true,
			                "http_errors" => false
			            ])->getBody()->getContents());

			            if($check->status == 500):
			            	$this->_cache->delete("token");
			            endif;
					} catch(Exception $e){
						return false;
					}
				endif;
			endif;
		endif;

		if(!$this->_cache->has("token")):
			try {
				$token = json_decode($this->_guzzle->post(titansys_echo . "/token", [
					"form_params" => [
		            	"purchase_code" => system_purchase_code,
		            	"site_url" => site_url
		            ],
	                "allow_redirects" => true,
	                "http_errors" => false
	            ])->getBody()->getContents());

	            if($token->status == 200):
	            	$this->_cache->set("token", $token->data->token);
	            else:
	            	return false;
	            endif;
			} catch(Exception $e){
				return false;
			}
		endif;

		return $this->_cache->get("token");
	}

	public function notify($name, $content, $volatile = false)
	{
		$token = $this->token();

		try {
			$notify = json_decode($this->_guzzle->post(titansys_echo . "/notify", [
				"form_params" => [
					"token" => $token,
					"event_name" => $name,
					"event_content" => $content,
					"event_volatile" => $volatile
				],
                "allow_redirects" => true,
                "http_errors" => false
            ])->getBody()->getContents());

            if($notify->status == 200):
            	return true;
            else:
            	return false;
            endif;
		} catch(Exception $e){
			return false;
		}
	}

	public function status($socket_id)
	{
		$token = $this->token();

		try {
			$status = json_decode($this->_guzzle->post(titansys_echo . "/status", [
				"form_params" => [
					"token" => $token,
					"socket_id" => $socket_id,
				],
                "allow_redirects" => true,
                "http_errors" => false
            ])->getBody()->getContents());

            if($status->status == 200):
	            return $status->data->connected;
            else:
            	return false;
            endif;
		} catch(Exception $e){
			return false;
		}
	}
}