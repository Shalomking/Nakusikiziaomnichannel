<?php

class System_Model extends MVC_Model
{
    /**
     * @coverage System
     * @desc Get data
     */

    public function getBalance($uid)
    {   
        return $this->db->query_one("SELECT credits, earnings FROM users WHERE id = ?", [
            $uid
        ]);
    }

    public function getSentSuccessCount($id)
    {
        $this->db->query("SELECT id FROM sent WHERE uid = ? AND status = 3", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getSentFailedCount($id)
    {
        $this->db->query("SELECT id FROM sent WHERE uid = ? AND status > 3", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function getMessageReceived($uid, $id)
    {
        return $this->db->query_one("SELECT id, rid, uid, did FROM received WHERE id = ? AND uid = ?", [
            $id,
            $uid
        ]);
    }

    /**
     * @coverage System
     * @desc Check functions
     */

    public function checkUser($id)
    {
        $this->db->query("SELECT id FROM users WHERE id = ?", [
            $id
        ]);
        
        return $this->db->num_rows();
    }

    public function checkUserRole($uid, $role)
    {
        $this->db->query("SELECT id FROM users WHERE id = ? AND role = ?", [
            $uid,
            $role
        ]);

        return $this->db->num_rows();
    }

    public function checkEmail($email)
    {
        $this->db->query("SELECT id FROM users WHERE email = ?", [
            $email
        ]);

        return $this->db->num_rows();
    }

    public function checkDeleted($uid, $rid, $did)
    {
        $this->db->query("SELECT id FROM deleted WHERE rid = ? AND uid = ? AND did = ?", [
            $rid,
            $uid,
            $did
        ]);

        return $this->db->num_rows();
    }

    public function checkVoucher($code)
    {
        $this->db->query("SELECT id FROM vouchers WHERE code = ?", [
            $code
        ]);

        return $this->db->num_rows();
    }

    public function checkPayout($id, $type = "id")
    {   
        if($type == "uid"):
            $this->db->query("SELECT id FROM payouts WHERE uid = ?", [
                $id
            ]);
        else:
            $this->db->query("SELECT id FROM payouts WHERE id = ?", [
                $id
            ]);
        endif;
        
        return $this->db->num_rows();
    }

    public function checkRole($id)
    {
        $this->db->query("SELECT id FROM roles WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function checkLanguage($id)
    {
        $this->db->query("SELECT id FROM languages WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function checkPage($id)
    {
        $this->db->query("SELECT id FROM pages WHERE id = ?", [
            $id
        ]);

        return $this->db->num_rows();
    }

    public function checkPlugin($name)
    {
        $this->db->query("SELECT id FROM plugins WHERE name = ?", [
            $name
        ]);

        return $this->db->num_rows();
    }

    public function checkDevice($uid, $identifier, $type)
    {
        switch($type):
            case "id":
                $this->db->query("SELECT id FROM devices WHERE uid = ? AND id = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            case "did":
                $this->db->query("SELECT id FROM devices WHERE uid = ? AND did = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            default:
                return 0;
        endswitch;
        
        return $this->db->num_rows();
    }

    public function checkWaAccount($uid, $identifier, $type)
    {
        switch($type):
            case "id":
                $this->db->query("SELECT id FROM wa_accounts WHERE uid = ? AND id = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            case "unique":
                $this->db->query("SELECT id FROM wa_accounts WHERE uid = ? AND `unique` = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            default:
                return 0;
        endswitch;
        
        return $this->db->num_rows();
    }

    public function checkSmsLimit($uid, $did, $interval, $limit)
    {
        if($interval < 2):
            $dateToCheck = date("Y-m-d", time());
        else:
            $dateToCheck = date("Y-m", time());
        endif;

        $this->db->query("SELECT id FROM sent WHERE uid = ? AND did = ? AND status < 4 AND create_date LIKE ?", [
            $uid,
            $did,
            "{$dateToCheck}%"
        ]);
        
        return $this->db->num_rows() >= $limit ? true : false;
    }

    public function checkNumber($uid, $number)
    {
        $this->db->query("SELECT id FROM contacts WHERE uid = ? AND phone = ?", [
            $uid,
            $number
        ]);
        
        return $this->db->num_rows();
    }

    public function checkGroup($uid, $id)
    {
        $this->db->query("SELECT id FROM `groups` WHERE uid = ? AND id = ?", [
            $uid,
            $id
        ]);
        
        return $this->db->num_rows();
    }

    public function checkWaGroup($uid, $gid)
    {
        $this->db->query("SELECT id FROM wa_groups WHERE uid = ? AND gid = ?", [
            $uid,
            $gid
        ]);

        return $this->db->num_rows();
    }

    public function checkUnsubscribed($uid, $phone)
    {
        $this->db->query("SELECT id FROM unsubscribed WHERE uid = ? AND phone = ?", [
            $uid,
            $phone
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

    public function checkSubscription($uid)
    {
        $this->db->query("SELECT id FROM subscriptions WHERE uid = ?", [
            $uid
        ]);
        
        return $this->db->num_rows();
    }

    /**
     * @coverage Subscription
     * @desc Count functions
     */

    public function countQuota($uid, $column)
    {
        try {
            return $this->db->query_one("SELECT {$column} FROM quota WHERE uid = ?", [
                $uid
            ])[$column];
        } catch(Exception $e){
            return 0;
        }
    }

    public function countScheduled($uid)
    {
        $this->db->query("SELECT id FROM scheduled WHERE uid = ?", [
            $uid
        ]);

        $sms_scheduled = $this->db->num_rows();

        $this->db->query("SELECT id FROM wa_scheduled WHERE uid = ?", [
            $uid
        ]);

        $wa_scheduled = $this->db->num_rows();

        return $sms_scheduled + $wa_scheduled;
    }

    public function countContacts($uid)
    {
        $this->db->query("SELECT id FROM contacts WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countDevices($uid)
    {
        $this->db->query("SELECT id FROM devices WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countWaAccounts($uid)
    {
        $this->db->query("SELECT id FROM wa_accounts WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countKeys($uid)
    {
        $this->db->query("SELECT id FROM `keys` WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countWebhooks($uid)
    {
        $this->db->query("SELECT id FROM webhooks WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    public function countActions($uid)
    {
        $this->db->query("SELECT id FROM actions WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    /**
     * @coverage System
     * @desc Get functions
     */

    public function getUser($id)
    {
        $query = <<<SQL
SELECT u.id AS id, IF(u.id < 2, 1, 0) AS admin, MD5(u.id) AS hash, u.role AS role, u.email AS email, r.permissions AS permissions, u.name AS name, l.id AS language, l.rtl AS rtl, u.suspended AS suspended, u.timezone AS `timezone`, u.formatting AS formatting, u.country AS country, u.alertsound AS alertsound, u.confirmed AS confirmed
FROM users u
LEFT JOIN roles r ON u.role = r.id
LEFT JOIN languages l ON u.language = l.id
WHERE u.id = ?
SQL;

        return $this->db->query_one($query, [
            $id
        ]);
    }

    public function getUserByHash($hash)
    {
        $query = <<<SQL
SELECT u.id AS id, IF(u.id < 2, 1, 0) AS admin, MD5(u.id) AS hash, u.role AS role, u.email AS email, r.permissions AS permissions, u.name AS name, l.id AS language, l.rtl AS rtl, u.suspended AS suspended, u.timezone AS `timezone`, u.country AS country, u.alertsound AS alertsound, u.confirmed AS confirmed
FROM users u
LEFT JOIN roles r ON u.role = r.id
LEFT JOIN languages l ON u.language = l.id
WHERE MD5(u.id) = ?
SQL;

        return $this->db->query_one($query, [
            $hash
        ]);
    }

    public function getPartnership($id)
    {
        try {
            return $this->db->query_one("SELECT partner FROM users WHERE id = ?", [
                $id
            ])["partner"];
        } catch(Exception $e){
            return false;
        }
    }

    public function getSent($id)
    {
        return $this->db->query_one("SELECT id, uid, did, gateway, mode, phone, message, status FROM sent WHERE id = ?", [
            $id
        ]);
    }

    public function getWaSent($id)
    {
        return $this->db->query_one("SELECT id, wid, status FROM wa_sent WHERE id = ?", [
            $id
        ]);
    }

    public function getWaScheduled($id)
    {
        return $this->db->query_one("SELECT id, wid, message FROM wa_scheduled WHERE id = ?", [
            $id
        ]);
    }

    public function getPage($id)
    {
        return $this->db->query_one("SELECT id, roles, slug, logged, name, content FROM pages WHERE id = ?", [
            $id
        ]);
    }

    public function getVoucher($code)
    {
        return $this->db->query_one("SELECT id, package, code, name, duration FROM vouchers WHERE code = ?", [
            $code
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

    public function getTransaction($id)
    {
        return $this->db->query_one("SELECT id, uid, pid, type, price, currency, duration, provider, create_date FROM transactions WHERE id = ?", [
            $id
        ]);
    }

    public function getSubscription($id, $uid = false, $default = false)
    {   
        if($id):
            return $this->db->query_one("SELECT * FROM subscriptions WHERE id = ?", [
                $id
            ]);
        else:
            if($uid):
                try {
                    return $this->db->query_one("SELECT s.id AS sid, p.*, DATE_FORMAT(DATE_ADD(DATE(s.date), INTERVAL t.duration MONTH), '%M %e, %Y') AS expire_date FROM subscriptions s LEFT JOIN packages p ON s.pid = p.id LEFT JOIN transactions t ON s.tid = t.id WHERE s.uid = ?", [
                        $uid
                    ]);
                } catch(Exception $e){
                    return false;
                }
            endif;

            if($default):
                return $this->db->query_one("SELECT * FROM packages WHERE id < 2");
            endif;
        endif;

        return false;
    }

    public function getPayout($id)
    {
        return $this->db->query_one("SELECT id, uid, amount, currency, provider, address, create_date FROM payouts WHERE id = ?", [
            $id
        ]);
    }

    public function getLanguage($id)
    {
        return $this->db->query_one("SELECT id, rtl, iso, name, create_date FROM languages WHERE id = ?", [
            $id
        ]);
    }

    public function getLanguageRtl($id)
    {
        try {
            return $this->db->query_one("SELECT rtl FROM languages WHERE id = ?", [
                $id
            ])["rtl"];
        } catch(Exception $e){
            return false;
        }
    }

    public function getPassword($email)
    {
        return $this->db->query_one("SELECT id, password, suspended FROM users WHERE email = ?", [
            $email
        ]);
    }

    public function getEmail($id)
    {
        try {
            return $this->db->query_one("SELECT id, email FROM users WHERE id = ?", [
                $id
            ])["email"];
        } catch(Exception $e){
            return false;
        }
    }

    public function getCredits($id)
    {
        try {
            return $this->db->query_one("SELECT credits FROM users WHERE id = ?", [
                $id
            ])["credits"];
        } catch(Exception $e){
            return false;
        }
    }

    public function getEarnings($id)
    {
        try {
            return $this->db->query_one("SELECT earnings FROM users WHERE id = ?", [
                $id
            ])["earnings"];
        } catch(Exception $e){
            return false;
        }
    }

    public function getPlugin($id)
    {
        return $this->db->query_one("SELECT id, name, data FROM plugins WHERE id = ?", [
            $id
        ]);
    }

    public function getDevice($uid, $identifier, $type)
    {
        switch($type):
            case "id":
                return $this->db->query_one("SELECT id, did, uid, name, version, limit_status, limit_interval, limit_number, global_device, global_priority, global_slots, country, rate FROM devices WHERE uid = ? AND id = ?", [
                    $uid,
                    $identifier
                ]);
                break;
            case "did":
                return $this->db->query_one("SELECT id, did, uid, name, version, limit_status, limit_interval, limit_number, global_device, global_priority, global_slots, country, rate FROM devices WHERE uid = ? AND did = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            case "sid":
                return $this->db->query_one("SELECT id, did, uid, name, version, limit_status, limit_interval, limit_number, global_device, global_priority, global_slots, country, rate FROM devices WHERE online_id = ?", [
                    $identifier
                ]);

                break;
            case "global":
                return $this->db->query_one("SELECT id, did, uid, name, version, limit_status, limit_interval, limit_number, global_device, global_priority, global_slots, country, rate FROM devices WHERE did = ?", [
                    $identifier
                ]);

                break;
            default:
                return false;
        endswitch;
    }

    public function getWaAccount($uid, $identifier, $type)
    {
        switch($type):
            case "id":
                return $this->db->query_one("SELECT id, uid, wid, `unique`, receive_chats, random_send, random_min, random_max, create_date FROM wa_accounts WHERE uid = ? AND id = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            case "wid":
                return $this->db->query_one("SELECT id, uid, wid, `unique`, receive_chats, random_send, random_min, random_max, create_date FROM wa_accounts WHERE uid = ? AND wid = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            case "unique":
                return $this->db->query_one("SELECT id, uid, wid, `unique`, receive_chats, random_send, random_min, random_max, create_date FROM wa_accounts WHERE uid = ? AND `unique` = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            default:
                return false;
        endswitch;
    }

    public function getWaCampaign($uid, $identifier, $type)
    {
        switch($type):
            case "id":
                return $this->db->query_one("SELECT id, uid, wid, type, status, name, contacts, create_date FROM wa_campaigns WHERE uid = ? AND id = ?", [
                    $uid,
                    $identifier
                ]);

                break;
            default:
                return false;
        endswitch;
    }

    public function getWaGroups($uid, $wid)
    {
       $query = <<<SQL
SELECT id, uid, wid, gid, name, create_date
FROM wa_groups 
WHERE uid = ? AND wid = ?
SQL;

        $this->db->query($query, [
            $uid,
            $wid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["id"]] = $row;

            return $rows; 
        else:
            return [];
        endif; 
    }

    public function getLanguages()
    {
       $query = <<<SQL
SELECT id, rtl, LOWER(iso) as iso, name 
FROM languages 
ORDER BY `order` ASC
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

    public function getUsers()
    {
        $query = <<<SQL
SELECT id, IF(id < 2, 1, 0) AS admin, MD5(id) AS hash, email, name, language
FROM users
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

    public function getUsersByRole($role)
    {
        $query = <<<SQL
SELECT id, IF(id < 2, 1, 0) AS admin, MD5(id) AS hash, email, name, language
FROM users
WHERE role = ?
SQL;

        $this->db->query($query, [
            $role
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getContacts($id)
    {
        $query = <<<SQL
SELECT c.id AS id, c.phone AS phone, c.name AS name
FROM contacts c
WHERE c.uid = ?
SQL;

        $this->db->query($query, [
            $id
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getContactsByGroup($uid, $gid)
    {
        $query = <<<SQL
SELECT c.id AS id, c.phone AS phone, c.name AS name, g.name AS `group`
FROM contacts c
LEFT JOIN `groups` g ON ? = g.id
WHERE c.uid = ? AND FIND_IN_SET(?, c.groups)
SQL;

        $this->db->query($query, [
            $gid,
            $uid,
            $gid
        ]);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[] = $row;

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getDevices($uid)
    {
        $query = <<<SQL
SELECT id, uid, did, name, version, manufacturer, create_date
FROM devices
WHERE uid = ?
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

    public function getGlobalDevices($uid)
    {
        $query = <<<SQL
SELECT id, uid, did, name, version, manufacturer, create_date
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

    public function getGateways()
    {
        $query = <<<SQL
SELECT id, name, callback, callback_id, pricing, create_date
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

    public function getBlocks()
    {
        $query = <<<SQL
SELECT MD5(id) AS hash, content
FROM widgets WHERE type = 1
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["hash"]] = $row["content"];

            return $rows; 
        else:
            return [
                "none" => "none"
            ];
        endif;
    }

    public function getSettings()
    {
        $query = <<<SQL
SELECT name, value FROM settings
SQL;

        $this->db->query($query);

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["name"]] = $row["value"];

            return $rows; 
        else:
            return [];
        endif;
    }

    public function getPlugins()
    {
        $query = <<<SQL
SELECT id, name, data FROM plugins
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
SELECT id, send_limit AS send, receive_limit AS receive, device_limit AS devices, key_limit AS `keys`, webhook_limit AS webhooks, name, price
FROM packages
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

    public function getDefaultPackages()
    {
        $query = <<<SQL
SELECT id, FORMAT(send_limit, 0) AS send, FORMAT(receive_limit, 0) AS receive, FORMAT(contact_limit, 0) AS contacts, FORMAT(device_limit, 0) AS devices, FORMAT(key_limit, 0) AS `keys`, FORMAT(webhook_limit, 0) AS webhooks, name, price
FROM packages
WHERE id < 2 OR hidden > 1
LIMIT 3
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

    public function checkQuota($uid)
    {
        $this->db->query("SELECT id FROM quota WHERE uid = ?", [
            $uid
        ]);

        return $this->db->num_rows();
    }

    /**
     * @coverage Charts
     * @desc Get chart stats
     */

    public function getStatisticsSent($uid, $whatsapp = false, $admin = false)
    {   
        if(!$admin):
            if(!$whatsapp):
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM sent
WHERE DATE(create_date) > (NOW() - INTERVAL 7 DAY) AND uid = ? AND status = 3
ORDER BY create_date DESC
SQL;    
            else:
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM wa_sent
WHERE DATE(create_date) > (NOW() - INTERVAL 7 DAY) AND uid = ? AND status = 3
ORDER BY create_date DESC
SQL;
            endif;

            $this->db->query($query, [
                $uid
            ]);
        else:
            if(!$whatsapp):
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM sent
WHERE DATE(create_date) > (NOW() - INTERVAL 30 DAY) AND status = 3
ORDER BY create_date DESC
SQL;    
            else:
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM wa_sent
WHERE DATE(create_date) > (NOW() - INTERVAL 30 DAY) AND status = 3
ORDER BY create_date DESC
SQL;
            endif;

            $this->db->query($query);
        endif;
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getStatisticsReceived($uid, $whatsapp = false, $admin = false)
    {  
        if(!$admin):
            if(!$whatsapp):
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(receive_date)) AS receive_date
FROM received
WHERE DATE(receive_date) > (NOW() - INTERVAL 7 DAY) AND uid = ?
ORDER BY receive_date DESC
SQL;
            else:
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(receive_date)) AS receive_date
FROM wa_received
WHERE DATE(receive_date) > (NOW() - INTERVAL 7 DAY) AND uid = ?
ORDER BY receive_date DESC
SQL;
            endif;

            $this->db->query($query, [
                $uid
            ]);
        else:
            if(!$whatsapp):
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(receive_date)) AS receive_date
FROM received
WHERE DATE(receive_date) > (NOW() - INTERVAL 30 DAY)
ORDER BY receive_date DESC
SQL;
            else:
                $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(receive_date)) AS receive_date
FROM wa_received
WHERE DATE(receive_date) > (NOW() - INTERVAL 30 DAY)
ORDER BY receive_date DESC
SQL;
            endif;

            $this->db->query($query);
        endif;
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["receive_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getStatisticsEvents($uid, $type = 1)
    {
        $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM events
WHERE DATE(create_date) > (NOW() - INTERVAL 7 DAY) AND uid = ? AND type = ?
ORDER BY create_date DESC
SQL;

        $this->db->query($query, [
            $uid,
            $type
        ]);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getStatisticsUtilities($uid, $type = 1, $admin = false)
    {
        if(!$admin):
            $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM utilities
WHERE DATE(create_date) > (NOW() - INTERVAL 7 DAY) AND uid = ? AND type = ?
ORDER BY create_date DESC
SQL;
        $this->db->query($query, [
            $uid,
            $type
        ]);
        else:
            $query = <<<SQL
SELECT id, UNIX_TIMESTAMP(DATE(create_date)) AS create_date
FROM utilities
WHERE DATE(create_date) > (NOW() - INTERVAL 30 DAY) AND type = ?
ORDER BY create_date DESC
SQL;
        $this->db->query($query, [
            $type
        ]);
        endif;

        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getStatisticsVisitors($type)
    {
        if($type == "country"):
            $query = <<<SQL
SELECT v.id AS id, v.country AS {$type}, UNIX_TIMESTAMP(DATE(v.create_date)) AS create_date
FROM visitors v
WHERE DATE(v.create_date) > (NOW() - INTERVAL 7 DAY)
ORDER BY v.create_date DESC
SQL;
        elseif($type == "browser"):
            $query = <<<SQL
SELECT v.id AS id, v.browser AS {$type}, UNIX_TIMESTAMP(DATE(v.create_date)) AS create_date
FROM visitors v
WHERE DATE(v.create_date) > (NOW() - INTERVAL 7 DAY)
ORDER BY v.create_date DESC
SQL;
        else:
            $query = <<<SQL
SELECT v.id AS id, v.os AS {$type}, UNIX_TIMESTAMP(DATE(v.create_date)) AS create_date
FROM visitors v
WHERE DATE(v.create_date) > (NOW() - INTERVAL 7 DAY)
ORDER BY v.create_date DESC
SQL;
        endif;

        $this->db->query($query);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next())
                $rows[$row[$type]][$row["create_date"]][] = $row["id"];
           
            return $rows; 
        else:
            return [];
        endif;
    }

    public function getStatisticsEarnings($type = 1)
    {
        if($type == 1):
            $query = <<<SQL
SELECT t.id AS id, p.name AS package_name, t.price AS price, UNIX_TIMESTAMP(DATE(t.create_date)) AS create_date
FROM transactions t
LEFT JOIN packages p ON t.pid = p.id
WHERE DATE(t.create_date) > (NOW() - INTERVAL 30 DAY) AND pid > 0
ORDER BY t.create_date DESC
SQL;
        endif;

        if($type == 2):
            $query = <<<SQL
SELECT t.id AS id, t.price AS credits, UNIX_TIMESTAMP(DATE(t.create_date)) AS create_date
FROM transactions t
WHERE DATE(t.create_date) > (NOW() - INTERVAL 30 DAY) AND pid < 1
ORDER BY t.create_date DESC
SQL;
        endif;

        if($type == 3):
            $query = <<<SQL
SELECT c.id AS id, c.commission_amount AS amount, UNIX_TIMESTAMP(DATE(c.create_date)) AS create_date
FROM commissions c
WHERE DATE(c.create_date) > (NOW() - INTERVAL 30 DAY)
ORDER BY c.create_date DESC
SQL;
        endif;

        $this->db->query($query);
        
        if ($this->db->num_rows() > 0):
            while ($row = $this->db->next()):
                if($type == 1):
                    $rows[$row["package_name"]][$row["create_date"]][] = $row["price"];
                endif;

                if($type == 2):
                    $rows[$row["create_date"]][] = $row["credits"];
                endif;

                if($type == 3):
                    $rows[$row["create_date"]][] = $row["amount"];
                endif;
            endwhile;

            return $rows; 
        else:
            return [];
        endif;
    }

    /**
     * @coverage System
     * @desc Create, update & delete functions
     */

    public function clearUssd($uid)
    {
        return $this->db->query("DELETE FROM ussd WHERE uid = ? AND status < 3", [
            $uid
        ]);
    }

    public function clearCampaignSms($uid, $cid)
    {
        return $this->db->query("DELETE FROM sent WHERE uid = ? AND cid = ?", [
            $uid,
            $cid
        ]);
    }

    public function clearCampaignChats($uid, $cid)
    {
        return $this->db->query("DELETE FROM wa_sent WHERE uid = ? AND cid = ?", [
            $uid,
            $cid
        ]);
    }

    public function updateOnline($did)
    {
        try {
            $this->db->where("did", $did);
            return $this->db->update("devices", [
                "online_status" => 1
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateOffline($id)
    {
        try {
            $this->db->where("online_id", $id);
            return $this->db->update("devices", [
                "online_status" => 2
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function credits($id, $type, $credits)
    {   
        if($type == "increase"):
            $query = <<<SQL
UPDATE users SET credits = (credits + {$credits}) WHERE id = ?
SQL;
        else:
            $query = <<<SQL
UPDATE users SET credits = (credits - {$credits}) WHERE id = ?
SQL;
        endif;

        return $this->db->query($query, [
            $id
        ]);
    }

    public function earnings($id, $type, $credits)
    {   
        if($type == "increase"):
            $query = <<<SQL
UPDATE users SET earnings = (earnings + {$credits}) WHERE id = ?
SQL;
        else:
            $query = <<<SQL
UPDATE users SET earnings = (earnings - {$credits}) WHERE id = ?
SQL;
        endif;

        return $this->db->query($query, [
            $id
        ]);
    }

    public function increment($uid, $column)
    {   
        $query = <<<SQL
UPDATE quota SET {$column} = {$column} + 1 WHERE uid = ? LIMIT 1
SQL;

        return $this->db->query($query, [
            $uid
        ]);
    }

    public function settings($name, $value)
    {
        try {
            $this->db->where("name", $name);
            return $this->db->update("settings", [
                "value" => $value
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($table, $data)
    {
        try {
            $this->db->insert($table, $data);
            return $this->db->last_insert_id();
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($id, $uid, $table, $data)
    {
        try {
            if($id)
                $this->db->where("id", $id);

            if($uid)
                $this->db->where("uid", $uid);
            
            return $this->db->update($table, $data);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($uid, $id, $table, $customArray = [])
    {
        try {
            if($id)
                $this->db->where("id", $id);
            
            if($uid)
                $this->db->where("uid", $uid);

            if(!empty($customArray)):
                foreach($customArray as $key => $value):
                    $this->db->where($key, $value);
                endforeach;
            endif;

            return $this->db->delete($table);
        } catch (Exception $e) {
            return false;
        }
    }
}