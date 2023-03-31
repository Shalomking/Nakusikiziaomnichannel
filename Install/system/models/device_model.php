<?php

class Device_Model extends MVC_Model
{
    public function checkUserId($id)
    {
        $this->db->query("SELECT id FROM users WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function checkUserEmail($email)
    {
        $this->db->query("SELECT id FROM users WHERE email = ?", [
            $email
        ]);

        return $this->db->num_rows();
    }

	public function checkDevice($did)
    {
        $this->db->query("SELECT id FROM devices WHERE did = ?", [
            $did
        ]);
        
        return $this->db->num_rows();
    }

    public function checkUserHash($hash)
    {
        $this->db->query("SELECT id FROM users WHERE MD5(id) = ?", [
            $hash
        ]);

        return $this->db->num_rows();
    }

    public function checkSuspension($uid)
    {
        $this->db->query("SELECT id FROM users WHERE id = ? AND suspended > 0", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function checkReceived($rid, $uid, $did)
    {
        $this->db->query("SELECT id FROM received WHERE rid = ? AND uid = ? AND did = ?", [
            $rid,
            $uid,
            $did
        ]);

        return $this->db->num_rows();
    }

    public function getUserAccess($email)
    {
        return $this->db->query_one("SELECT id, MD5(id) AS hash, password, suspended FROM users WHERE email = ?", [
            $email
        ]);
    }

    public function getUserHash($id)
    {
        try {
            return $this->db->query_one("SELECT MD5(uid) AS hash FROM sent WHERE id = ?", [
                $id
            ])["hash"];
        } catch(Exception $e){
            return false;
        }
    }

    public function getUserEmail($id)
    {
        try {
            return $this->db->query_one("SELECT email FROM users WHERE id = ?", [
                $id
            ])["email"];
        } catch(Exception $e){
            return false;
        }
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

    public function getDevice($did)
    {
        return $this->db->query_one("SELECT id, uid, did, name, version, manufacturer, random_send, random_min, random_max, packages, receive_sms, global_device, global_priority, global_slots, country, rate, create_date FROM devices WHERE did = ?", [
            $did
        ]);
    }

    public function getPendingMessages($did)
    {
        $query = <<<SQL
SELECT s.id AS id, IF(c.id, c.id, 0) AS cid, IF(c.status, c.status, 1) AS cstatus, s.uid AS uid, s.sim AS sim, s.phone AS phone, s.message AS message, s.priority AS priority
FROM sent s
LEFT JOIN campaigns c ON s.cid = c.id
WHERE s.did = ? AND s.status < 2
LIMIT 100
SQL;

        $this->db->query($query, [
            $did
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getCleanerSms($ids, $did)
    {
        $this->db->query("SELECT id FROM sent WHERE id in ({$ids}) AND did = ? AND status < 3", [
            $did
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row["id"];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getPendingUssd($did)
    {
        $query = <<<SQL
SELECT id, uid, sim, code
FROM ussd
WHERE did = ? AND status < 2
SQL;

        $this->db->query($query, [
            $did
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getContacts($hash)
    {
        $query = <<<SQL
SELECT id, uid, gid, phone, name
FROM contacts
WHERE MD5(uid) = ?
SQL;

        $this->db->query($query, [
            $hash
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
WHERE uid = ? AND type = ? AND source < 2
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