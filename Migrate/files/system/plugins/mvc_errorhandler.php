<?php
/**
 * Name: MVC Framework
 * About: Titan Systems MVC Framework
 * Copyright: 2020, All Rights Reserved.
 * Author: Titan Systems <mail@titansystems.ph>
 */

/**
 * MVC_ErrorHandler
 * A simple exception handler to display exceptions in a formatted box
 * @package MVC
 * @author Titan Systems <mail@titansystems.ph>
 */

function MVC_ErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (error_reporting() === 0)
        return;
    
    if (error_reporting() & $errno)
        throw new MVC_ExceptionHandler($errstr, $errno, $errno, $errfile, $errline);
}
