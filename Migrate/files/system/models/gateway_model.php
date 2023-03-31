<?php

class Gateway_Model extends MVC_Model
{
	public function checkCallbackId($id)
	{
		$this->db->query("SELECT id FROM gateways WHERE callback_id = ?", [
			$id
		]);

		return $this->db->num_rows();
	}

	public function getGatewayByCallbackId($id)
	{
		return $this->db->query_one("SELECT id, name, callback, callback_id, pricing, create_date FROM gateways WHERE callback_id = ?", [
			$id
		]);
	}
}