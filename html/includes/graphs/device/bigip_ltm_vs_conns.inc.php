<?php
/*
 * LibreNMS module to display F5 LTM Virtual Server Details
 *
 * Copyright (c) 2016 Aaron Daniels <aaron@daniels.id.au>
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

$component = new LibreNMS\Component();
$options = array();
$options['filter']['type'] = array('=','f5-ltm-vs');
$components = $component->getComponents($device['device_id'], $options);

// We only care about our device id.
$components = $components[$device['device_id']];

// Is the ID we are looking for a valid LTM VS
if (isset($components[$vars['id']])) {
    $label = $components[$vars['id']]['label'];
    $hash = $components[$vars['id']]['hash'];

    $rrd_filename = rrd_name($device['hostname'], array('f5-ltm-vs', $label, $hash));
    if (rrdtool_check_rrd_exists($rrd_filename)) {
        require 'includes/graphs/common.inc.php';
        $ds = 'totconns';

        $colour_area = '9999cc';
        $colour_line = '0000cc';

        $colour_area_max = '9999cc';

        $graph_max = 1;
        $graph_min = 0;

        $unit_text = 'Total Connections';
        $line_text = 'Connections';
        require 'includes/graphs/generic_simplex.inc.php';
    }
}
