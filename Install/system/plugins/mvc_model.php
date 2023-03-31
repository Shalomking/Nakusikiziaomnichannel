<?php
/**
 * Name: MVC Framework
 * About: Titan Systems MVC Framework
 * Copyright: 2020, All Rights Reserved.
 * Author: Titan Systems <mail@titansystems.ph>
 */

/**
 * MVC_Model
 * @package MVC
 * @author Titan Systems <mail@titansystems.ph>
 */

class MVC_Model
{
    /**
     * $db
     * The database object instance
     * @access public
     */
    
    public $db = false;
    
    /**
     * Class constructor
     * @access public
     */
    
    public function __construct($poolname = false)
    {
        $this->db = mvc::instance()->controller->load->database($poolname);
    }
}
