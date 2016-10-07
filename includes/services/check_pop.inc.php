<?php

#$check_cmd = $config['nagios_plugins'] . "/check_pop -H ".$service['hostname'];
$check_cmd = $config['nagios_plugins'] . "/check_pop " .$service['service_param'];
