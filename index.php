<?php

$config = require_once('src/config/config.php');

require_once('src/classes/Pattern.php');

$objPattern = new Pattern($config);

echo $objPattern->getPage();
