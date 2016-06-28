#!/usr/bin/env php
<?php

require 'includes/defaults.inc.php';
require 'config.php';
require 'includes/definitions.inc.php';
require 'includes/functions.php';
require 'includes/polling/functions.inc.php';
require 'includes/alerts.inc.php';

$global_modules = [];
$attribs = get_dev_attribs($argv[1]);
$poller_modules = $config['poller_modules'];
$local_modules = [];

//print_r($attribs);
//echo("-----\r\n");

$global_modules = array_filter($poller_modules);

foreach($global_modules as $module_name => $enabled) {
  if (isset($attribs["poll_".$module_name])) {
    if ($attribs["poll_".$module_name] == 1) {
      array_push($local_modules, $module_name);
    }
  } else {
    array_push($local_modules, $module_name);
  }
}

echo(implode(",", $local_modules));


