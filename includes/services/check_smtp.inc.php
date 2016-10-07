<?php

#$check_cmd = $config['nagios_plugins'] . "/check_smtp -H ".$service['hostname'];
$check_cmd = $config['nagios_plugins'] . "/check_smtp " .$service['service_param'];
