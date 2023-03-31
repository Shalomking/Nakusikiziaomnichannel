<?php
/**
 * Name: MVC Framework
 * About: Titan Systems MVC Framework
 * Copyright: 2020, All Rights Reserved.
 * Author: Titan Systems <mail@titansystems.ph>
 */


/**
 * MVC_Controller
 * @package	MVC
 * @author Titan Systems <mail@titansystems.ph>
 */

class MVC_Controller
{

    /**
     * Class constructor
     * @access public
     */

    public function __construct()
    {
        mvc::instance($this, "controller");
        $this->load = new MVC_Load;
    }

    /**
     * __call
     * Gets called when an unspecified method is used
     * @access public
     */
    
    public function __call($function, $args)
    {
        return $this->index();
    }
}
