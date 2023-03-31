<?php

class Builder_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);
		$type = $this->sanitize->string($this->url->segment(3));

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if(!isset($request["token"]))
			response(500);
		
		if(system_token != $request["token"])
			response(500);

		switch($type):
			case "download":
				$gateway = strtolower(system_package_name . ".apk");

				try {
					$this->guzzle->get(titansys_cdn . "/zender/" . strtolower(system_package_name) . ".apk?_=" . time(), [
						"sink" => "uploads/builder/{$gateway}",
		                "allow_redirects" => true,
						"http_errors" => false
					]);

					$this->fcm->send("system", [
				    	"type" => "update"
				    ]);

				    if(!empty(system_mailing_address) && in_array("admin_build_gateway", explode(",", system_mailing_triggers))):
						$mailingContent = <<<HTML
						<p>Hi there!</p>
						<p>This is to inform you that a new gateway apk file has been uploaded to system!</p> 
						HTML;

						$this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
								"content" => $mailingContent
							]
						], system_mailing_address, "_mail/default.tpl", $this->smarty);
		    		endif;
				} catch(Exception $e){
					if(!empty(system_mailing_address) && in_array("admin_build_gateway", explode(",", system_mailing_triggers))):
						$mailingContent = <<<HTML
						<p>Hi there!</p>
						<p>The newly built gateway app wasn't uploaded to your site, please check if your filesystem has proper permissions. Please read the official documentation for more info.</p> 
						HTML;

						$this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
								"content" => $mailingContent
							]
						], system_mailing_address, "_mail/default.tpl", $this->smarty);
		    		endif;

					response(500);
				}

	            response(200);
			default:
				response(500);
		endswitch;
	}
}