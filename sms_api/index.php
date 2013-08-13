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

/* Course Status Block
 * The plugin shows the number and list of enrolled courses and completed courses.
 * It also shows the number of courses which are in progress and whose completion criteria is undefined but the manger.
 * @package blocks
 * @author: Azmat Ullah, Talha Noor
 * @date: 2013
 */

include_once "sms.php";

/*
	Please login to your account in SendSMS.pk and get your API KEY
	by navigating to this URL: http://www.sendsms.pk/api-settings.php
	Enter then API KEY given there in the following variable ($apikey)
*/

$apikey = "9af16fa9db76f0a56d1a";	// Your API KEY
$sms = new sendsmsdotpk($apikey);	// Making a new sendsms dot pk object
