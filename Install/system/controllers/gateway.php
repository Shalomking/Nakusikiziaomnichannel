<?php
/**
 * SMS gateway callback handler
 * @since v3.0
 */

class Gateway_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		$callbackId = $this->sanitize->string($this->url->segment(3));

		if($this->gateway->checkCallbackId($callbackId) > 0):
			$gateway = $this->gateway->getGatewayByCallbackId($callbackId);

			if(!$this->file->exists("system/gateways/" . md5($gateway["id"]) . ".php"))
				response(500);

			try {
				require "system/gateways/" . md5($gateway["id"]) . ".php";
			} catch(Exception $e){
				response(500);
			}

			$verify = gatewayCallback($_REQUEST, $this);

			if($verify):
				try {
					$this->system->update($verify, false, "sent", [
						"status" => 3
					]);

					$sent = $this->system->getSent($verify);

					$gateways = $this->system->getGateways();

					$pricing = json_decode($gateways[$sent["gateway"]]["pricing"], true);

				    $number = $this->phone->parse($sent["phone"]);

					$country = $number->getRegionCode();

					if(array_key_exists(strtolower($country), $pricing["countries"])):
						$price = $pricing["countries"][strtolower($country)];
					else:
						$price = $pricing["default"];
					endif;

					$this->process->_sanitize = $this->sanitize;
					$this->process->_guzzle = $this->guzzle;
					$this->process->_lex = $this->lex;

					$hooks = $this->process->actionHooks($sent["uid"], 1, 1, $sent["phone"], $sent["message"], $this->device->getActions($sent["uid"], 1));

					if(!empty($hooks)):
						foreach($hooks as $hook):
							$this->system->create("events", [
								"uid" => $sent["uid"],
								"type" => 2,
								"create_date" => date("Y-m-d H:i:s", time())
							]);
						endforeach;
					endif;

					$this->system->credits($sent["uid"], "decrease", $price);
				} catch(Exception $e){
					response(500);
				}

				response(200);
			else:
				response(500);
			endif;
		else:
			response(404);
		endif;
	}
}