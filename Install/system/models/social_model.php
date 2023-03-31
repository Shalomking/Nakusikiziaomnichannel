<?php

class Social_Model extends MVC_Model
{
	public function checkIdentifier($id)
	{
		$this->db->query("SELECT id FROM users WHERE providers LIKE ?", [
			"%{$id}%"
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

	public function getUserById($id)
	{
        $query = <<<SQL
SELECT u.id AS id, IF(u.id < 2, 1, 0) AS admin, MD5(u.id) AS hash, u.role AS role, u.email AS email, r.permissions AS permissions, u.name AS name, l.id AS language, l.rtl AS rtl, u.suspended AS suspended, u.timezone AS `timezone`, u.country AS country, u.alertsound AS alertsound
FROM users u
LEFT JOIN roles r ON u.role = r.id
LEFT JOIN languages l ON u.language = l.id
WHERE u.id = ?
SQL;

        return $this->db->query_one($query, [
            $id
        ]);
	}

	public function getUserByEmail($email)
	{
        $query = <<<SQL
SELECT u.id AS id, IF(u.id < 2, 1, 0) AS admin, MD5(u.id) AS hash, u.role AS role, u.email AS email, r.permissions AS permissions, u.name AS name, l.id AS language, l.rtl AS rtl, u.suspended AS suspended, u.timezone AS `timezone`, u.country AS country, u.alertsound AS alertsound
FROM users u
LEFT JOIN roles r ON u.role = r.id
LEFT JOIN languages l ON u.language = l.id
WHERE u.email = ?
SQL;

        return $this->db->query_one($query, [
            $email
        ]);
	}

	public function getUserByIdentifier($id)
	{
        $query = <<<SQL
SELECT u.id AS id, IF(u.id < 2, 1, 0) AS admin, MD5(u.id) AS hash, u.role AS role, u.email AS email, r.permissions AS permissions, u.name AS name, l.id AS language, l.rtl AS rtl, u.suspended AS suspended, u.timezone AS `timezone`, u.country AS country, u.alertsound AS alertsound
FROM users u
LEFT JOIN roles r ON u.role = r.id
LEFT JOIN languages l ON u.language = l.id
WHERE u.providers LIKE ?
SQL;

        return $this->db->query_one($query, [
            "%{$id}%"
        ]);
	}    

	public function updateSocial($email, $data)
    {
        try {
            $this->db->where("email", $email);
            return $this->db->update("users", $data);
        } catch (Exception $e) {
            return false;
        }
    }
}