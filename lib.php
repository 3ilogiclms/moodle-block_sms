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
function yutobo_path($api_key, $from, $to, $text) {

$url = "https://services.yuboto.com/web2sms/api/v2/smsc.aspx?api_key=".$api_key."&action=send&from=".$from."&to=".$to."&text=".$text;
//redirect($url);

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	// support https url
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
	$output = curl_exec($ch);
	return $output;
	curl_close($ch);
}
 
 
//function send_sms($to, $msg) {
//    global $CFG;
//    /* THE SMS API WORK BEGINS HERE */
//    require_once('sms_api/sms.php');
//
//    $apikey=$CFG->block_sms_apikey;         // API Key.
//
//    $sms = new sendsmsdotpk($apikey);	    // Making a new sendsms dot pk object.
//    
//    // isValid.
//    if ($sms->isValid()) {
//        $status = get_string('valid_key', 'block_sms');
//    } else {
//        $status = "KEY: " . $apikey . " IS NOT VALID";
//    }
//    $msg = stripslashes($msg);
//    // SEND SMS.
//    if ($sms->sendsms($to, $msg, 0)) {
//        $status = get_string('sent', 'block_sms');
//    } else {
//        $status = get_string('error', 'block_sms');
//    }
//    return $status;
//}

function bulk_sms($to, $message) {
    global $CFG;
    /* User Numbers */
    $numbers = '';
//    foreach ($to as $num) {
//        if ($numbers == '') {
//            $numbers = $num;
//        } else {
//            $numbers .= ',' . $num;
//        }
//    }
	$numbers = $to;
    // Usernames.
    $username = $CFG->block_sms_api_username;
    // Password
    $password = $CFG->block_sms_api_password;
    // SMS API.
    //$api_id = $CFG->block_sms_apikey;
    // Message
    $message = str_replace("'", "", $message);
    $message = urlencode($message);
    $sender = "3i Logic";

    // Send Sms.
    $url = "http://sendpk.com/api/sms.php?username=" . $username . "&password=" . $password . "&mobile=" . $numbers . "&message=" . $message . "&sender=" . $sender;
    //$url = urlencode($url);
    //echo $url;
    $ch = curl_init();
    $timeout = 30;
    // Set url and other options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    // Get the page contents
    $output = curl_exec($ch);

    $count = substr_count($output, 'ID');
//    if ($count >= 1) {
//        echo "<div id='load-users' style='border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #00529B;background-image: url(" . 'pic/success.png' . "); background-color: #BDE5F8;border-color: #3b8eb5;'>$count Message(s) sent successfully. Detail Report of SMS can be viewed at http://bulksms.com.pk/ account.</div>";
//    } else
//        echo "<div id='load-users' style='border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #00529B;background-image: url(" . 'pic/error.png' . "); background-color: #BDE5F8;border-color: #3b8eb5;'>Error sending SMS.</div>";
//    // close curl resource to free up system resources
    curl_close($ch);
    //redirect($url);
   return $output;
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