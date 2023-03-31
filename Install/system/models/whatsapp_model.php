<?php

class Whatsapp_Model extends MVC_Model
{
	public function checkWid($uid, $wid)
	{
		$this->db->query("SELECT id FROM wa_accounts WHERE uid = ? AND wid LIKE ?", [
			$uid,
			"{$wid}%"
		]);

		return $this->db->num_rows();
	}

	public function checkAccount($uid, $unique)
	{
		$this->db->query("SELECT id FROM wa_accounts WHERE uid = ? AND `unique` = ?", [
			$uid,
			$unique
		]);

		return $this->db->num_rows();
	}

    public function getChat($id)
    {
        return $this->db->query_one("SELECT id, uid, wid, phone, message, status, api, create_date FROM wa_sent WHERE id = ?", [
            $id
        ]);
    }

	public function getUserEmail($id)
    {
        $fetch = $this->db->query_one("SELECT email FROM users WHERE id = ?", [
            $id
        ]);

        return $fetch ? $fetch["email"] : false;
    }

    public function getUserLanguage($id)
    {
        $fetch = $this->db->query_one("SELECT language FROM users WHERE id = ?", [
            $id
        ]);

        return $fetch ? $fetch["language"] : 1;
    }

	public function getUserTimezone($id)
    {
        $fetch = $this->db->query_one("SELECT timezone FROM users WHERE id = ?", [
            $id
        ]);

        return $fetch ? $fetch["timezone"] : "UTC";
    }

	public function getPendingMessages($uid, $unique)
	{
		$query = <<<SQL
SELECT s.id AS id, IF(c.id, c.id, 0) AS cid, IF(c.status, c.status, 1) AS cstatus, s.uid AS uid, s.wid AS wid, s.unique AS `unique`, s.phone AS phone, s.message AS message
FROM wa_sent s
LEFT JOIN wa_campaigns c ON s.cid = c.id
WHERE s.uid = ? AND s.unique = ? AND s.status < 2
LIMIT 100
SQL;

        $this->db->query($query, [
            $uid,
            $unique
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
	}

    public function getWebhooks($uid, $event)
    {
        $query = <<<SQL
SELECT id, secret, url, events
FROM webhooks
WHERE uid = ? AND FIND_IN_SET(?, events)
SQL;

        $this->db->query($query, [
            $uid,
            $event
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getActions($uid, $type)
    {
        $query = <<<SQL
SELECT id, type, source, event, keywords, link, message
FROM actions
WHERE uid = ? AND type = ? AND source > 1
SQL;

        $this->db->query($query, [
            $uid,
            $type
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }
}