<?php

require_once "./config.php";
require_once "./App/Loader.php";
spl_autoload_register(array('App\Loader','autoload'));



$app = new \App\App();
$app->run();
