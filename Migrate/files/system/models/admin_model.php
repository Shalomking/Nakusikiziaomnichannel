<?php

class Admin_Model extends MVC_Model
{
	public function getUsers($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, role, email, credits, earnings, name, country, language, alertsound, suspended, timezone, partner, create_date
FROM users 
LIMIT {$page}, {$limit}
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

    public function getRoles($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT * FROM roles LIMIT {$page}, {$limit}
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

    public function getPackages($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT * FROM packages LIMIT {$page}, {$limit}
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

    public function getVouchers($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT * FROM vouchers LIMIT {$page}, {$limit}
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

    public function getSubscriptions($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT * FROM subscriptions LIMIT {$page}, {$limit}
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

    public function getTransactions($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT * FROM transactions LIMIT {$page}, {$limit}
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

    public function getLanguages($page = 1, $limit = 10)
    {
        $page = $page < 2 ? 0 : $page * $limit;

        $query = <<<SQL
SELECT id, rtl, iso, `order`, name
FROM languages 
LIMIT {$page}, {$limit}
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