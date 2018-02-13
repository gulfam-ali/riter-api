<?php

header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization,userId");

    require_once(realpath(dirname(__FILE__) . "/App/config.php"));
	require_once(realpath(dirname(__FILE__) . "/App/globals.php"));
    require_once(realpath(dirname(__FILE__) . "/App/api.php"));
?>
