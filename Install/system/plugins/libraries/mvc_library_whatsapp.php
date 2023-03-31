<?php

class MVC_Library_Whatsapp 
{
	public $_guzzle = false;

	public function check()
	{
		try {
			$check = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
				"timeout" => 15,
				"connect_timeout" => 15,
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());
			
			if($check->status == 200):
				return true;
			else:
				return false;
			endif;
		} catch(Exception $e){
			return false;
		}
	}

	public function create($uid, $hash)
	{
		try {
        	$create = json_decode($this->_guzzle->post(system_wa_server . ":" . system_wa_port . "/accounts/create/" . system_purchase_code, [
        		"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
	            "form_params" => [
	            	"system_token" => system_token,
	            	"site_unique" => sha1(site_url),
	            	"site_url" => rtrim(site_url(false, true), "/"),
	            	"unique" => uniqid(time() . $hash),
	            	"uid" => $uid,
	            	"hash" => $hash,
	            	"os" => system_site_name
	            ],
	            "allow_redirects" => true,
	            "http_errors" => false,
                "verify" => false
	        ])->getBody()->getContents());

	        if($create->status == 200):
	        	return $create->data->qr;
	        else:
        		return false;
        	endif;
        } catch(Exception $e){
        	return false;
        }
	}

	public function update($account)
	{
		try {
			$update = json_decode($this->_guzzle->post(system_wa_server . ":" . system_wa_port . "/accounts/update/" . sha1(site_url) . "/{$account["unique"]}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
	            "form_params" => [
	            	"receive_chats" => $account["receive_chats"],
	            	"random_send" => $account["random_send"],
	            	"random_min" => $account["random_min"],
	            	"random_max" => $account["random_max"]
	            ],
	            "allow_redirects" => true,
	            "http_errors" => false,
                "verify" => false
	        ])->getBody()->getContents());

	    	return $update->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function delete($unique)
	{
		try {
			$delete = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/accounts/delete/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $delete->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function status($unique)
	{
		try {
			$status = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/accounts/status/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());
			
            return $status->data;
		} catch(Exception $e){
			return false;
		}
	}

	public function send($unique)
	{
		try {
			$send = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/send/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $send->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function delete_campaign($unique, $hash, $cid)
	{
		try {
			$delete = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/delete/campaign/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $delete->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function delete_chat($unique, $hash, $id)
	{
		try {
			$delete = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/delete/" . sha1(site_url) . "/{$unique}/{$hash}/{$id}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $delete->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function start_campaign($unique, $hash, $cid)
	{
		try {
			$start = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/campaign/start/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $start->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function stop_campaign($unique, $hash, $cid)
	{
		try {
			$stop = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/chats/campaign/stop/" . sha1(site_url) . "/{$unique}/{$hash}/{$cid}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents());

            return $stop->status;
		} catch(Exception $e){
			return false;
		}
	}

	public function get_groups($unique)
	{
		try {
			$groups = json_decode($this->_guzzle->get(system_wa_server . ":" . system_wa_port . "/accounts/groups/" . sha1(site_url) . "/{$unique}/" . system_purchase_code, [
				"headers" => [
					"ngrok-skip-browser-warning" => uniqid(),
					"Bypass-Tunnel-Reminder" => uniqid()
				],
                "allow_redirects" => true,
                "http_errors" => false,
                "verify" => false
            ])->getBody()->getContents(), true);

			if($groups["status"] == 200):
				return $groups["data"];
			else:
				return false;
			endif;
		} catch(Exception $e){
			return false;
		}
	}
}