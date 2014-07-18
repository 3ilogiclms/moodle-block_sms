<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../config.php');
require_once('sms_form.php');
require_once("lib.php");

$c_id = required_param('c_id', PARAM_INT);
$group = optional_param('group', 0, PARAM_INT);

if ($c_id && $group) {
    if ($group == '0') {
        $attributes = array('1' => 'No Group');
    } else if ($group == '2') {
        $attributes = get_groups($c_id);
    }
} else if ($c_id) {
    if (!isGroup_null($c_id)) {
        $attributes = array('1' => 'No Group');
    } else
        $attributes = array('1' => 'No Group', '2' => 'Group');
}

$data = "";
foreach ($attributes as $key => $attrib) {
    $data .= $key . '~' . $attrib . '^';
}
return print_r($data);
