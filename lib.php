<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/* SMS Notifier Block
 * SMS notifier is a one way SMS messaging block that allows managers, teachers and administrators to
 * send text messages to their student and teacher.
 * @package blocks
 * @author: Azmat Ullah, Talha Noor
 * @date: 06-Jun-2013
*/

require_once(dirname(__FILE__).'/../../config.php');
// Send SMS pk Api Function
/**
 * This function will send the SMS using sendsms.pk.API is only for Pakistan's users.
 *
 * @param int   $to  User id
 * @param string $msg  Message Text
 * @return String $status return will shows the status of message.
 */
function send_sms($to, $msg) {
    global $CFG;
    /* THE SMS API WORK BEGINS HERE */
    require_once('sms_api/sms.php');

    $apikey=$CFG->block_sms_apikey;         // API Key.

    $sms = new sendsmsdotpk($apikey);	    // Making a new sendsms dot pk object.
    
    // isValid.
    if ($sms->isValid()) {
        $status = get_string('valid_key', 'block_sms');
    } else {
        $status = "KEY: " . $apikey . " IS NOT VALID";
    }
    $msg = stripslashes($msg);
    // SEND SMS.
    if ($sms->sendsms($to, $msg, 0)) {
        $status = get_string('sent', 'block_sms');
    } else {
        $status = get_string('error', 'block_sms');
    }
    return $status;
}

/**
 * This function will send the SMS using Clickatells API, by this API Users can send international messages.
 *
 * @param int   $to  User id
 * @param string $msg  Message Text
 * @return Call back URL through clickatell
 */
function send_sms_clickatell($to, $message) {
    global $CFG;
    /*User Numbers*/
    $numbers = '';
    foreach($to as $num){
        if($numbers == '') {
            $numbers =  $num;
        }
        else {
            $numbers .=  ','.$num;
        }
    }

    // Usernames.
    $username = $CFG->block_sms_api_username;
    // Password
    $password = $CFG->block_sms_api_password;
    // SMS API.
    $api_id = $CFG->block_sms_apikey;
    // Send Sms.
    $url = "http://api.clickatell.com/http/sendmsg?user=".$username."&password=".$password."&api_id=".$api_id."&to=".$numbers."&text=".$message;
    redirect($url);
}

function block_sms_print_page($sms) {
    global $OUTPUT, $COURSE;
    $display = $OUTPUT->heading($sms->pagetitle);
    $display .= $OUTPUT->box_start();
    if($sms->displaydate) {
        $display .= userdate($sms->displaydate);
    }
    if($return) {
        return $display;
    } else {
        echo $display;
    }
}

/**
 * This function will return the message template.
 *
 * @param int   $to  Message id
 * @return string $result->msg return message template on the base of message id
 */
function get_msg($id) {
    global $DB;
    $result = $DB->get_record_sql('SELECT cju.j_id, cj.job FROM {competency_job} AS cj inner join {competency_job_user} AS cju ON cj.id = cju.j_id
                                  WHERE cju.u_id = ?', array($id));
    return $result->msg;
}