<?php
/**
 * Framework System Config
 * @package MVC Framework
 * @author Titan Systems <mail@titansystems.ph>
 */

/**
 * Project Configs
 * @warning Do not touch this part
 */

define("base_dir", "system/");
define("error_handler", 1);
define("site_url", "//" . env["siteurl"] . env["port"] . env["subdir"]);
define("system_token", env["systoken"]);

/**
 * Titan Systems API
 * 
 * @desc This endpoint is used for the app builder, it sends your build configurations to the build server when
 * you request for an app build. When your app is finally built, this endpoint is not used anymore unless you
 * build a new app again. We respect your data privacy.
 * 
 * @desc It is also used for the exchange rate API, the rates are from https://exchangeratesapi.io which is a
 * paid service. We have subscribed to enterprise plan to allow our customers to use the service for free.
 * 
 * @desc It is also used for GeoIP location, normally, a server cannot accurately identify which country 
 * the IP comes from, this is why we create a dedicated service for our customers. 
 * These locations are used to store correct countries in the visitor statistics.
 */

define("titansys_api", "https://api.titansystems.ph");

/**
 * Titan Systems CDN
 * 
 * @desc This endpoint is for the hosting server of demo assets, this is also where the built apks are stored
 * so your server can download them.
 */

define("titansys_cdn", "https://cdn.titansystems.ph");

/**
 * Titan Systems Echo
 * 
 * @desc This is the server for the socket commucation, this is primarily used for realtime dashboard
 * updates only such as notifications, device status and UI refresh.
 */

define("titansys_echo", "https://echo.titansystems.ph");