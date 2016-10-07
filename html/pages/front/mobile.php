<?php
/*
$sql = "SELECT * FROM `devices` WHERE `status` = '0' AND `ignore` = '0' ORDER BY `devices`.`location` ASC, `devices`.`hostname` ASC";
$location=NULL;
$first=true;
foreach (dbFetchRows($sql) as $device) {
    $thislocation=$device['location'];
    if (!$first and $location!=$thislocation) {
        echo '<hr>';
    }
    $first=false;
    if ($location != $thislocation) {
        echo '<h4><strong>'.$device['location'].'</strong></h4>';
    }
    echo '<h5>'.generate_device_link($device, shorthost($device['hostname'])).'</h5>';
    $location = $thislocation;
}
*/

$device['device_id'] = '-1';
require_once 'includes/common/min-alerts.inc.php';
echo implode('',$common_output);
unset($device['device_id']);


