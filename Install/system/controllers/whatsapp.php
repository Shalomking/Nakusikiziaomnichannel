<?php

class Whatsapp_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		response(404);
	}

	public function server()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["code"], $request["url"], $request["port"]))
			response(500);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

		if($request["code"] != system_purchase_code)
			response(500);

		if(!$this->sanitize->isUrl($request["url"]))
			response(500);

		$this->system->settings("wa_server", $request["url"]);
		$this->system->settings("wa_port", $request["port"]);
		$this->cache->clear();

		response(200);
	}

	public function messages()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["system_token"], $request["uid"], $request["hash"], $request["unique"]))
			response(500);

		if($request["system_token"] != system_token)
			response(500);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

		if($this->whatsapp->checkAccount($request["uid"], $request["unique"]) > 0):
			$subscription = set_subscription(
	            $this->system->checkSubscription($request["uid"]), 
	            $this->system->getSubscription(false, $request["uid"]), 
	            $this->system->getSubscription(false, false, true)
	        );

			if(empty($subscription))
				response(500);

			$account = $this->system->getWaAccount($request["uid"], $request["unique"], "unique");
			$messages = $this->whatsapp->getPendingMessages($request["uid"], $request["unique"]);

			if(!empty($messages)):
				$messageContainer = [];

				foreach($messages as $row):
					if(!limitation($subscription["wa_send_limit"], $this->system->countQuota($request["uid"], "wa_sent"))):
						$messageContainer[] = $row;

			        	$this->system->update($row["id"], false, "wa_sent", [
		        			"status" => 2
		        		]);

		        		$this->system->increment($request["uid"], "wa_sent");
		        	else:
		        		$this->system->update($row["id"], false, "wa_sent", [
		        			"status" => 4
		        		]);
		        	endif;
				endforeach;

				try {
					$this->echo->_cache = $this->cache;
					$this->echo->_guzzle = $this->guzzle;

					$echoToken = $this->echo->token();
				} catch(Exception $e){
					response(500);
				}

				if($echoToken):
					$this->echo->notify($request["hash"], [
						"type" => "table"
					]);
				endif;

				if(empty($messageContainer))
					response(500);

				response(200, false, [
					"site_name" => system_site_name,
					"receive_chats" => $account["receive_chats"],
					"random_send" => $account["random_send"],
					"random_min" => $account["random_min"],
					"random_max" => $account["random_max"],
					"messages" => $messageContainer
				]);
			else:
				response(500);
			endif;
		else:
			response(500);
		endif;
	}

	public function link()
	{
		$this->header->allow();

		$type = $this->url->segment(4);
		$request = $this->sanitize->array($_GET);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        try {
			$this->echo->_cache = $this->cache;
			$this->echo->_guzzle = $this->guzzle;

			$echoToken = $this->echo->token();
		} catch(Exception $e){
			response(500);
		}

        $this->cache->container("system.whatsapp", true);

		if($type == "success"):
			if(!isset($request["system_token"], $request["uid"], $request["wid"], $request["unique"]))
				response(500);

			if($request["system_token"] != system_token)
				response(500);

			date_default_timezone_set($this->whatsapp->getUserTimezone($request["uid"]));

			$filtered = [
				"uid" => $request["uid"],
				"wid" => $request["wid"],
				"unique" => $request["unique"],
				"receive_chats" => 1,
				"random_send" => 1,
				"random_min" => 1,
				"random_max" => 5,
				"create_date" => date("Y-m-d H:i:s", time())
			];

			$phone = explode(":", $request["wid"]);

			if(count($phone) > 1):
				if($this->whatsapp->checkWid($request["uid"], $phone[0]) < 1):
					if($this->system->create("wa_accounts", $filtered)):
						if(!empty(system_mailing_address) && in_array("admin_new_whatsapp", explode(",", system_mailing_triggers))):
							$userAccount = $this->system->getUser($request["uid"]);

							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that a new WhatsApp has been linked to account: <strong>{$userAccount["email"]}</strong></p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;

			    		if($echoToken):
				    		$this->echo->notify(md5($filtered["uid"]), [
								"type" => "whatsapp",
								"content" => ___(__("lang_whatsapp_accountlink_notifymessage"), ["<strong>+{$phone[0]}</strong>"])
							]);
				    	endif;

						response(200);
					else:
						response(500);
					endif;
				else:
					response(302);
				endif;
			else:
				response(500);
			endif;
		else:
			if(!isset($request["system_token"], $request["uid"], $request["hash"], $request["unique"]))
				response(500);

			if($request["system_token"] != system_token)
				response(500);

			if($this->system->checkWaAccount($request["uid"], $request["unique"], "unique") > 0):
				if($this->system->delete($request["uid"], false, "wa_accounts", [
					"`unique`" => $request["unique"]
				])):
					set_language($this->whatsapp->getUserLanguage($request["uid"]));

					$email = $this->whatsapp->getUserEmail($request["uid"]);

					$this->mail->send([
						"title" => system_site_name,
						"data" => [
							"subject" => mail_title(__("lang_mail_waunlink_title")),
							"unique" => $request["unique"]
						]
					], $email, "_mail/wa_unlink.tpl", $this->smarty);

					if($echoToken):
						$this->echo->notify($request["hash"], [
							"type" => "table"
						]);
					endif;

					response(200);
				else:
					response(500);
				endif;
			else:
				response(500);
			endif;
		endif;
	}

	public function sent()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["system_token"], $request["id"], $request["uid"], $request["hash"], $request["status"]))
			response(500);

		if($request["system_token"] != system_token)
			response(500);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language($this->whatsapp->getUserLanguage($request["uid"]));

        $subscription = set_subscription(
            $this->system->checkSubscription($request["uid"]), 
            $this->system->getSubscription(false, $request["uid"]), 
            $this->system->getSubscription(false, false, true)
        );

		if(empty($subscription))
			response(403);

		if($this->system->checkQuota($request["uid"]) < 1):
			$this->system->create("quota", [
				"uid" => $request["uid"],
				"sent" => 0,
				"received" => 0,
				"wa_sent" => 0,
				"wa_received" => 0,
				"ussd" => 0,
				"notifications" => 0
			]);
		endif;

		date_default_timezone_set($this->whatsapp->getUserTimezone($request["uid"]));

		$filtered = [
			"status" => $request["status"],
			"create_date" => date("Y-m-d H:i:s", time())
		];

		if($this->system->update($request["id"], $request["uid"], "wa_sent", $filtered)):
			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(403);
			}

			if($request["status"] == 3):
				if($echoToken):
					$this->echo->notify($request["hash"], [
						"type" => "message",
						"status" => 1,
						"content" => ___(__("lang_whatsapp_sent_sentsuccess"), ["<strong><a href=\"#\" class=\"text-warning\" zender-toggle=\"zender.view/wa.sent-{$request["id"]}\">#{$request["id"]}</a></strong>"])
					]);
				endif;
			else:
				if($echoToken):
					$this->echo->notify($request["hash"], [
						"type" => "message",
						"status" => 2,
						"content" => ___(__("lang_whatsapp_sent_sentfailed"), ["<strong><a href=\"#\" class=\"text-warning\" zender-toggle=\"zender.view/wa.sent-{$request["id"]}\">#{$request["id"]}</a></strong>"])
					]);
				endif;
			endif;

			$this->process->_sanitize = $this->sanitize;
			$this->process->_guzzle = $this->guzzle;
			$this->process->_lex = $this->lex;

			/**
			 * Process Action Hooks
			 */

			$whatsappChat = $this->whatsapp->getChat($request["id"]);

			if($whatsappChat):
				$hooks = $this->process->actionHooks($request["uid"], 2, 1, $whatsappChat["phone"], $whatsappChat["message"], $this->whatsapp->getActions($request["uid"], 2));

				if(!empty($hooks)):
					foreach($hooks as $hook):
						$this->system->create("events", [
							"uid" => $request["uid"],
							"type" => 2,
							"create_date" => date("Y-m-d H:i:s", time())
						]);
					endforeach;
				endif;
			endif;

			response(200);
		else:
			response(500);
		endif;
	}

	public function received()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["system_token"], $request["uid"], $request["hash"], $request["unique"], $request["phone"], $request["message"], $request["timestamp"]))
			response(500);

		if($request["system_token"] != system_token)
			response(500);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language($this->whatsapp->getUserLanguage($request["uid"]));

        $subscription = set_subscription(
            $this->system->checkSubscription($request["uid"]), 
            $this->system->getSubscription(false, $request["uid"]), 
            $this->system->getSubscription(false, false, true)
        );

		if(empty($subscription))
			response(403);

		if($this->system->checkQuota($request["uid"]) < 1):
			$this->system->create("quota", [
				"uid" => $request["uid"],
				"sent" => 0,
				"received" => 0,
				"wa_sent" => 0,
				"wa_received" => 0,
				"ussd" => 0,
				"notifications" => 0
			]);
		endif;

		date_default_timezone_set($this->whatsapp->getUserTimezone($request["uid"]));

		if(!limitation($subscription["receive_limit"], $this->system->countQuota($request["uid"], "wa_received"))):
			if($this->whatsapp->checkAccount($request["uid"], $request["unique"]) > 0):
				$account = $this->system->getWaAccount($request["uid"], $request["unique"], "unique");

				$filtered = [
					"uid" => $request["uid"],
					"wid" => $account["wid"],
					"unique" => $account["unique"],
					"phone" => "+{$request["phone"]}",
					"message" => $request["message"],
					"receive_date" => date("Y-m-d H:i:s", $request["timestamp"])
				];

				$received = $this->system->create("wa_received", $filtered);

				if($received):
					if(strtolower(substr($filtered["message"], 0, 4)) === "stop"):
						if($this->system->checkUnsubscribed($request["uid"], $filtered["phone"]) < 1):
							$this->system->create("unsubscribed", [
								"uid" => $request["uid"],
								"phone" => $filtered["phone"]
							]);
						endif;
		            endif;

					try {
						$this->echo->_cache = $this->cache;
						$this->echo->_guzzle = $this->guzzle;

						$echoToken = $this->echo->token();
					} catch(Exception $e){
						response(403);
					}

					if($echoToken):
						$this->echo->notify($request["hash"], [
							"type" => "message",
							"status" => 1,
							"content" => ___(__("lang_whatsapp_received_chat"), ["<strong><a href=\"#\" class=\"text-warning\" zender-toggle=\"zender.view/wa.received-{$received}\">#{$received}</a></strong>"])
						]);
					endif;

					$this->system->increment($request["uid"], "wa_received");

					$this->process->_sanitize = $this->sanitize;
					$this->process->_guzzle = $this->guzzle;
					$this->process->_lex = $this->lex;

					/**
					 * Process Webhooks
					 */

					$wid = explode(":", $filtered["wid"]);

					$webhooks = $this->process->webhooks($filtered["uid"], "whatsapp", [
						"id" => (int) $received,
						"wid" => "+{$wid[0]}",
						"phone" => $filtered["phone"],
						"message" => $filtered["message"],
						"timestamp" => strtotime($filtered["receive_date"])
					], $this->whatsapp->getWebhooks($filtered["uid"], "whatsapp"));

					if(!empty($webhooks)):
						foreach($webhooks as $webhook):
							$this->system->create("events", [
								"uid" => $filtered["uid"],
								"type" => 1,
								"create_date" => date("Y-m-d H:i:s", time())
							]);
						endforeach;
					endif;

					/**
					 * Process Action Hooks
					 */

					$hooks = $this->process->actionHooks($filtered["uid"], 2, 2, $filtered["phone"], $filtered["message"], $this->whatsapp->getActions($filtered["uid"], 2));

					if(!empty($hooks)):
						foreach($hooks as $hook):
							$this->system->create("events", [
								"uid" => $filtered["uid"],
								"type" => 2,
								"create_date" => date("Y-m-d H:i:s", time())
							]);
						endforeach;
					endif;

					/**
					 * Process Action Autoreplies
					 */

					$autoreplies = $this->process->actionAutoreplies($filtered["uid"], 2, $filtered["phone"], $filtered["message"], $this->whatsapp->getActions($filtered["uid"], 2));

					if(!empty($autoreplies)):
						foreach($autoreplies as $autoreply):
							$this->system->create("wa_sent", [
								"cid" => 0,
					        	"uid" => $filtered["uid"],
								"wid" => $account["wid"],
								"unique" => $account["unique"],
								"phone" => $filtered["phone"],
								"message" => json_encode([
									"text" => $autoreply["message"]
								]),
								"status" => 1,
								"api" => 2,
								"create_date" => date("Y-m-d H:i:s", time())
					        ]);

						    $this->system->create("events", [
								"uid" => $filtered["uid"],
								"type" => 2,
								"create_date" => date("Y-m-d H:i:s", time())
							]);

					    	$this->wa->_guzzle = $this->guzzle;

							$this->wa->send($account["unique"]);
						endforeach;

						if($echoToken):
							$this->echo->notify($request["hash"], [
								"type" => "table"
							]);
						endif;
					endif;

					response(200);
				else:
					response(500);
				endif;
			endif;
		endif;
	}
}