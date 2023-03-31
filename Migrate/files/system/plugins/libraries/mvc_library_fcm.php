<?php

class MVC_Library_Fcm
{
	public function send($topic, $data, $notification = [])
	{
		if(!file_exists("system/storage/temporary/firebase.json"))
			return false;

		$fcm = (new Kreait\Firebase\Factory)->withServiceAccount("system/storage/temporary/firebase.json");

		$messaging = $fcm->createMessaging();

		try {
			if(empty($notification)):
				return $messaging->send(Kreait\Firebase\Messaging\CloudMessage::fromArray([
				    "topic" => $topic,
				    "data" => $data
				]));
			else:
				return $messaging->send(Kreait\Firebase\Messaging\CloudMessage::fromArray([
				    "topic" => $topic,
				    "notification" => $notification,
				    "data" => $data
				]));
			endif;
		} catch(Exception $e){
			return false;
		}
	}
}