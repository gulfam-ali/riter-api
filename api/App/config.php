<?php

global $config;
$config = array(
    "db" => array(
        "dbRead" => array(
            "dbname" => "riter",
            "username" => "root",
            "password" => "",
            "host" => "localhost"
        ),
        "dbWrite" => array(
            "dbname" => "riter",
            "username" => "root",
            "password" => "",
            "host" => "localhost"
        )
    ),
    "urls" => array(
        "baseUrl" => "localhost/riter/api"
    ),
	"salt" => "lkshs872njljdkfk9832u4vu3jslfjks",
	"dbprefix" => "pr_",
	"offset" => 0,
	"items" => 5
);

/*
    I will usually place the following in a bootstrap file or some type of environment
    setup file (code that is run at the start of every page request), but they work
    just as well in your config file if it's in php (some alternatives to php are xml or ini files).
*/

/*
    Creating constants for heavily used paths makes things a lot easier.
    ex. require_once(LIBRARY_PATH . "Paginator.php")
*/
defined("SITE_URL")
    or define("SITE_URL", 'localhost/riter/api');
defined("LAYOUT_URL")
    or define("LAYOUT_URL", SITE_URL . '/resources/layouts');

defined("CONTROLLER_PATH")
    or define("CONTROLLER_PATH", realpath(dirname(__FILE__) . '/controllers'));

defined("CLASS_PATH")
    or define("CLASS_PATH", realpath(dirname(__FILE__) . '/classes'));
defined("ADMIN_CLASS_PATH")
	or define("ADMIN_CLASS_PATH", CLASS_PATH . '/admin');

defined("INCLUDE_PATH")
    or define("INCLUDE_PATH", CLASS_PATH . '/includes');

defined("LIBRARY_PATH")
    or define("LIBRARY_PATH", realpath(dirname(__FILE__) . '/library'));

defined("API_URL")
    or define("API_URL", SITE_URL.'/resources/api');

defined("API_PATH")
    or define("API_PATH", dirname(__FILE__) . '/api');

defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/layouts'));


/*
    Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL | E_STRICT);

?>
