<?php

class Api_Model extends MVC_Model
{
    public function checkApikey($secret)
    {
        $this->db->query("SELECT id FROM `keys` WHERE secret = ?", [
            $secret
        ]);

        return $this->db->num_rows();
    }

    public function checkSmsCampaign($id, $uid)
    {
        $this->db->query("SELECT id FROM campaigns WHERE id = ? AND uid = ?", [
            $id,
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function checkSmsCampaignPending($uid, $cid)
    {
        $this->db->query("SELECT id FROM sent WHERE uid = ? AND cid = ? AND status < 3", [
            $uid,
            $cid
        ]);

        return $this->db->num_rows();
    }

    public function checkWaCampaign($id, $uid)
    {
        $this->db->query("SELECT id FROM wa_campaigns WHERE id = ? AND uid = ?", [
            $id,
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function checkWaCampaignPending($uid, $cid)
    {
        $this->db->query("SELECT id FROM wa_sent WHERE uid = ? AND cid = ? AND status < 3", [
            $uid,
            $cid
        ]);

        return $this->db->num_rows();
    }

    public function getApikey($secret)
    {
        $query = <<<SQL
SELECT k.*, u.*, MD5(u.id) AS hash, k.id AS id, k.permissions AS permissions
FROM `keys` k
LEFT JOIN users u ON k.uid = u.id
WHERE secret = ?
SQL;

        return $this->db->query_one($query, [
            $secret
        ]);
    }

    public function getSms($uid, $type = "sent", $status = "pending", $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        if($type == "sent"):
            if($status == "pending"):
                $query = <<<SQL
SELECT s.*, g.name AS gateway_name
FROM sent s
LEFT JOIN gateways g ON s.gateway = g.id
WHERE s.uid = ? AND s.status < 2
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    
            else:
                $query = <<<SQL
SELECT s.*, g.name AS gateway_name
FROM sent s
LEFT JOIN gateways g ON s.gateway = g.id
WHERE s.uid = ? AND s.status > 2
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    
            endif;
        else:
            $query = <<<SQL
SELECT * 
FROM received 
WHERE uid = ? 
ORDER BY receive_date DESC 
LIMIT {$page}, {$limit}
SQL;    
        endif;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getSmsCampaigns($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, did, gateway, mode, status, name, contacts, create_date
FROM campaigns 
WHERE uid = ? 
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getSmsCampaign($id, $uid)
    {
        $query = <<<SQL
SELECT c.id AS id, c.uid AS uid, c.did AS did, d.uid AS device_uid, c.gateway AS gateway, c.mode AS mode, c.status AS status, c.name AS name, c.contacts AS contacts, c.create_date AS create_date
FROM campaigns c
LEFT JOIN devices d ON c.did = d.did
WHERE c.id = ? AND c.uid = ?
SQL;

        return $this->db->query_one($query, [
            $id,
            $uid
        ]);
    }

    public function getWaCampaign($id, $uid)
    {
        $query = <<<SQL
SELECT id, uid, wid, type, status, name, contacts, create_date
FROM wa_campaigns
WHERE id = ? AND uid = ?
SQL;

        return $this->db->query_one($query, [
            $id,
            $uid
        ]);
    }

    public function getChats($uid, $type = "sent", $status = "pending", $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        if($type == "sent"):
            if($status == "pending"):
                $query = <<<SQL
SELECT *
FROM wa_sent
WHERE uid = ? AND status < 3
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    
            else:
                $query = <<<SQL
SELECT *
FROM wa_sent
WHERE uid = ? AND status > 2
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;  
            endif;
        else:
            $query = <<<SQL
SELECT * 
FROM wa_received 
WHERE uid = ?
ORDER BY receive_date DESC 
LIMIT {$page}, {$limit}
SQL;    
        endif;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getWaCampaigns($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, wid, type, status, name, contacts, create_date
FROM wa_campaigns 
WHERE uid = ? 
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getContacts($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, `groups`, phone, name 
FROM contacts 
WHERE uid = ? 
ORDER BY id DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getGroups($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, name
FROM `groups` 
WHERE uid = ? 
ORDER BY id DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getUnsubscribed($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, phone, create_date
FROM unsubscribed 
WHERE uid = ? 
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getUssd($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, did, sim, code, response, status, create_date
FROM ussd 
WHERE uid = ?
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getNotifications($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, did, package, title, `text`, create_date
FROM notifications 
WHERE uid = ? 
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getWaAccounts($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, wid, `unique`, create_date
FROM wa_accounts 
WHERE uid = ? 
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getDevices($uid, $page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, did, name, version, manufacturer, random_send, random_min, random_max, limit_status, limit_interval, limit_number, packages, global_device, global_priority, global_slots, country, rate, online_id, online_status, create_date
FROM devices 
WHERE uid = ? 
ORDER BY create_date DESC
LIMIT {$page}, {$limit}
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getGateways()
    {
        $query = <<<SQL
SELECT id, name, pricing FROM gateways 
SQL;    

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getPartners($uid)
    {
        $query = <<<SQL
SELECT d.did AS `unique`, d.name AS name, d.version AS version, d.global_priority AS priority, d.global_slots AS slots, d.country AS country, d.rate AS rate, d.online_status AS status, u.email AS owner 
FROM devices d
LEFT JOIN users u ON d.uid = u.id
WHERE d.global_device < 2 AND d.uid != ?
SQL;    

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getShorteners($uid)
    {
        $query = <<<SQL
SELECT id, name FROM shorteners 
SQL;    

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }
} 