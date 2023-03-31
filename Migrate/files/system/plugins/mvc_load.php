<?php
/**
 * Name: MVC FRAMEWORK
 * About: Titan Systems MVC Framework
 * Copyright: 2020, All Rights Reserved.
 * Author: Titan Systems <mail@titansystems.ph>
 */

/**
 * MVC_Load
 * @package MVC
 * @author Titan Systems <mail@titansystems.ph>
 */

class MVC_Load
{
    /**
     * Class constructor
     * @access public
     */
    
    public function __construct(){}

    /**
     * Model
     * Load a model object
     * @access public
     * @param string $model_name the name of the model class
     * @param string $model_alias the property name alias
     * @param string $filename the filename
     * @param string $pool_name the database pool name to use
     * @return boolean
     */
    
    public function model($model_name, $model_alias = false, $filename = false, $pool_name = false)
    {

        if (!$model_alias)
            $model_alias = $model_name;

        if (!$filename)
            $filename = strtolower($model_name) . ".php";

        if (empty($model_alias))
            throw new Exception("Model name cannot be empty!");

        if (!preg_match('!^[a-zA-Z][a-zA-Z0-9_]+$!', $model_alias))
            throw new Exception("Model name \"{$model_alias}\" has invalid syntax!");
      
        if (method_exists($this, $model_alias))
            throw new Exception("Model name \"{$model_alias}\" is invalid (reserved) name!");

        $controller = mvc::instance(false, "controller");
    
        if (isset($controller->$model_alias)):
            return true;
        else:
            return $controller->$model_alias = new $model_name($pool_name);
        endif;
    }

    /**
     * Library
     * Load a library plugin
     * @access public
     * @param string $class_name the class name
     * @param string $alias the property name alias
     * @param string $filename the filename
     * @return boolean
     */
    
    public function library($lib_name, $alias = false, $filename = false)
    {

        if (!$alias)
            $alias = $lib_name;

        if (empty($alias))
            throw new Exception("Library name cannot be empty!");

        if (!preg_match('!^[a-zA-Z][a-zA-Z_]+$!', $alias))
            throw new Exception("Library name \"{$alias}\" has invalid syntax!");
      
        if (method_exists($this, $alias))
            throw new Exception("Library name \"{$alias}\" has invalid (reserved) name!");
    
        $controller = mvc::instance(false, "controller");

        if (isset($controller->$alias)):
            return true;
        else:
            $class_name = "MVC_Library_{$lib_name}";
            return $controller->$alias = new $class_name;
        endif;
    }

    /**
    * Database
    * Returns a database plugin object
    * @access	public
    * @param string $poolname the name of the database pool (if NULL default pool is used)
    * @return	object
    */
    
    public function database($poolname = false)
    {
        static $dbs = [];
        static $config = database;

        if (!$poolname)
            $poolname = "default";

        if ($poolname && isset($dbs[$poolname]))
            return $dbs[$poolname];

        if ($poolname && isset($config[$poolname]) && !empty($config[$poolname]["plugin"])):
            $dbs[$poolname] = new $config[$poolname]["plugin"]($config[$poolname]);
            return $dbs[$poolname];
        endif;
    }
}
