<?php

class Cron_Model extends MVC_Model
{
    public function checkWaAccount($uid, $wid)
    {
        $this->db->query("SELECT id FROM wa_accounts WHERE uid = ? AND wid = ?", [
            $uid,
            $wid
        ]);

        return $this->db->num_rows();
    }

    public function resetQuota()
    {
        return $this->db->query("TRUNCATE TABLE quota");
    }

    public function getQuota()
    {
        $query = <<<SQL
SELECT id, uid, MD5(uid) as hash
FROM quota
SQL;

        $this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getPendingSms()
    {
        $query = <<<SQL
SELECT id, uid, did, mode 
FROM sent
WHERE status < 2
SQL;

        $this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getPendingWa()
    {
        $query = <<<SQL
SELECT id, uid, `unique` 
FROM wa_sent
WHERE status < 2
SQL;

        $this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getWaAccount($uid, $wid)
    {
        return $this->db->query_one("SELECT id, uid, wid, `unique`, create_date FROM wa_accounts WHERE uid = ? AND wid = ?", [
            $uid,
            $wid
        ]);
    }

    public function getWaAccounts()
    {
        $query = <<<SQL
SELECT id, uid, MD5(uid) as hash, wid, `unique`, create_date
FROM wa_accounts
SQL;

        $this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getScheduled($wa = false)
    {
        if($wa):
            $query = <<<SQL
SELECT s.id AS id, s.uid AS uid, s.wid AS wid, s.unique AS `unique`, MD5(s.uid) AS hash, u.timezone AS `timezone`, u.country AS country, s.groups AS `groups`, s.name AS name, s.numbers AS numbers, s.message AS message, s.repeat AS `repeat`, s.last_send AS last_send, s.send_date AS send_date
FROM wa_scheduled s
LEFT JOIN users u ON s.uid = u.id
SQL;
        else:
            $query = <<<SQL
SELECT s.id AS id, s.uid AS uid, MD5(s.uid) AS hash, u.timezone AS `timezone`, u.country AS country, s.did AS did, s.sim AS sim, s.mode AS mode, s.gateway AS gateway, s.groups AS `groups`, s.name AS name, s.numbers AS numbers, s.message AS message, s.repeat AS `repeat`, s.last_send AS last_send, s.send_date AS send_date
FROM scheduled s
LEFT JOIN users u ON s.uid = u.id
SQL;
        endif;

        $this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

	public function getSubscriptions()
	{
		$query = <<<SQL
SELECT s.id AS id, u.email AS email, MD5(s.uid) AS hash, u.language AS language, UNIX_TIMESTAMP(DATE_ADD(DATE(s.date), INTERVAL t.duration MONTH)) AS expire
FROM subscriptions s 
LEFT JOIN users u ON s.uid = u.id
LEFT JOIN transactions t ON s.tid = t.id
SQL;

		$this->db->query($query);

        if($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
	}

    public function updateLastSend($id, $timestamp, $wa = false)
    {
        try {
            $this->db->where("id", $id);
            if($wa):
                return $this->db->update("wa_scheduled", [
                    "last_send" => $timestamp
                ]);
            else:
                return $this->db->update("scheduled", [
                    "last_send" => $timestamp
                ]);
            endif;
        } catch (Exception $e) {
            return false;
        }
    }
}