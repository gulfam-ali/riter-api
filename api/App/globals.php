<?php

//Notification EVENTS
defined("EV_LIKE") 		or define("EV_LIKE", 1);
defined("EV_COMMENT") 		or define("EV_COMMENT", 2);
defined("EV_FOLLOW") 		or define("EV_FOLLOW", 3);
defined("EV_BOOKMARK") 	or define("EV_BOOKMARK", 4);
defined("EV_SHARE") 		or define("EV_SHARE", 5);
defined("EV_DELETE") 		or define("EV_DELETE", 6);

//Notification Reference Types
defined("REF_STORY") 	or define("REF_STORY", 1);
defined("REF_COMMENT") 	or define("REF_COMMENT", 2);