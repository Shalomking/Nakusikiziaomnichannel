<?php

class Widget_Model extends MVC_Model
{   
    /**
     * @type Check functions
     */

    public function checkModal($hash)
    {
        $this->db->query("SELECT id FROM widgets WHERE type = 2 AND MD5(id) = ?", [
            $hash
        ]);

        return $this->db->num_rows();
    }

    public function checkPackage($id)
    {
        $this->db->query("SELECT id FROM packages WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    /**
     * @type Single Row
     */

    public function getContent($id, $table, $column)
    {
        if($table == "`keys`"):
            $query = <<<SQL
SELECT REPLACE({$column}, ',', ', ') AS permissions FROM {$table} WHERE id = ? AND uid = ?
SQL;
        else:
            $query = <<<SQL
SELECT {$column} FROM {$table} WHERE id = ? AND uid = ?
SQL;
        endif;

        try {
            return $this->db->query_one($query, [
                $id,
                logged_id
            ])[$column];
        } catch(Exception $e){
            return false;
        }
    }

    public function getModal($hash)
    {
        return $this->db->query_one("SELECT id, size, position, icon, name, content FROM widgets WHERE type = 2 AND MD5(id) = ?", [
            $hash
        ]);
    }

    public function getTemplate($id)
    {
        return $this->db->query_one("SELECT id, name, format FROM templates WHERE id = ?", [
            $id
        ]);
    }

    public function getScheduled($uid, $id, $wa = false)
    {
        if($wa):
            return $this->db->query_one("SELECT id, uid, wid, `groups`, name, numbers, message, `repeat`, last_send, send_date FROM wa_scheduled WHERE uid = ? AND id = ?", [
                $uid,
                $id
            ]);
        else:
            return $this->db->query_one("SELECT id, uid, did, sim, mode, gateway, `groups`, name, numbers, message, `repeat`, last_send, send_date FROM scheduled WHERE uid = ? AND id = ?", [
                $uid,
                $id
            ]);
        endif;
    }

    public function getContact($uid, $id)
    {
        return $this->db->query_one("SELECT id, `groups`, name, phone FROM contacts WHERE uid = ? AND id = ?", [
            $uid,
            $id
        ]);
    }

    public function getGroup($uid, $id)
    {
        return $this->db->query_one("SELECT id, name FROM `groups` WHERE uid = ? AND id = ?", [
            $uid,
            $id
        ]);
    }

    public function getDevice($id)
    {
        return $this->db->query_one("SELECT id, name, version, random_send, random_min, random_max, limit_status, limit_interval, limit_number, packages, receive_sms, global_device, global_priority, global_slots, country, rate FROM devices WHERE id = ?", [
            $id
        ]);
    }

    public function getWhatsapp($id)
    {
        return $this->db->query_one("SELECT id, uid, wid, `unique`, receive_chats, random_send, random_min, random_max FROM wa_accounts WHERE id = ?", [
            $id
        ]);
    }

    public function getKey($uid, $id)
    {
        return $this->db->query_one("SELECT id, uid, secret, name, permissions FROM `keys` WHERE uid = ? AND id = ?", [
            $uid,
            $id
        ]);
    }

    public function getWebhook($id)
    {
        return $this->db->query_one("SELECT id, uid, events, secret, name, url FROM webhooks WHERE id = ?", [
            $id
        ]);
    }

    public function getAction($uid, $id)
    {
        return $this->db->query_one("SELECT id, uid, type, source, event, name, keywords, link, message, create_date FROM actions WHERE uid = ? AND id = ?", [
            $uid,
            $id
        ]);
    }

    public function getUser($id)
    {
        return $this->db->query_one("SELECT id, role, email, credits, name, language, timezone, formatting, country, alertsound, partner FROM users WHERE id = ?", [
            $id
        ]);
    }

    public function getRole($id)
    {
        return $this->db->query_one("SELECT id, name, permissions FROM roles WHERE id = ?", [
            $id
        ]);
    }

    public function getPackage($id)
    {
        return $this->db->query_one("SELECT * FROM packages WHERE id = ?", [
            $id
        ]);
    }

    public function getWidget($id)
    {
        return $this->db->query_one("SELECT id, type, size, position, icon, name, content FROM widgets WHERE id = ?", [
            $id
        ]);
    }

    public function getPage($id)
    {
        return $this->db->query_one("SELECT id, roles, logged, slug, name, content FROM pages WHERE id = ?", [
            $id
        ]);
    }

    public function getLanguage($id)
    {
        return $this->db->query_one("SELECT id, rtl, iso, name FROM languages WHERE id = ?", [
            $id
        ]);
    }

    public function getGateway($id)
    {
        return $this->db->query_one("SELECT id, name, callback, callback_id, pricing, create_date FROM gateways WHERE id = ?", [
            $id
        ]);
    }

    public function getShortener($id)
    {
        return $this->db->query_one("SELECT id, name, create_date FROM shorteners WHERE id = ?", [
            $id
        ]);
    }

    public function getPlugin($id)
    {
        $query = <<<SQL
            SELECT id, data FROM plugins WHERE id = ?
SQL;

        return $this->db->query_one($query, [
            $id
        ]);
    }

    /**
     * @type Multiple Rows
     */

    public function getTemplates($uid)
    {
        $query = <<<SQL
SELECT id, uid, name, format FROM templates WHERE uid = ?
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "uid" => $row["uid"],
                    "name" => $row["name"],
                    "format" => $row["format"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

	public function getGroups($uid)
	{
		$query = <<<SQL
SELECT id, uid, name FROM `groups` WHERE uid = ?
SQL;

		$this->db->query($query, [
			$uid
		]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                	"name" => $row["name"],
                	"token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
	}

    public function getDevices($uid, $ussd = false)
    {
        if($ussd):
            $query = <<<SQL
SELECT id, uid, did, name, online_id, online_status FROM devices WHERE uid = ? AND ROUND(version) >= 8
SQL;
        else:
            $query = <<<SQL
SELECT id, uid, did, name, online_id, online_status FROM devices WHERE uid = ?
SQL;
        endif;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = [
                    "id" => $row["id"],
                    "uid" => $row["uid"],
                    "global" => false,
                    "name" => $row["name"],
                    "online_id" => $row["online_id"],
                    "status" => $row["online_status"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getGlobalDevices($uid)
    {
        $query = <<<SQL
SELECT d.id AS id, d.uid AS uid, d.did AS did, d.name AS name, d.country AS country, d.rate AS rate, d.online_id AS online_id, d.online_status AS online_status, u.email AS owner
FROM devices d
LEFT JOIN users u ON d.uid = u.id 
WHERE d.uid != ? AND d.global_device < 2
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = [
                    "id" => $row["id"],
                    "uid" => $row["uid"],
                    "global" => true,
                    "country" => $row["country"],
                    "rate" => $row["rate"],
                    "name" => $row["name"],
                    "owner" => $row["owner"],
                    "online_id" => $row["online_id"],
                    "status" => $row["online_status"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getWaAccounts($uid)
    {
        $query = <<<SQL
SELECT id, uid, wid, `unique` FROM wa_accounts WHERE uid = ?
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next()):
                $name = explode(":", $row["wid"]);
                $rows[$row["id"]] = [
                    "name" => "+{$name[0]}",
                    "token" => "+{$name[0]}",
                    "wid" => $row["wid"]
                ];
            endwhile;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getLanguages()
    {
        $query = <<<SQL
SELECT id, iso, name FROM languages
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "name" => $row["name"],
                    "token" => strtolower($row["name"])
                ];

            return $rows;
        else:
            return [];
        endif;
    }

    public function getGateways()
    {
        $query = <<<SQL
SELECT id, name, pricing
FROM gateways
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getShorteners()
    {
        $query = <<<SQL
SELECT id, name, create_date
FROM shorteners
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getRoles()
    {
        $query = <<<SQL
SELECT id, name, permissions FROM roles
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getPackages($hiddenFilter = false, $freemodel = false)
    {
        $freemodelSql = !$freemodel ? ($hiddenFilter ? "WHERE hidden > 1 AND id > 1" : "WHERE id > 1") : ($hiddenFilter ? "WHERE hidden > 1" : false);

        if($hiddenFilter):
            $query = <<<SQL
SELECT *
FROM packages
{$freemodelSql}
SQL;
        else:
            $query = <<<SQL
SELECT *
FROM packages
{$freemodelSql}
SQL;
        endif;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getUsers()
    {
        $query = <<<SQL
SELECT id, role, name, email FROM users
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = [
                    "id" => $row["id"],
                    "hash" => md5($row["id"]),
                    "role" => $row["role"],
                    "name" => $row["name"],
                    "email" => $row["email"],
                    "token" => strtolower("{$row["name"]} {$row["email"]}")
                ];

            return $rows;
        else:
            return [];
        endif;
    }
}