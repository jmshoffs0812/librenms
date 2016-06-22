<?php

function resolvable($h) {
  return checkdnsrr($h, 'A') || checkdnsrr($h, 'AAAA') || checkdnsrr($h, 'CNAME');
}

if ($_POST['editing']) {
    if ($_SESSION['userlevel'] > '7') {
        $oxidized_hostname = $device['hostname'];
        $oxidized_devicetype = trim(mres($_POST['oxidized_devicetype']));
        $oxidized_username = trim(mres($_POST['oxidized_username']));
        $oxidized_sshport = trim(mres($_POST['oxidized_sshport']));
        $oxidized_telnetport = trim(mres($_POST['oxidized_telnetport']));
        $oxidized_password = trim(mres($_POST['oxidized_password']));

        if (ctype_space($oxidized_devicetype) || $oxidized_devicetype == '') {
            del_dev_attrib($device, 'oxidized_config');
        } else {
            if ($oxidized_hostname != '' && $oxidized_devicetype != '' && $oxidized_username != '' && $oxidized_password != '' && oxidized_sshport != '' && oxidized_telnetport != '') {
                set_dev_attrib($device, 'oxidized_config', $oxidized_hostname.":".$oxidized_devicetype.":".$oxidized_username.":".$oxidized_password.":".$oxidized_sshport.":".$oxidized_telnetport);
            }
        }
        $update_message = 'Credentials updated.';
        $updated        = 1;

        $routerdb = '/opt/librenms/.config/oxidized/router.db';
 
        copy($routerdb, $routerdb.".last");
        $fp = fopen($routerdb, 'w');
        $query = "SELECT DISTINCT `attrib_value` FROM `devices_attribs` WHERE `attrib_type` = 'oxidized_config' ORDER BY `device_id` ASC";
        foreach (dbFetch($query) as $row) {
          fwrite($fp, $row['attrib_value'].PHP_EOL);
        }
        fclose($fp);
     
        $oxidized_report = file_get_contents($config['oxidized']['url']."/reload");
        if ($oxidized_report == 'reloaded list of nodes') {
            $update_message .= " Oxidized configuration reloaded.";
        }
    }
    else {
        include 'includes/error-no-perm.inc.php';
    }//end if
}//end if

if ($updated && $update_message) {
    print_message($update_message);
}
else if ($update_message) {
    print_error($update_message);
}

?>


<?php

$oxidized_config = get_dev_attrib($device, 'oxidized_config');
list($o_hostname, $o_devicetype, $o_username, $o_password, $o_sshport, $o_telnetport) = explode(":", $oxidized_config);

$newcreds = false;

echo '<h3>Oxidized credentials</h3>';
if (ctype_space($o_hostname) || $o_hostname == '') {
    $newcreds = true;
    echo '<p><i>No existing configuration found. <br>Values are a mix of estimates and defaults and may be unsuitable for this device.</i></p><br>';
    $o_hostname = $device['hostname'];
}

$modelsfile = '/opt/librenms/.config/oxidized/models.lst';
$models = file($modelsfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$found_model = false;

if (ctype_space($o_devicetype) || $o_devicetype == '') {
  $found_model_key = array_search($device['os'], $models, true);
  if ($found_model_key) {
    $found_model = $models[$found_model_key];
  }
}

if (ctype_space($o_username) || $o_username == '') {
    $o_username = $config['oxidized']['default_user'];
}

if (ctype_space($o_password) || $o_password == '') {
    $o_password = $config['oxidized']['default_pass'];
}

if (ctype_space($o_sshport) || $o_sshport == '') {
    $o_sshport = $config['oxidized']['default_sshport'];
}

if (ctype_space($o_telnetport) || $o_telnetport == '') {
    $o_telnetport = $config['oxidized']['default_telnetport'];
}

?>

<script>
function clearAndSubmit() {
  document.getElementById("oxidized_devicetype").value = '';
  document.getElementById("oxidized_username").value = '';
  document.getElementById("oxidized_password").value = '';
  document.getElementById("oxidized_sshport").value = '';
  document.getElementById("oxidized_telnetport").value = '';
  document.getElementById("edit").submit();
}
</script>

<form id="edit" name="edit" method="post" action="" role="form" class="form-horizontal">
<input type="hidden" name="editing" value="yes">
  <div class="form-group">
    <label for="oxidized_devicetype" class="col-sm-2 control-label">Device Type</label>
    <div class="col-sm-6">
      <select id="oxidized_devicetype" name="oxidized_devicetype" class="form-control">
      <option value=''></option>
      <?php
      foreach($models as $model) {
        echo("<option value='" . $model . "'");
        if ($model == $o_devicetype || $model == $found_model) {
          echo(" selected ");
        }
        echo(">". $model . "</option>");
      }
      ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="oxidized_username" class="col-sm-2 control-label">Username</label>
    <div class="col-sm-6">
      <input id="oxidized_username" name="oxidized_username" class="form-control" value="<?php echo $o_username; ?>" />
    </div>
  </div>
  <div class="form-group">
    <label for="oxidized_password" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-6">
      <input id="oxidized_password" name="oxidized_password" type="password" class="form-control" value="<?php echo $o_password; ?>" />
    </div>
  </div>
  <div class="form-group">
    <label for="oxidized_sshport" class="col-sm-2 control-label">SSH Port</label>
    <div class="col-sm-6">
      <input id="oxidized_sshport" name="oxidized_sshport" class="form-control" value="<?php echo $o_sshport; ?>" />
    </div>
  </div>
  <div class="form-group">
    <label for="oxidized_telnetport" class="col-sm-2 control-label">Telnet Port</label>
    <div class="col-sm-6">
      <input id="oxidized_telnetport" name="oxidized_telnetport" class="form-control" value="<?php echo $o_telnetport; ?>" />
    </div>
  </div>
  <div class="row">
    <div class="col-md-1 col-md-offset-2">
        <button name="Delete" onclick="clearAndSubmit();" class="btn btn-default"><i class="fa fa-check"></i> Delete</button>
    </div>
    <div class="col-md-1 col-md-offset-2">
        <button type="submit" name="Submit"  class="btn btn-default"><i class="fa fa-check"></i> Save</button>
    </div>
  </div>
</form>
