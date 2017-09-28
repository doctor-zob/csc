<?php

define('DB_SERVER', getenv("MYSQL_SERVICE_HOST").":".getenv("MYSQL_SERVICE_PORT"));
define('DB_USERNAME', getenv("MYSQL_DB_USERNAME"));
define('DB_PASSWORD', getenv("MYSQL_DB_PASSWORD"));
define('DB_DATABASE', 'club');
define('ENABLE_NEWSFLOW_OVERLAY', false);
