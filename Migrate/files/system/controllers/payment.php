<?php

class Payment_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->redirect(site_url);
	}

	public function success()
	{
		if(!$this->session->has("logged"))
            $this->header->redirect(site_url);
        else
            set_template("dashboard");

        $request = $this->sanitize->array($_GET);
        $provider = $this->sanitize->string($this->url->segment(4));
        $order = $this->sanitize->string($this->url->segment(5)) ?: false;

        if(empty($provider))
        	$this->header->redirect(site_url);

        if(!$this->smarty->templateExists(template . "/pages/misc.tpl"))
            $this->header->redirect(site_url("dashboard"));

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language, logged_rtl);

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;
            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

        if($provider == "paypal")
        	$order = $request["tx"] ?: __("lang_payment_success_unknowntx");

        if(empty($order))
        	$order = __("lang_payment_success_unknowntx");

        $dashboardUrl = site_url("dashboard");

        $vars = [
            "title" => __("lang_title_payment_success"),
            "page" => "misc",
            "data" => [
            	"title" => __("lang_header_payment_success"),
            	"content" => <<<HTML
				<p>{$GLOBALS["__"]("lang_and_dash_pg_pay_line45")}</p>

				<p>{$GLOBALS["__"]("lang_transaction_payment_success")} <strong>{$order}</strong></p>

				<p class="mb-0">
					<a href="{$dashboardUrl}" class="btn btn-primary">{$GLOBALS["__"]("lang_back_payment_page")}</a>
				</p>
				HTML
            ]
        ];

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/misc.tpl");
        $this->smarty->display(template . "/footer.tpl");
	}

	public function cancel()
	{
		if(!$this->session->has("logged"))
            $this->header->redirect(site_url);
        else
            set_template("dashboard");

        if(!$this->smarty->templateExists(template . "/pages/misc.tpl"))
            $this->header->redirect(site_url("dashboard"));

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language, logged_rtl);

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;
            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

        $dashboardUrl = site_url("dashboard");

        $vars = [
            "title" => __("lang_title_payment_cancel"),
            "page" => "misc",
            "data" => [
            	"title" => __("lang_header_payment_cancel"),
            	"content" => <<<HTML
				<p>{$GLOBALS["__"]("lang_body_payment_cancel")}</p>

				<p class="mb-0">
				  <a href="{$dashboardUrl}" class="btn btn-primary">{$GLOBALS["__"]("lang_back_payment_page")}</a>
				</p>
				HTML
            ]
        ];

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/misc.tpl");
        $this->smarty->display(template . "/footer.tpl");
	}

	public function webhook()
	{	
		$request = $this->sanitize->array($_POST);
		$provider = $this->sanitize->string($this->url->segment(4));

		if(empty($request))
			response(500);
		
		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

		switch($provider):
			case "mollie":
				if(!isset($request["id"]))
					response(500);

				$mollie = new \Mollie\Api\MollieApiClient();
				$mollie->setApiKey(system_mollie_key);

			 	$payment = $mollie->payments->get($request["id"]);

			 	$hash = $this->sanitize->string($this->url->segment(5));
			 	$order = $this->sanitize->string($this->url->segment(6));

				$this->cache->container("system.payments", true);

				if(!$this->cache->has("order.{$hash}"))
					response(404);

				$item = $this->cache->get("order.{$hash}");
				$user = $this->system->getUserByHash($hash);

				$this->cache->delete("order.{$hash}");

				set_language($user["language"]);

				if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()):
					if($item["type"] < 2):
				    	if($this->system->checkSubscription($user["id"]) > 0):
							$transaction = $this->system->create("transactions", [
								"uid" => $user["id"],
								"pid" => $item["data"]["package"]["id"],
								"type" => 1,
								"price" => $item["data"]["package"]["price"],
								"currency" => system_currency,
								"duration" => $item["data"]["duration"],
								"provider" => "mollie"
							]);

							$filtered = [
								"pid" => $item["data"]["package"]["id"],
								"tid" => $transaction
							];

							$subscription = $this->system->getSubscription(false, $user["id"]);

							$this->system->update($subscription["sid"], $user["id"], "subscriptions", $filtered);
						else:
							$transaction = $this->system->create("transactions", [
								"uid" => $user["id"],
								"pid" => $item["data"]["package"]["id"],
								"type" => 1,
								"price" => $item["data"]["package"]["price"],
								"currency" => system_currency,
								"duration" => $item["data"]["duration"],
								"provider" => "mollie"
							]);

							$filtered = [
								"uid" => $user["id"],
								"pid" => $item["data"]["package"]["id"],
								"tid" => $transaction
							];

							$this->system->create("subscriptions", $filtered);
						endif;

						$this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title(__("lang_response_package_purchasedtitle")),
								"package" => $this->system->getPackage($item["data"]["package"]["id"]),
								"subscription" => $this->system->getSubscription(false, $user["id"])
							]
						], $user["email"], "_mail/subscribe.tpl", $this->smarty);

						if(!empty(system_mailing_address) && in_array("admin_package_buy", explode(",", system_mailing_triggers))):
							$packageWorth = "{$item["data"]["package"]["price"]} " . system_currency;

							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that <strong>{$user["email"]}</strong> has bought <strong>{$item["data"]["package"]["name"]}</strong> package worth <strong>{$packageWorth}</strong> via <strong>Mollie</strong>.</p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;
					else:
						$transaction = $this->system->create("transactions", [
							"uid" => $user["id"],
							"pid" => 0,
							"type" => 2,
							"price" => $item["data"]["credits"],
							"currency" => system_currency,
							"duration" => 0,
							"provider" => "mollie"
						]);

						$this->system->credits($user["id"], "increase", $item["data"]["credits"]);

						$this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title(__("lang_payment_webhook_molliecreditsadded")),
								"credits" => $item["data"]["credits"]
							]
						], $user["email"], "_mail/credits.tpl", $this->smarty);

						if(!empty(system_mailing_address) && in_array("admin_credits_buy", explode(",", system_mailing_triggers))):
							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that <strong>{$user["email"]}</strong> has bought <strong>{$item["data"]["credits"]}</strong> credits via <strong>Mollie</strong>.</p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;
					endif;

					$this->cache->container("system.transactions");
        			$this->cache->clear();
			    else:
			    	$this->mail->send([
						"title" => system_site_name,
						"data" => [
							"subject" => mail_title(__("lang_payment_transact_failed")),
							"order" => $order ?: __("lang_payment_webhook_unknowntn")
						]
					], $user["email"], "_mail/failed.tpl", $this->smarty);
			    endif;

				break;
			case "paypal":
				if(system_paypal_test < 2)
        			$this->paypal->useSandbox();

        		$verify = $this->paypal->verifyIPN();

        		$uid = $this->hash->decode($request["item_number"], system_token);

				if(!$uid)
					response(500);

				$this->cache->container("system.payments", true);

				if(!$this->cache->has("order." . md5($uid)))
					response(404);

				$item = $this->cache->get("order." . md5($uid));
				$user = $this->system->getUser($uid);

				$this->cache->delete("order." . md5($uid));

				set_language($user["language"]);

				if($verify):
					if($item["type"] < 2):
						if($this->system->checkSubscription($user["id"]) > 0):
							$transaction = $this->system->create("transactions", [
								"uid" => $user["id"],
								"pid" => $item["data"]["package"]["id"],
								"type" => 1,
								"price" => $item["data"]["price"],
								"currency" => system_currency,
								"duration" => $item["data"]["duration"],
								"provider" => "paypal"
							]);

							$filtered = [
								"pid" => $item["data"]["package"]["id"],
								"tid" => $transaction
							];

							$subscription = $this->system->getSubscription(false, $user["id"]);

							$this->system->update($subscription["sid"], $user["id"], "subscriptions", $filtered);
						else:
							$transaction = $this->system->create("transactions", [
								"uid" => $user["id"],
								"pid" => $item["data"]["package"]["id"],
								"type" => 1,
								"price" => $item["data"]["price"],
								"currency" => system_currency,
								"duration" => $item["data"]["duration"],
								"provider" => "paypal"
							]);

							$filtered = [
								"uid" => $user["id"],
								"pid" => $item["data"]["package"]["id"],
								"tid" => $transaction
							];

							$this->system->create("subscriptions", $filtered);
						endif;

						$this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title(__("lang_response_package_purchasedtitle")),
								"package" => $item["data"]["package"],
								"subscription" => $this->system->getSubscription(false, $user["id"])
							]
						], $user["email"], "_mail/subscribe.tpl", $this->smarty);

						if(!empty(system_mailing_address) && in_array("admin_package_buy", explode(",", system_mailing_triggers))):
							$packageWorth = "{$item["data"]["package"]["price"]} " . system_currency;

							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that <strong>{$user["email"]}</strong> has bought <strong>{$item["data"]["package"]["name"]}</strong> package worth <strong>{$packageWorth}</strong> via <strong>Paypal</strong>.</p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;
					else:
						$transaction = $this->system->create("transactions", [
							"uid" => $user["id"],
							"pid" => 0,
							"type" => 2,
							"price" => $item["data"]["credits"],
							"currency" => system_currency,
							"duration" => 0,
							"provider" => "paypal"
						]);

						$this->system->credits($user["id"], "increase", $item["data"]["credits"]);

						$this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title(__("lang_payment_webhook_paypalcreditsadded")),
								"credits" => $item["data"]["credits"]
							]
						], $user["email"], "_mail/credits.tpl", $this->smarty);

						if(!empty(system_mailing_address) && in_array("admin_credits_buy", explode(",", system_mailing_triggers))):
							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that <strong>{$user["email"]}</strong> has bought <strong>{$item["data"]["credits"]}</strong> credits via <strong>Paypal</strong>.</p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;
					endif;

					$this->cache->container("system.transactions");
        			$this->cache->clear();
				else:
					$this->mail->send([
						"title" => system_site_name,
						"data" => [
							"subject" => mail_title(__("lang_payment_transact_failed")),
							"order" => $request["item_number"] ?: __("lang_payment_webhook_unknowntn")
						]
					], $user["email"], "_mail/failed.tpl", $this->smarty);
				endif;

				break;
			default:
				response(500);
		endswitch;

		response(200);
	}
}