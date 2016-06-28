<?php
/* Copyright (C) 2015 Daniel Preussker, QuxLabs UG <preussker@quxlabs.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. */

/**
 * Generic IFrame Widget
 * @author Daniel Preussker
 * @copyright 2015 Daniel Preussker, QuxLabs UG
 * @license GPL
 * @package LibreNMS
 * @subpackage Widgets
 */

if( defined('show_settings') || empty($widget_settings) ) {
    $common_output[] = '
<form class="form" onsubmit="widget_settings(this); return false;">
  <div class="form-group input_'.$unique_id.'" id="input_'.$unique_id.'">
    <div class="col-sm-2">
      <label for="iframe_title" class="control-label">Title: </label>
    </div>
    <div class="col-sm-10">
      <input type="text" class="form-control input_'.$unique_id.'" name="iframe_title" placeholder="Title" value="'.htmlspecialchars($widget_settings['iframe_title']).'">
    </div>
  </div>
  <div class="form-group input_'.$unique_id.'" id="input_'.$unique_id.'">
    <div class="col-sm-2">
      <label for="iframe_url" class="control-label">IFrame URL: </label>
    </div>
    <div class="col-sm-10">
      <input type="text" class="form-control input_'.$unique_id.'" name="iframe_url" placeholder="IFrame URL" value="'.htmlspecialchars($widget_settings['iframe_url']).'">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-2">
      <button type="submit" class="btn btn-default">Set</button>
    </div>
  </div>
</form>';
}
else {
    $widget_settings['title'] = $widget_settings['iframe_title'];
    $common_output[]          = '<iframe style="max-width: '.$widget_dimensions['x'].'px; max-height:'.$widget_dimensions['y'].'px;" src="'.$widget_settings['iframe_url'].'"/>';
}
