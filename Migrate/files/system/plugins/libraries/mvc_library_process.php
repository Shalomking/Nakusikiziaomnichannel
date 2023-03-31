<?php

class MVC_Library_Process 
{
	public $_sanitize = false;
	public $_guzzle = false;
	public $_lex = false;

	public function webhooks($uid, $type, $payload, $webhooks)
	{
		$webhookArray = [];

		if(!empty($webhooks)):
			foreach($webhooks as $webhook):
				$form = [
					"secret" => $webhook["secret"],
					"type" => $type,
					"data" => $payload
				];

				if($this->_sanitize->isUrl($webhook["url"])):
					try {
						$this->_guzzle->post($webhook["url"], [
				            "form_params" => $form,
				            "allow_redirects" => true,
				            "http_errors" => false
				        ]);

				        $webhookArray[] = $webhook["id"];
					} catch(Exception $e){
						// Ignore
					}
				endif;
			endforeach;
		endif;

		return $webhookArray;
	}

	public function actionHooks($uid, $source, $event, $phone, $message, $hooks)
	{
		$actionArray = [];

		if(!empty($hooks)):
			foreach($hooks as $hook):
				if($source == $hook["source"] && $event == $hook["event"]):
					if($this->_sanitize->isUrl($hook["link"])):
						try {
							$this->_guzzle->get($this->_lex->parse($hook["link"], [
		        				"phone" => urlencode($phone),
		        				"message" => urlencode($message),
		        				"date" => [
		        					"now" => urlencode(date("F j, Y")),
		        					"time" => urlencode(date("h:i A"))
		        				]
		        			]), [
					            "allow_redirects" => true,
					            "http_errors" => false
					        ]);

					        $actionArray[] = $hook["id"];
						} catch(Exception $e){
							// Ignore
						}
					endif;
				endif;
			endforeach;
		endif;

		return $actionArray;
	}

	public function actionAutoreplies($uid, $source, $phone, $message, $autoreplies)
	{
		$actionArray = [];

		if(!empty($autoreplies)):
			foreach($autoreplies as $autoreply):
				if($source == $autoreply["source"]):
					$detected = false;
					$keywords = explode(",", trim($autoreply["keywords"]));

					foreach($keywords as $keyword):
						if(find(strtolower(trim($keyword)), strtolower($message))):
							$detected = true;
						endif;
					endforeach;

					if($detected):
						$actionArray[] = [
							"message" => $this->_lex->parse($autoreply["message"], [
		        				"phone" => $phone,
		        				"message" => $message,
		        				"date" => [
		        					"now" => date("F j, Y"),
		        					"time" => date("h:i A") 
		        				]
		        			])
						];
					endif;
				endif;
			endforeach;
		endif;

		return $actionArray;
	}
}