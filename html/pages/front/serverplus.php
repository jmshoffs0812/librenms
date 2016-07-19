<?php

/*
 * LibreNMS
 *
 * Copyright (c) 2014 Neil Lathwood <https://github.com/laf/ http://www.lathwood.co.uk/fa>
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

/*
 * Code for Gridster.sort_by_row_and_col_asc(serialization) call is from http://gridster.net/demos/grid-from-serialize.html
 */

$no_refresh   = true;
$default_dash = 0;
if (($tmp = dbFetchCell('SELECT dashboard FROM users WHERE user_id=?',array($_SESSION['user_id']))) != 0) {
    $default_dash = $tmp;
} else if (dbFetchCell('SELECT dashboard_id FROM dashboards WHERE user_id=?',array($_SESSION['user_id'])) == 0) {
    $tmp = dbInsert(array('dashboard_name'=>'Default','user_id'=>$_SESSION['user_id']),'dashboards');
    $vars['dashboard'] = dbInsert(array('dashboard_name'=>'Default','user_id'=>$_SESSION['user_id']),'dashboards');
    if (dbFetchCell('select 1 from users_widgets where user_id = ? && dashboard_id = ?',array($_SESSION['user_id'],0)) == 1) {
        dbUpdate(array('dashboard_id'=>$vars['dashboard']),'users_widgets','user_id = ? && dashboard_id = ?',array($_SESSION['user_id'],0));
    }
}
if (!empty($vars['dashboard'])) {
    $orig = $vars['dashboard'];
    $vars['dashboard'] = dbFetchRow('select * from dashboards where user_id = ? && dashboard_id = ? order by dashboard_id limit 1',array($_SESSION['user_id'],$vars['dashboard']));
    if (empty($vars['dashboard'])) {
        $vars['dashboard'] = dbFetchRow('select dashboards.*,users.username from dashboards inner join users on dashboards.user_id = users.user_id where dashboards.dashboard_id = ? && dashboards.access > 0',array($orig));
    }
}
if (empty($vars['dashboard'])) {
    if ($default_dash != 0) {
        $vars['dashboard'] = dbFetchRow('select dashboards.*,users.username from dashboards inner join users on dashboards.user_id = users.user_id where dashboards.dashboard_id = ?',array($default_dash));
    }
    else {
        $vars['dashboard'] = dbFetchRow('select * from dashboards where user_id = ? order by dashboard_id limit 1',array($_SESSION['user_id']));
    }
    if (isset($orig)) {
        $msg_box[] = array('type' => 'error', 'message' => 'Dashboard <code>#'.$orig.'</code> does not exist! Loaded <code>'.$vars['dashboard']['dashboard_name'].'</code> instead.','title' => 'Requested Dashboard Not Found!');
    }
}

?>
<div class="dash-collapse" id="del_dash">
  <div class="row" style="margin-top:5px;">
    <div class="col-md-6">
      <div class="col-md-6">
      </div>
    </div>
  </div>
  <hr>
</div>
<script src='https://www.google.com/jsapi'></script>
<script src="js/jquery.gridster.min.js"></script>

<span class="message" id="message"></span>

        <div class="gridster grid">
            <ul>
            </ul>
        </div>

<script type="text/javascript">

    var gridster;

    var serialization = [
  { "user_widget_id":"19", "widget_id":"3", "title":"Alerts", "widget":"alerts", "col":"1", "row":"1", "size_x":"20", "size_y":"4", "refresh":"60" }
];

    serialization = Gridster.sort_by_row_and_col_asc(serialization);

    function updatePos(gridster) {
        var s = JSON.stringify(gridster.serialize());
        $.ajax({
            type: 'POST',
            url: 'ajax_form.php',
            data: {type: "update-dashboard-config", data: s, dashboard_id: <?php echo $vars['dashboard']['dashboard_id']; ?>},
            dataType: "json",
            success: function (data) {
                if (data.status == 'ok') {
                }
                else {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                }
            },
            error: function () {
                $("#message").html('<div class="alert alert-info">An error occurred.</div>');
            }
        });
    }

    var gridster_state = 0;

    $(function(){
        $('[data-toggle="tooltip"]').tooltip();
        dashboard_collapse();
        gridster = $(".gridster ul").gridster({
            widget_base_dimensions: ['auto', 100],
            autogenerate_stylesheet: true,
            widget_margins: [5, 5],
            avoid_overlapped_widgets: true,
            min_cols: 1,
            max_cols: 20,
            draggable: {
                handle: 'header, span',
                stop: function(e, ui, $widget) {
                    updatePos(gridster);
                },
            },
            resize: {
                enabled: true,
                stop: function(e, ui, widget) {
                    updatePos(gridster);
                    widget_reload(widget.attr('id'),widget.data('type'));
                }
            },
            serialize_params: function(w, wgd) {
                return {
                    id: $(w).attr('id'),
                    col: wgd.col,
                    row: wgd.row,
                    size_x: wgd.size_x,
                    size_y: wgd.size_y
                };
            }
        }).data('gridster');
        $('.gridster  ul').css({'width': $(window).width()});

        gridster.remove_all_widgets();
        gridster.disable();
        gridster.disable_resize();
        $.each(serialization, function() {
            widget_dom(this);
        });
        $(document).on('click','.edit-dash-btn', function() {
            if (gridster_state == 0) {
                gridster.enable();
                gridster.enable_resize();
                gridster_state = 1;
                $('.fade-edit').fadeIn();
            }
            else {
                gridster.disable();
                gridster.disable_resize();
                gridster_state = 0;
                $('.fade-edit').fadeOut();
            }
        });

        $(document).on('click','#clear_widgets', function() {
            var widget_id = $(this).data('widget-id');
            $.ajax({
                type: 'POST',
                url: 'ajax_form.php',
                data: {type: "update-dashboard-config", sub_type: 'remove-all', dashboard_id: <?php echo $vars['dashboard']['dashboard_id']; ?>},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'ok') {
                        gridster.remove_all_widgets();
                    }
                    else {
                        $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    }
                },
                error: function () {
                    $("#message").html('<div class="alert alert-info">An error occurred.</div>');
                }
            });
        });

        $('.place_widget').on('click',  function(event, state) {
            var widget_id = $(this).data('widget_id');
            $.ajax({
                type: 'POST',
                url: 'ajax_form.php',
                data: {type: "update-dashboard-config", sub_type: 'add', widget_id: widget_id, dashboard_id: <?php echo $vars['dashboard']['dashboard_id']; ?>},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'ok') {
                        widget_dom(data.extra);
                        updatePos(gridster);
                    }
                    else {
                        $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    }
                },
                error: function () {
                    $("#message").html('<div class="alert alert-info">An error occurred.</div>');
                }
            });
        });

        $(document).on( "click", ".close-widget", function() {
            var widget_id = $(this).data('widget-id');
            $.ajax({
                type: 'POST',
                url: 'ajax_form.php',
                data: {type: "update-dashboard-config", sub_type: 'remove', widget_id: widget_id, dashboard_id: <?php echo $vars['dashboard']['dashboard_id']; ?>},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'ok') {
                        gridster.remove_widget($('#'+widget_id));
                        updatePos(gridster);
                    }
                    else {
                        $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    }
                },
                error: function () {
                    $("#message").html('<div class="alert alert-info">An error occurred.</div>');
                }
            });
        });

        $(document).on("click",".edit-widget",function() {
            obj = $(this).parent().parent().parent();
            if( obj.data('settings') == 1 ) {
                obj.data('settings','0');
            } else {
                obj.data('settings','1');
            }
            widget_reload(obj.attr('id'),obj.data('type'));
        });

   });

    function dashboard_collapse(target) {
        if (target !== undefined) {
            $('.dash-collapse:not('+target+')').each(function() {
                $(this).fadeOut(0);
            });
            $(target).fadeToggle(300);
            if (target != "#edit_dash") {
                gridster.disable();
                gridster.disable_resize();
                gridster_state = 0;
                $('.fade-edit').fadeOut();
            }
        } else {
            $('.dash-collapse').fadeOut(0);
        }
    }

    function dashboard_delete(data) {
        $.ajax({
            type: 'POST',
            url: 'ajax_form.php',
            data: {type: 'delete-dashboard', dashboard_id: $(data).data('dashboard')},
            dataType: "json",
            success: function (data) {
                if( data.status == "ok" ) {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    window.location.href="<?php echo rtrim($config['base_url'],'/'); ?>/overview";
                }
                else {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                }
            }
        });
    }

    function dashboard_edit(data) {
        datas = $(data).serializeArray();
        data = [];
        for( var field in datas ) {
            data[datas[field].name] = datas[field].value;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax_form.php',
            data: {type: 'edit-dashboard', dashboard_name: data['dashboard_name'], dashboard_id: <?php echo $vars['dashboard']['dashboard_id']; ?>, access: data['access']},
            dataType: "json",
            success: function (data) {
                if( data.status == "ok" ) {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    window.location.href="<?php echo rtrim($config['base_url'],'/'); ?>/overview/dashboard=<?php echo $vars['dashboard']['dashboard_id']; ?>";
                }
                else {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                }
            }
        });
    }

    function dashboard_add(data) {
        datas = $(data).serializeArray();
        data = [];
        for( var field in datas ) {
            data[datas[field].name] = datas[field].value;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax_form.php',
            data: {type: 'add-dashboard', dashboard_name: data['dashboard_name']},
            dataType: "json",
            success: function (data) {
                if( data.status == "ok" ) {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    window.location.href="<?php echo rtrim($config['base_url'],'/'); ?>/overview/dashboard="+data.dashboard_id;
                }
                else {
                    $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                }
            }
        });
    }

    function widget_dom(data) {
        dom = '<li id="'+data.user_widget_id+'" data-type="'+data.widget+'" data-settings="0">'+
              '<header class="widget_header"><span id="widget_title_'+data.user_widget_id+'">'+data.title+
              '</span>'+
              '<span class="fade-edit pull-right">'+
              '<i class="fa fa-pencil-square-o edit-widget" data-widget-id="'+data.user_widget_id+'" aria-label="Settings" data-toggle="tooltip" data-placement="top" title="Settings">&nbsp;</i>&nbsp;'+
              '<i class="text-danger fa fa-times close-widget" data-widget-id="'+data.user_widget_id+'" aria-label="Close" data-toggle="tooltip" data-placement="top" title="Remove">&nbsp;</i>&nbsp;'+
              '</span>'+
              '</header>'+
              '<div class="widget_body" id="widget_body_'+data.user_widget_id+'">'+data.widget+'</div>'+
              '\<script\>var timeout'+data.user_widget_id+' = grab_data('+data.user_widget_id+','+data.refresh+',\''+data.widget+'\');\<\/script\>'+
              '</li>';
        if (data.hasOwnProperty('col') && data.hasOwnProperty('row')) {
            gridster.add_widget(dom, parseInt(data.size_x), parseInt(data.size_y), parseInt(data.col), parseInt(data.row));
        } else {
            gridster.add_widget(dom, parseInt(data.size_x), parseInt(data.size_y));
        }
        if (gridster_state == 0) {
            $('.fade-edit').fadeOut(0);
        }
        $('[data-toggle="tooltip"]').tooltip();
    }

    function widget_settings(data) {
        var widget_settings = {};
        var widget_id = 0;
        datas = $(data).serializeArray();
        for( var field in datas ) {
            widget_settings[datas[field].name] = datas[field].value;
        }
        $('.gridster').find('div[id^=widget_body_]').each(function() {
            if(this.contains(data)) {
                widget_id = $(this).parent().attr('id');
                widget_type = $(this).parent().data('type');
                $(this).parent().data('settings','0');
            }
        });
        if( widget_id > 0 && widget_settings != {} ) {
            $.ajax({
                type: 'POST',
                url: 'ajax_form.php',
                data: {type: 'widget-settings', id: widget_id, settings: widget_settings},
                dataType: "json",
                success: function (data) {
                    if( data.status == "ok" ) {
                        widget_reload(widget_id,widget_type);
                    }
                    else {
                        $("#message").html('<div class="alert alert-info">' + data.message + '</div>');
                    }
                }
            });
        }
	return false;
    }

    function widget_reload(id,data_type) {
        if( $("#widget_body_"+id).parent().data('settings') == 1 ) {
            settings = 1;
        } else {
            settings = 0;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax_dash.php',
            data: {type: data_type, id: id, dimensions: {x:$("#widget_body_"+id).innerWidth()-50, y:$("#widget_body_"+id).innerHeight()-50}, settings:settings},
            dataType: "json",
            success: function (data) {
                if (data.status == 'ok') {
                    $("#widget_title_"+id).html(data.title);
                    $("#widget_body_"+id).html(data.html);
                }
                else {
                    $("#widget_body_"+id).html('<div class="alert alert-info">' + data.message + '</div>');
                }
            },
            error: function () {
                $("#widget_body_"+id).html('<div class="alert alert-info">Problem with backend</div>');
            }
        });
    }

    function grab_data(id,refresh,data_type) {
        if( $("#widget_body_"+id).parent().data('settings') == 0 ) {
            widget_reload(id,data_type);
        }
        new_refresh = refresh * 1000;
        setTimeout(function() {
            grab_data(id,refresh,data_type);
        },
        new_refresh);
    }
    $('#new-widget').popover();
</script>
