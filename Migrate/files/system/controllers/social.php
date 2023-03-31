<?php

class Social_Controller extends MVC_Controller
{
	public function index()
	{
		if($this->session->has("logged"))
            $this->header->redirect(site_url("dashboard"));

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $platform = ($this->sanitize->string($this->url->segment(3)) ?: "facebook");

        switch($platform):
        	case "facebook":
    			$config = [
				   	"callback" => site_url("social/facebook", true),
				   	"scope" => "email",
				    "keys" => [
				    	"id" => system_facebook_id, 
				    	"secret" => system_facebook_secret
				    ]
				];

				$adapter = new Hybridauth\Provider\Facebook($config);

				try {
			        $adapter->authenticate();
			        $profile = $adapter->getUserProfile();
			    } catch (\Exception $e) {
			        response(500, $e->getMessage());
			    }

        		break;
        	case "google":
        		$config = [
				   	"callback" => site_url("social/google", true),
				    "keys" => [
				    	"id" => system_google_id, 
				    	"secret" => system_google_secret
				    ]
				];

				$adapter = new Hybridauth\Provider\Google($config);

				try {
			        $adapter->authenticate();
			        $profile = $adapter->getUserProfile();
			    } catch (\Exception $e) {
			        response(500, $e->getMessage());
			    }
        		
        		break;
        	case "vkontakte":
        		$config = [
				   	"callback" => site_url("social/vkontakte", true),
				    "keys" => [
				    	"id" => system_vk_id, 
				    	"secret" => system_vk_secret
				    ]
				];

        		$adapter = new Hybridauth\Provider\Vkontakte($config);

        		try {
			        if (!$adapter->isConnected())
            			$adapter->authenticate();
			 
			        $profile = $adapter->getUserProfile();
			    } catch (\Exception $e) {
			        response(500, $e->getMessage());
			    }

        		break;
        	default:
        		$this->header->redirect(site_url);
        endswitch;

        if($this->social->checkIdentifier($profile->identifier) < 1):
        	if(empty($profile->email)):
        		response(500, "A valid email address must be present in your {$platform} account!");
        	else:
        		if($this->social->checkEmail($profile->email) > 0):
        			$user = $this->social->getUserByEmail($profile->email);

        			if($user["suspended"] > 0):
        				response(500, "Your account is suspended!");
        			endif;

        			if(!empty($user["providers"])):
        				$decoded = json_decode($user["providers"], true);
        				$decoded[$platform] = $profile->identifier;

        				$this->social->updateSocial($profile->email, [
        					"providers" => json_encode($decoded)
        				]);
        			else:
        				$this->social->updateSocial($profile->email, [
        					"providers" => json_encode([
        						$platform => $profile->identifier
        					])
        				]);
        			endif;

        			$this->session->set("logged", $user);
        			$this->session->delete("language");
                    $this->header->redirect(site_url("dashboard"));
        		else:
        			$create = $this->system->create("users", [
        				"role" => 1,
        				"email" => $profile->email,
        				"password" => uniqid(system_token, rand(0, 1000)),
        				"name" => $profile->displayName,
                        "credits" => 0,
                        "earnings" => 0,
        				"language" => system_default_lang,
        				"providers" => json_encode([
        					$platform => $profile->identifier
        				]),
                        "alertsound" => 1,
                        "timezone" => system_default_timezone,
                        "formatting" => false,
                        "country" => system_default_country,
                        "partner" => 2,
                        "confirmed" => 1,
        				"suspended" => 0
        			]);

        			if($create):
        				if(!empty(system_mailing_address) && in_array("admin_new_user", explode(",", system_mailing_triggers))):
            				$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that a new user with email <strong>{$profile->email}</strong> have registered via {$platform}!</p> 
							HTML;

	            			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
	            		endif;

        				$this->session->set("logged", 
	        				$this->social->getUserById($create)
	        			);

                        $this->header->redirect(site_url("dashboard"));
        			else:
        				response(500, false);
        			endif;
        		endif;
        	endif;
        else:
        	$this->session->set("logged", 
        		$this->social->getUserByIdentifier($profile->identifier)
        	);

			$this->session->delete("language");
            $this->header->redirect(site_url("dashboard"));
        endif;
	}
}