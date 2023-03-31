<?php

class Table_Model extends MVC_Model
{
    /**
     * @coverage System
     * @desc System table models
     */

	public function getContacts($id)
    {
        $query = <<<SQL
SELECT id, `groups`, phone, name
FROM contacts
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["phone"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getWaGroups($uid)
    {
       $query = <<<SQL
SELECT id, uid, wid, gid, name, create_date
FROM wa_groups 
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["gid"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

	public function getGroups($id)
    {
        $query = <<<SQL
SELECT id, uid, name
FROM `groups`
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getDevices($id)
    {
        $query = <<<SQL
SELECT id, uid, did, name, manufacturer, create_date, ROUND(version, 0) AS version
FROM devices
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getGlobalDevices($id)
    {
        $query = <<<SQL
SELECT id, uid, did, name, manufacturer, create_date, ROUND(version, 0) AS version
FROM devices
WHERE uid != ? AND global_device < 2
SQL;

        $this->db->query($query, [
            $uid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["did"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    public function getWhatsAppAccounts($id)
    {
        $query = <<<SQL
SELECT id, uid, wid, `unique`, create_date
FROM wa_accounts
WHERE uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["unique"]] = $row;

            return $rows;
        else:
            return [];
        endif;
    }

    /**
     * @coverage Administration
     * @desc Admin models
     */

    public function getUsers()
    {
        $query = <<<SQL
SELECT id, role, email, name, country, language, providers, suspended, timezone, create_date
FROM users
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
SELECT id, name, permissions
FROM roles
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

    public function getPackages()
    {
        $query = <<<SQL
SELECT id, send_limit, receive_limit, contact_limit, device_limit, key_limit, webhook_limit, action_limit, scheduled_limit, wa_send_limit, wa_receive_limit, wa_account_limit, name, price, footermark, hidden, create_date
FROM packages
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

    public function getTransactions()
    {
        $query = <<<SQL
SELECT id, uid, pid, price, currency, duration, provider, create_date
FROM transactions
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

    public function checkSmsCampaignPending($uid, $cid)
    {
        $this->db->query("SELECT id FROM sent WHERE uid = ? AND cid = ? AND status < 3", [
            $uid,
            $cid
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
}