<?php
/**
 * @desc Helper functions
 */

function get_configs($env = false, $ver = false)
{   
    if($env):
        if(!file_exists($env)):
            die("Environment config not found!");
        endif;

        $configs = explode("\n", file_get_contents($env));
    else:
        if(!file_exists("system/configurations/cc_env.inc")):
            die("Environment config not found!");
        endif;

        $configs = explode("\n", file_get_contents("system/configurations/cc_env.inc"));
    endif;

    foreach($configs AS $config):
        $line = explode("<=>", $config);
        $vals[$line[0]] = (isset($line[1]) ? trim($line[1]) : false);
    endforeach;

    $vals["siteurl"] = (isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : php_uname("n"));
    $vals["port"] = (in_array($_SERVER["SERVER_PORT"], [80, 443]) ? false : ":{$_SERVER["SERVER_PORT"]}");
    $vals["subdir"] = (empty(explode("/", dirname($_SERVER["SCRIPT_NAME"]))[1]) ? false : dirname($_SERVER["SCRIPT_NAME"]));

    define("env", $vals);

    if(!isset(env["installed"]) && !Stringy\create($_SERVER["REQUEST_URI"])->contains("install"))
        header("location: ./install");

    return get_version($ver);
}

function get_version($ver)
{
    if($ver):
        if(!file_exists($ver)):
            die("Version config not found!");
        endif;

        $version = file_get_contents($ver);
    else:
        if(!file_exists("system/configurations/cc_ver.inc")):
            die("Version config not found!");
        endif;

        $version = file_get_contents("system/configurations/cc_ver.inc");
    endif;

    return define("version", $version);
}

function site_url($path = false, $protocol = false)
{
    return $protocol ? str_replace("//", (system_protocol < 2 ? "http://" : "https://"), rtrim(site_url, "/")) . "/" . ltrim($path, "/") : site_url . "/{$path}";
}

function set_template($name)
{
    return define("template", $name);
}

function set_language($id, $rtl = 2)
{
    $GLOBALS["__"] = "__";
    $GLOBALS["___"] = "___";

    $rtl = $rtl ?: 2;

    $file = md5($id);

    if(file_exists("system/languages/{$file}.lang")):
        $lines = explode("\n", file_get_contents("system/languages/{$file}.lang"));
    else:
        $lines = explode("\n", file_get_contents("system/storage/temporary/default.lang"));
    endif;

    $langStrings = [];

    foreach($lines as $line):
        if(Stringy\create($line)->contains("===")):
            $columns = explode("===", trim($line));
            $langStrings["lang_" . trim($columns[0])] = trim($columns[1]);            
        endif;
    endforeach;

    if(!defined("language"))
        define("language", $langStrings);

    if(!defined("language_rtl"))
        define("language_rtl", $rtl < 2 ? true : false);
}

function set_blocks($blocks)
{
    foreach($blocks as $key => $value):
        define("block_{$key}", $value);
    endforeach;
}

function set_logged($user = [])
{
    if(empty($user)):
        $user = [
            "id" => false,
            "admin" => false,
            "hash" => false,
            "email" => false,
            "name" => false,
            "rtl" => 2,
            "permissions" => false,
            "country" => false,
            "alertsound" => 2,
            "timezone" => "asia/manila",
            "formatting" => []
        ];
    endif;

    date_default_timezone_set(
        $user["timezone"] ?: "UTC"
    );

    $user["language"] = isset($_SESSION["language"]) ? $_SESSION["language"] : (isset($_SESSION["logged"]["language"]) ? $_SESSION["logged"]["language"] : system_default_lang);

    $formatting = isset($user["formatting"]) && !empty($user["formatting"]) ? json_decode($user["formatting"], true) : [
        "clock" => "g:i A",
        "date" => "n/j/Y",
        "container" => [
            "clock_format" => 1,
            "date_format" => 1,
            "date_separator" => 2,
            "separator_selected" => "/"
        ]
    ];  

    $user["clock_format"] = $formatting["clock"];
    $user["date_format"] = $formatting["date"];

    $permissions = [
        "manage_users",
        "manage_roles",
        "manage_packages",
        "manage_vouchers",
        "manage_subscriptions",
        "manage_transactions",
        "manage_payouts",
        "manage_widgets",
        "manage_pages",
        "manage_marketing",
        "manage_languages",
        "manage_gateways",
        "manage_shorteners",
        "manage_plugins",
        "manage_templates",
        "manage_api"
    ];

    $permissionArray = [];

    $userPermissions = explode(",", is_null($user["permissions"]) ? "" : $user["permissions"]);

    if($user["id"] < 2)
        define("super_admin", true);
    else
        define("super_admin", false);

    if(!empty($user["permissions"]) || $user["id"] < 2):
        define("is_admin", true);
    else:
        define("is_admin", false);
    endif;

    foreach($user as $key => $value):
        define("logged_{$key}", $value);
    endforeach;

    define("logged_avatar", file_exists("uploads/avatars/" . $user["hash"] . ".jpg") ? site_url . "/uploads/avatars/" . $user["hash"] . ".jpg?v=" . filemtime("uploads/avatars/" . $user["hash"] . ".jpg") : site_url . "/uploads/avatars/noavatar.png");

    foreach($permissions as $permission):
        if(in_array($permission, $userPermissions)):
            $permissionArray["perm_{$permission}"] = true;
        endif;
    endforeach;

    define("permissions", $permissionArray);
}

function set_system($system)
{
    foreach($system as $key => $value):
        define("system_{$key}", $value);
    endforeach;

    define("isMobile", (new Mobile_Detect)->isMobile());
    define("isTablet", (new Mobile_Detect)->isTablet());
}

function set_plugins($plugins)
{
    foreach($plugins as $plugin):
        define("plugin_{$plugin["name"]}", json_decode($plugin["data"], true)["data"]);
    endforeach;

    $scripts = [];
    $styles = [];

    foreach(glob("templates/_plugins/*.js") as $script):
        $scripts[] = "\"" . site_url . "/templates/_plugins/" . str_replace("templates/_plugins/", "", $script) . "\"";
    endforeach;

    foreach(glob("templates/_plugins/*.css") as $style):
        $styles[] = "\"" . site_url . "/templates/_plugins/" . str_replace("templates/_plugins/", "", $style) . "\"";
    endforeach;

    define("plugin_scripts", implode(",", $scripts));
    define("plugin_styles", implode(",", $styles));
}

function set_subscription($check, $package, $default)
{
    $subscription = [];

    if($check > 0):
        $subscription = $package ? $package : $default;
    else:
        if(system_freemodel < 2):
            $subscription = $default;
        endif;    
    endif;

    return $subscription;
}

function __($lang)
{   
    return defined("language") ? (isset(language[$lang]) ? language[$lang] : "lang_undefined") : "lang_undefined";
}

function ___($string, $array = [])
{
    foreach($array as $key => $value):
        $string = str_replace("_888{$key}_", $value, $string);
    endforeach;

    return $string;
}

function _block($id)
{
    return defined("block_{$id}") ? constant("block_{$id}") : false;
}

function _assets($path, $force = false)
{
    return (Stringy\create($path)->contains(".js") || Stringy\create($path)->contains(".css") ? site_url . "/templates/_assets/{$path}?v=" . ($force ? md5(filemtime("templates/_assets/{$path}")) : md5(version)) : site_url . "/templates/_assets/{$path}");
}

function assets($path, $template = template)
{
    return (Stringy\create($path)->contains(".js") || Stringy\create($path)->contains(".css") ? site_url . "/templates/{$template}/assets/{$path}?v=" . md5(filemtime("templates/{$template}/assets/{$path}")) : site_url . "/templates/{$template}/assets/{$path}");
}

function response($status = 200, $msg = false, $data = false)
{
    header("Content-Type: application/json; charset=utf-8");

    return die(json_encode([
        "status" => (int) $status, 
        "message" => $msg, 
        "data" => $data
    ]));
}

function responseTable($data)
{
    return die(json_encode($data));
}

function get_image($type)
{
    switch($type):
        case "bg":
            return file_exists("uploads/theme/bg.png") ? site_url . "/uploads/theme/bg.png" : site_url . "/uploads/theme/default-bg.jpg";

            break;
        case "logo_light":
            return file_exists("uploads/theme/logo-light.png") ? site_url . "/uploads/theme/logo-light.png" : site_url . "/uploads/theme/default-logo-light.png";

            break;
        case "logo_dark":
            return file_exists("uploads/theme/logo-dark.png") ? site_url . "/uploads/theme/logo-dark.png" : site_url . "/uploads/theme/default-logo-dark.png";

            break;
        case "favicon":
            return file_exists("uploads/theme/favicon.png") ? site_url . "/uploads/theme/favicon.png" : site_url . "/uploads/theme/default-favicon.png";

            break;
        default:
            return false;
    endswitch;
}

function mail_title($title)
{
    return stripslashes(html_entity_decode($title, ENT_QUOTES, "UTF-8"));
}

function permission($permission)
{
    if(logged_id < 2):
        return true;
    else:
        if(isset(permissions["perm_{$permission}"]))
            return true;
        else
            return false;
    endif;
}

function limitation($limit, $used)
{
    if($limit < 1):
        return false;
    else:
        return $used >= $limit ? true : false;
    endif;
}

function footermark($state, $message, $mark)
{   
    if($state < 2 && !empty($mark)):
        return "{$message}\n\n{$mark}";
    else:
        return $message;
    endif;
}

function find($find, $string)
{
    return Stringy\create($string)->contains($find);
}

function rmrf($dir) 
{
    foreach (glob($dir) as $file):
        if(is_dir($file)):
            rmrf("$file/*");
            @rmdir($file);
        else:
            unlink($file);
        endif;
    endforeach;
}

function copyDirectory($src, $destination)
{  
    $dir = opendir($src);  

    @mkdir($destination);  
  
    while($file = readdir($dir)):
        if (($file != '.') && ($file != '..')):  
            if (is_dir("{$src}/{$file}")):
                @copyDirectory("{$src}/{$file}", "{$destination}/{$file}");  
            else: 
                @copy("{$src}/{$file}", "{$destination}/{$file}");  
            endif;
        endif;
    endwhile;
  
    closedir($dir); 
}  

function maskEmail($email)
{
    $mail_parts = explode("@", $email);
    $length = strlen($mail_parts[0]);
    $show = floor($length / 2);
    $hide = $length - $show;
    $replace = str_repeat("*", $hide);

    return substr_replace($mail_parts[0] , $replace , $show, $hide) . "@" . substr_replace($mail_parts[1], "***", 1, 3);
}

function formatMexicoNumWa($number)
{
    return (string) Stringy\create($number)->insert(1, 3);
}

function encodeBraces($string)
{
    $string = str_replace("{", "<++", $string);
    $string = str_replace("}", "++>", $string);
    $string = str_replace("|", "<+>", $string);

    return $string;
}

function decodeBraces($string)
{
    $string = str_replace("<++", "{", $string);
    $string = str_replace("++>", "}", $string);
    $string = str_replace("<+>", "|", $string);

    return $string;
}

function countDays($date1, $date2) {
    $date1 = date_create($date1);
    $date2 = date_create($date2);
    $interval = date_diff($date1, $date2);
    $days = $interval->days;

    return $days;
}