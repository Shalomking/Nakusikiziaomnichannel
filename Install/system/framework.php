<?php
/**
 * MVC Framework
 * @author Titan Systems <mail@titansystems.ph>
 */

/**
 * Initial Configs
 */

set_include_path(
    get_include_path()
    . PATH_SEPARATOR . "system/configurations" . DIRECTORY_SEPARATOR
    . PATH_SEPARATOR . "system/controllers" . DIRECTORY_SEPARATOR
    . PATH_SEPARATOR . "system/models" . DIRECTORY_SEPARATOR
    . PATH_SEPARATOR . "system/plugins". DIRECTORY_SEPARATOR
    . PATH_SEPARATOR . "system/plugins/libraries". DIRECTORY_SEPARATOR
);

spl_autoload_extensions(".php, .inc");

$autoload = spl_autoload_functions();

if (!$autoload):
  spl_autoload_register();
elseif(!in_array("spl_autoload", $autoload)):
  spl_autoload_register("spl_autoload");
endif;

/**
 * Runtime
 */

require "mvc_errorhandler.php";
require "mvc_functions.php";

/**
 * Environment Configs
 */

require "mvc_application.php";
require "mvc_database.php";
require "mvc_system.php";

/** 
 * Initialize
 */

$init = new MVC;
$init->run();

/**
 * Framework Class
 * @package	MVC Framewok
 * @author Titan Systems <mail@titansystems.ph>
 */

class MVC
{
    public $config = configuration;
    public $controller = false;
    public $action = false;
    public $path_info = false;
    public $url_segments = false;
  
    /**
     * Class constructor
     * @access public
     */
    
    public function __construct($id = "default")
    {
        self::instance($this, $id);
    }
  
    /**
     * Main method of execution
     * @access public
     */
    
    public function run()
    {
        $this->path_info = env["subdir"] ? (String) Stringy\create($_SERVER["REQUEST_URI"])->removeLeft(env["subdir"]) : $_SERVER["REQUEST_URI"];

        $this->errors();
        $this->segments();
        $this->controllers();
        $this->actions();
        $this->autoloaders();

        return $this->controller->{$this->action}();
    }
  
    /**
     * Setup error handling for mvc
     * @access public
     */
    
    private function errors()
    {
      if (defined("error_handler") && error_handler):
          set_exception_handler([
            "MVC_ExceptionHandler", 
            "handleException"
          ]);

          set_error_handler("MVC_ErrorHandler");
      endif;
    }

    /**
     * Setup url segments array
     * @access	public
     */
    
    private function segments()
    {
        $this->url_segments = !empty($this->path_info) ? array_filter(explode("/", $this->path_info)) : false;
    }
  
    /**
     * Setup controller
     * @access public
     */
    
    private function controllers()
    {
        if (!empty($this->config["root_controller"])):
            $controller_name = $this->config["root_controller"];
            $controller_file = "{$controller_name}.php";
        else:
            $controller_name = !empty($this->url_segments[1]) ? (isset(parse_url($this->url_segments[1])["path"]) ? parse_url($this->url_segments[1])["path"] : false) : $this->config["default_controller"];

            $controller_name = in_array($controller_name, ["index"]) ? "default" : $controller_name;

            $controller_file = "{$controller_name}.php";

            if (!stream_resolve_include_path($controller_file)):
                $controller_name = $this->config["default_controller"];
                $controller_file = "{$controller_name}.php";
            endif;
        endif;
    
        require $controller_file;
    
        $controller_class = "{$controller_name}_Controller";
      
        $this->controller = new $controller_class(true);
    }
  
    /**
     * Setup controller method (action) to execute
     * @access public
     */
    private function actions()
    {
        if (!empty($this->config["root_action"])):
            $this->action = $this->config["root_action"];
        else:
            $this->action = !empty($this->url_segments[2]) ? (isset(parse_url($this->url_segments[2])["path"]) ? parse_url($this->url_segments[2])["path"] : "index") : (!empty($this->config["default_action"]) ? $this->config["default_action"] : "index");
        endif;
    }
  
    /**
     * Autoload any libs/models
     * @access public
     */
    
    private function autoloaders()
    {
        if (!empty(autoload["libraries"])):
            foreach (autoload["libraries"] as $library):
                if (is_array($library))
                    $this->controller->load->library($library[0], $library[1]);
                else
                    $this->controller->load->library($library);
            endforeach;
        endif;
        
        if (!empty(autoload["models"])):
            foreach (autoload["models"] as $model):
                if (is_array($library))
                    $this->controller->load->model($model[0], $model[1]);
                else
                    $this->controller->load->model($model);
            endforeach;
        endif;
    }

    /**
     * Instance
     * Get/set the main object instance(s)
     * @access public
     * @param object $new_instance reference to new object instance
     * @param string $id object instance id
     * @return object $instance reference to object instance
     */
    
    public static function &instance($new_instance = false, $id = "default")
    {
        static $instance = [];

        if (isset($new_instance) && is_object($new_instance))
            $instance[$id] = $new_instance;
        
        return $instance[$id];
    }
}