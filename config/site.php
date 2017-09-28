<?php

define('DB_SERVER', getenv("MYSQL_SERVICE_HOST").":".getenv("MYSQL_SERVICE_PORT"));
define('DB_USERNAME', '$MYSQL_DB_USERNAME');
define('DB_PASSWORD', '$MYSQL_DB_PASSWORD');
define('DB_DATABASE', 'club');
define('ENABLE_NEWSFLOW_OVERLAY', false);
