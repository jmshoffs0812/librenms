<?php

#$check_cmd = $config['nagios_plugins'] . "/check_imap -H ".$service['hostname'];
$check_cmd = $config['nagios_plugins'] . "/check_imap " .$service['service_param'];
