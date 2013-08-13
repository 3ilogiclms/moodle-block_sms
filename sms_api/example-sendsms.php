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

 ?>
<html>
<head>
<title>SEND URDU SMS API</title>
<script LANGUAGE="JavaScript">
function textCounter(field, countfield, maxlimit) {
if (field.value.length > maxlimit)
field.value = field.value.substring(0, maxlimit);
else
countfield.value = maxlimit - field.value.length;
}
function showmessage(field)
{
    if (!mess) mess = document.getElementById('mess');
    if (lengths[field.name] > field.value.length) {
	mess.innerHTML = '';
	for (var i=0; i<field.form.elements.length; i++) {
		e = field.form.elements[i];
		if (e.name != field.name && (e.type=='text' || e.type=='textarea') && e.onkeyup) {
			e.onkeyup();
		}
	}
    }
    lengths[field.name] = field.value.length;
}
</script>
</head>
<body>

<?php
if (isset($_POST['a'])) {
    $to = $_POST['a'];
    $msg = $_POST['b'];
    /* THE SMS API WORK BEGINS HERE */
    include "sms.php";

    $apikey = "9af16fa9db76f0a56d1a";	// Your API KEY
    $sms = new sendsmsdotpk($apikey);	// Making a new sendsms dot pk object

    // TESTING isValid
    if ($sms->isValid()) {
        echo "Your key IS VALID";
    }
    else {
        echo "KEY: " . $apikey . " IS NOT VALID";
    }

    // TESTING SENDSMS
    if ($sms->sendsms($to, $msg, 0)) {
        echo "<br /> Your sms is sent to $to!";
    }
    else {
            echo "error ouccured!";
    }
    /* THE SMS API WORK ENDS HERE */
}
?>

<form method ="post">
<table>
<tr>
<td>
To: <input type="text" name="a" maxlength="11">
</td>
</tr>
<tr>
<td>
<small>Example: 03001234567</small>
</tr>
</td>
<tr>
<td>
Message:
</td>
</tr>
<tr>
<td>
<textarea name="b" id="msg" rows="7" wrap="physical" cols="30" onKeyDown="textCounter(this.form.msg,this.form.textbox,270);" onKeyUp="textCounter(this.form.msg,this.form.textbox,270);showmessage(this);"></textarea> <br />
<input readonly type=text name=textbox size=3 maxlength=3 value="270" tabindex=270> characters left.<br />
<input type="submit" value="SEND SMS!">
</td>
</tr>
</table>
</form>
</body>
</html>