<?php
/**
 * Name: MVC Framework
 * About: Titan Systems MVC Framework
 * Copyright: 2020, All Rights Reserved.
 * Author: Titan Systems <mail@titansystems.ph>
 */


/**
 * Define SQL actions
 */

if (!defined('MVC_SQL_NONE'))
    define('MVC_SQL_NONE', 0);

if (!defined('MVC_SQL_INIT'))
    define('MVC_SQL_INIT', 1);

if (!defined('MVC_SQL_ALL'))
    define('MVC_SQL_ALL', 2);

/**
 * MVC_PDO
 * @package MVC
 * @author Titan Systems <mail@titansystems.ph>
 */

class MVC_PDO
{
    /**
     * $pdo
     * The PDO object handle
     * @access public
     */
    
    public $pdo = false;
  
    /**
     * $result
     * The query result handle
     * @access public
     */
    
    public $result = false;
  
    /**
     * $fetch_mode
     * The results fetch mode
     * @access public
     */
    
    public $fetch_mode = PDO::FETCH_ASSOC;

    /**
     * $query_params
     * @access public
     */
    
    public $query_params = [
        "select" => 
        "*"
    ];

    /**
     * $last_query
     * @access public
     */
    
    public $last_query = false;

    /**
     * $last_query_type
     * @access public
     */
    
    public $last_query_type = false;
  
    /**
     * Class constructor
     * @access public
     */
    
    public function __construct($config)
    {
        if (!class_exists("PDO", false))
            throw new Exception("PHP PDO package is required!");
     
        if (empty($config))
            throw new Exception("Database definitions required!");

        $config["port"] = isset($config["port"]) ? $config["port"] : 3306;

        if (!empty($config["dsn"])):
            $dsn = $config["dsn"];
        elseif ($config["type"] == "sqlsrv"):
            $dsn = "{$config["type"]}:Server={$config["host"]};Database={$config["name"]}";
        else:
            $dsn = "{$config["type"]}:host={$config["host"]};port={$config["port"]};dbname={$config["name"]};charset=utf8mb4";
        endif;
     
        if(isset(env["installed"])):
            try {
                $this->pdo = new PDO(
                    $dsn,
                    $config["user"],
                    $config["pass"]
                );

                $this->pdo->exec("SET NAMES utf8mb4");
                $this->pdo->exec("SET CHARACTER SET utf8mb4");
            } catch (PDOException $e) {
                throw new Exception(sprintf("Can't connect to database \"{$config["type"]}\". Error: %s", $e->getMessage()));
            }
        
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        endif;
    }

    /**
     * SELECT
     * Set the active record select clause
     * @access public
     * @param string $clause
     */
    
    public function select($clause)
    {
        return $this->query_params["select"] = $clause;
    }

    /**
     * FROM
     * Set the active record from clause
     * @access public
     * @param string $clause
     */
    
    public function from($clause)
    {
        return $this->query_params["from"] = $clause;
    }

    /**
     * WHERE
     * Set the active record where clause
     * @access public
     * @param string $clause
     */
    
    public function where($clause, $args)
    {
        if (empty($clause))
            throw new Exception(sprintf("where cannot be empty"));
  
        if (!preg_match('![=<>]!', $clause))
            $clause .= "=";
  
        if (strpos($clause, "?") === false)
            $clause .= "?";
      
        $this->_where($clause, (array) $args, "AND");
    }

    /**
     * ORWHERE
     * Set the active record orwhere clause
     * @access public
     * @param string $clause
     */
    
    public function orwhere($clause, $args)
    {
        $this->_where($clause, $args, "OR");
    }
  
    /**
     * _WHERE
     * Set the active record where clause
     * @access public
     * @param string $clause
     */
    
    private function _where($clause, $args = [], $prefix = "AND")
    {
        if (empty($clause))
            return false;

        if (($count = substr_count($clause, "?")) && (count($args) != $count))
            throw new Exception(sprintf("Number of where clause args don't match number of ?: '%s'", $clause));
      
        if (!isset($this->query_params["where"]))
            $this->query_params["where"] = [];
      
        return $this->query_params["where"][] = [
            "clause" => $clause, 
            "args" => $args, 
            "prefix" => $prefix
        ];
    }

    /**
     * JOIN
     * Set the active record join clause
     * @access public
     * @param string $clause
     */
    
    public function join($join_table, $join_on, $join_type = false)
    {
        $clause = "JOIN {$join_table} ON {$join_on}";
    
        if (!empty($join_type))
            $clause = $join_type . " " . $clause;
    
        if (!isset($this->query_params["join"]))
            $this->query_params["join"] = [];
      
        $this->query_params["join"][] = $clause;
    }

    /**
     * IN
     * Set an active record IN clause
     * @access public
     * @param string $clause
     */
    
    public function in($field, $elements, $list = false)
    {
        $this->_in($field, $elements, $list, "AND");
    }

    /**
     * ORIN
     * Set an active record OR IN clause
     * @access public
     * @param string $clause
     */
    
    public function orin($field, $elements, $list = false)
    {
        $this->_in($field, $elements, $list, "OR");
    }

  
    /**
     * _IN
     * Set an active record IN clause
     * @access public
     * @param string $clause
     */
    
    private function _in($field, $elements, $list = false, $prefix = "AND")
    {
        if (!$list):
            if (!is_array($elements)) 
                $elements = explode(",", $elements);
        
            foreach ($elements as $idx => $element)
                $elements[$idx] = $this->pdo->quote($element);
      
            $clause = sprintf("{$field} IN (%s)", implode(",", $elements));
        else:
            $clause = sprintf("{$field} IN (%s)", $elements);
        endif;
    
        $this->_where($clause, [], $prefix);
    }
  
    /**
     * ORDERBY
     * Set the active record orderby clause
     * @access public
     * @param string $clause
     */
    
    public function orderby($clause)
    {
        $this->_set_clause("orderby", $clause);
    }

    /**
     * GROUPBY
     * Set the active record groupby clause
     * @access public
     * @param string $clause
     */
    
    public function groupby($clause)
    {
        $this->_set_clause("groupby", $clause);
    }

    /**
     * LIMIT
     * Set the active record limit clause
     * @access public
     * @param int $limit
     * @param int $offset
     */
    
    public function limit($limit, $offset = 0)
    {
        if (!empty($offset))
            $this->_set_clause("limit", sprintf("%d,%d", (int) $offset, (int) $limit));
        else
            $this->_set_clause("limit", sprintf("%d", (int) $limit));
    }
  
    /**
     * _SET_CLAUSE
     * Set an active record clause
     * @access public
     * @param string $clause
     */
    
    private function _set_clause($type, $clause, $args = [])
    {
        if (empty($type) || empty($clause))
            return false;
      
        $this->query_params[$type] = [
            "clause" => 
            $clause
        ];
    
        if (isset($args))
            $this->query_params[$type]["args"] = $args;
    }
  
    /**
     * _QUERY_ASSEMBLE
     * Get an active record query
     * @access public
     * @param string $fetch_mode the PDO fetch mode
     */
    
    private function _query_assemble(&$params, $fetch_mode = false)
    {
        if (empty($this->query_params["from"])):
            throw new Exception("Unable to get(), set from() first");
            return false;
        endif;
    
        $query = [];
        $query[] = "SELECT {$this->query_params["select"]}";
        $query[] = "FROM {$this->query_params["from"]}";

        if (!empty($this->query_params["join"])):
            foreach ($this->query_params["join"] as $cjoin)
                $query[] = $cjoin;
        endif;
    
        if ($where = $this->_assemble_where($where_string, $params))
            $query[] = $where_string;

        if (!empty($this->query_params["groupby"]))
            $query[] = "GROUP BY {$this->query_params["groupby"]["clause"]}";
    
        if (!empty($this->query_params["orderby"]))
            $query[] = "ORDER BY {$this->query_params["orderby"]["clause"]}";
    
        if (!empty($this->query_params["limit"]))
            $query[] = "LIMIT {$this->query_params["limit"]["clause"]}";
        
        $query_string = implode(" ", $query);
        $this->last_query = $query_string;
        $this->query_params = [
            "select" => "*"
        ];
    
        return $query_string;
    }
  
    /**
     * _ASSEMBLE_WHERE
     * Assemble where query
     * @access private
     */
    
    private function _assemble_where(&$where, &$params)
    {
        if (!empty($this->query_params["where"])):
            $where_init = false;
            $where_parts = [];
            $params = [];

            foreach ($this->query_params["where"] as $cwhere) {
                $prefix = !$where_init ? "WHERE" : $cwhere['prefix'];
                $where_parts[] = "{$prefix} {$cwhere["clause"]}";
                $params = array_merge($params, (array) $cwhere["args"]);
                $where_init = true;
            }

            $where = implode(" ", $where_parts);

            return true;
        endif;

        return false;
    }
  
    /**
     * QUERY
     * Execute a database query
     * @access public
     * @param array $params an array of query params
     * @param int $fetch_mode the fetch formatting mode
     */
    
    public function query($query = false, $params = [], $fetch_mode = false)
    {
        if (!$query)
            $query = $this->_query_assemble($params, $fetch_mode);
  
        return $this->_query($query, $params, MVC_SQL_NONE, $fetch_mode);
    }

    /**
     * QUERY_ALL
     * Execute a database query, return all records
     * @access public
     * @param array $params an array of query params
     * @param int $fetch_mode the fetch formatting mode
     */
    
    public function query_all($query = false, $params = [], $fetch_mode = false)
    {
        if (!$query)
            $query = $this->_query_assemble($params, $fetch_mode);
  
        return $this->_query($query, $params, MVC_SQL_ALL, $fetch_mode);
    }

    /**
     * QUERY_ONE
     * Execute a database query, return one record
     * @access public
     * @param array $params an array of query params
     * @param int $fetch_mode the fetch formatting mode
     */
    
    public function query_one($query = false, $params = [], $fetch_mode = false)
    {
        if (!$query):
            $this->limit(1);
            $query = $this->_query_assemble($params, $fetch_mode);
        endif;
  
        return $this->_query($query, $params, MVC_SQL_INIT, $fetch_mode);
    }
  
    /**
     * _QUERY
     * Internal query method
     * @access private
     * @param string $query the query string
     * @param array $params an array of query params
     * @param int $return_type none/all/init
     * @param int $fetch_mode the fetch formatting mode
     */
    
    public function _query($query, $params = [], $return_type = MVC_SQL_NONE, $fetch_mode = false)
    {
        if (!$fetch_mode)
            $fetch_mode = PDO::FETCH_ASSOC;

        $timezone = (new DateTime("now", new DateTimeZone(defined("logged_timezone") && !empty(logged_timezone) ? logged_timezone : "UTC")))->format("P");

        try {
            $this->pdo->exec("SET time_zone = \"{$timezone}\"");
        } catch (PDOException $e) {
            throw new Exception(sprintf("PDO Error: %s Query: %s", $e->getMessage(), $query));
            return false;
        }
  
        try {
            $this->result = $this->pdo->prepare($query);
        } catch (PDOException $e) {
            throw new Exception(sprintf("PDO Error: %s Query: %s", $e->getMessage(), $query));
            return false;
        }
    
        try {
            $this->result->execute($params);
        } catch (PDOException $e) {
            throw new Exception(sprintf("PDO Error: %s Query: %s", $e->getMessage(), $query));
            return false;
        }
  
        $this->result->setFetchMode($fetch_mode);
  
        switch ($return_type) {
            case MVC_SQL_INIT:
                return $this->result->fetch();
                break;
            case MVC_SQL_ALL:
                return $this->result->fetchAll();
                break;
            case MVC_SQL_NONE:
            default:
                return true;
                break;
        }
    }

    /**
     * UPDATE
     * Update records
     * @access public
     * @param int $fetch_mode the fetch formatting mode
     */
    
    public function update($table, $columns)
    {
        if (empty($table)):
            throw new Exception("Unable to update, table name required!");
            return false;
        endif;

        if (empty($columns) || !is_array($columns)):
            throw new Exception("Unable to update, at least one column required!");
            return false;
        endif;

        $query = [
            "UPDATE {$table} SET"
        ];
        $fields = [];
        $params = [];

        foreach ($columns as $cname => $cvalue) {
            if (!empty($cname)):
                $fields[] = "`{$cname}`=?";
                $params[] = $cvalue;
            endif;
        }

        $query[] = implode(",", $fields);
    
        if ($this->_assemble_where($where_string, $where_params)):
            $query[] = $where_string;
            $params = array_merge($params, $where_params);
        endif;

        $query = implode(" ", $query);
    
        $this->query_params = [
            "select" => "*"
        ];
    
        return $this->_query($query, $params);
    }

    /**
     * INSERT
     * Insert records
     * @access public
     * @param string $table
     * @param array $columns
     */
    
    public function insert($table, $columns)
    {
        if (empty($table)):
            throw new Exception("Unable to insert, table name required!");
            return false;
        endif;

        if (empty($columns) || !is_array($columns)):
            throw new Exception("Unable to insert, at least one column required!");
            return false;
        endif;
    
        $column_names = array_keys($columns);
    
        $query = [
            sprintf("INSERT INTO `{$table}` (`%s`) VALUES", implode("`,`", $column_names))
        ];
        $fields = [];
        $params = [];

        foreach ($columns as $cname => $cvalue) {
            if (!empty($cname)):
                $fields[] = "?";
                $params[] = $cvalue;
            endif;
        }

        $query[] = "(" . implode(",", $fields) . ")";
        $query = implode(" ", $query);
        $this->_query($query, $params);

        return $this->last_insert_id();
    }

  
    /**
     * DELETE
     * Delete records
     * @access public
     * @param string $table
     * @param array $columns
     */
    
    public function delete($table)
    {
        if (empty($table)):
            throw new Exception("Unable to delete, table name required!");
            return false;
        endif;

        $query = [
            "DELETE FROM `{$table}`"
        ];
        $params = [];
    
        if ($this->_assemble_where($where_string, $where_params)):
            $query[] = $where_string;
            $params = array_merge($params, $where_params);
        endif;

        $query = implode(" ", $query);
    
        $this->query_params = [
            "select" => "*"
        ];
    
        return $this->_query($query, $params);
    }
  
    /**
     * NEXT
     * Go to next record in result set
     * @access public
     * @param int $fetch_mode the fetch formatting mode
     */
    
    public function next($fetch_mode = false)
    {
        if ($fetch_mode):
            $this->result->setFetchMode($fetch_mode);
        endif;

        return $this->result->fetch();
    }

    /**
     * LAST_INSERT_ID
     * Get last insert id from previous query
     * @access public
     * @return int $id
     */
    
    public function last_insert_id()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * NUM_ROWS
     * Get number of returned rows from previous select
     * @access public
     * @return int $id
     */
    
    public function num_rows()
    {
        return $this->result->rowCount();
    }

    /**
     * AFFECTED_ROWS
     * Get number of affected rows from previous insert/update/delete
     * @access public
     * @return int $id
     */
    
    public function affected_rows()
    {
        return $this->result->rowCount();
    }
  
    /**
     * LAST_QUERY
     * Return last query executed
     * @access public
     */
    
    public function last_query()
    {
        return $this->last_query;
    }

    /**
     * Class destructor
     * @access public
     */
    
    public function __destruct()
    {
        $this->pdo = false;
    }
}