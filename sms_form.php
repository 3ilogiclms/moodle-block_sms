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

require_once("{$CFG->libdir}/formslib.php");
require_once("lib.php");
require_login();
$release = $CFG->release;
$release = explode(" (", $release);
if ($release[0] >= 2.2) {
    $PAGE->set_context(context_system::instance());
} else {
    $PAGE->set_context(get_system_context());
}
// Display List of Users.
class sms_form extends moodleform {
    public function definition() {
        return false;
    }
    public function display_report() {
        global $DB, $OUTPUT, $CFG, $USER;
        $table = new html_table();
        $table->head  = array(get_string('serial_no', 'block_sms'), get_string('name', 'block_sms'), get_string('cell_no', 'block_sms'), get_string('select', 'block_sms'));
        $table->size  = array('10%', '20%', '20%', '20%');
        $table->align  = array('center', 'left', 'center', 'center');
        $table->width = '100%';
        $table->data  = array();
        $sql="SELECT usr.firstname, usr.lastname, usr.email,usr.phone2,c.fullname
        FROM {course} c INNER JOIN {context} cx ON c.id = cx.instanceid
        AND cx.contextlevel = '50' and c.id=8
        INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
        INNER JOIN {role} r ON ra.roleid = r.id
        INNER JOIN {user} usr ON ra.userid = usr.id
        WHERE r.name = 'Student'";
        $rs = $DB->get_recordset_sql($sql, array(),  null, null);
        $i=0;
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$i;
            $row[] = $log->firstname;
            $row[] = $log->phone2;
            $row[] = "<input type='checkbox' class='usercheckbox' name='user[]' value='$log->phone2'/>";
            $table->data[] = $row;
        }
        return $table;
    }
}

// Display SMS Template.
class sms_send extends moodleform {
    public function definition() {
        global $DB, $CFG;
        $mform =& $this->_form;
        $mform->addElement('header', 'sms_send', get_string('sms_send', 'block_sms'));
        if(isset($c_id)) {
            $attributes =  $DB->get_records_sql_menu('SELECT id , fullname FROM {course} where id = ?', array ($c_id), $limitfrom=0, $limitnum=0);
        }
        else {
            $attributes =  $DB->get_records_sql_menu('SELECT id , fullname FROM {course}', array ($params=null), $limitfrom=0, $limitnum=0);
        }
        $mform->addElement('select', 'c_id', get_string('selectcourse', 'block_sms'), $attributes);
        $mform->setType('c_id', PARAM_INT);
	if(isset($c_id)) {
	    $attributes =  $DB->get_records_sql_menu('SELECT id,level_name FROM {competency_level} where id = ?',array ($l_id), $limitfrom=0, $limitnum=0);
        }
        else {
	    $attributes1=array('teacher', 'student');
        }
        $attributes2 =  $DB->get_records_sql_menu('SELECT id , shortname FROM {role}', null, $limitfrom=0, $limitnum=0);
        $attributes=array_intersect($attributes2, $attributes1);
        $mform->addElement('select', 'r_id', get_string('selectrole', 'block_sms'), $attributes);
        $attributes =  $DB->get_records_sql_menu('SELECT id,tname FROM {block_sms_template}', null, $limitfrom=0, $limitnum=0);
        $mform->addElement('selectwithlink', 'm_id', get_string('selectmsg', 'block_sms'), $attributes, null,
                           array('link' => $CFG->wwwroot.'/blocks/sms/view.php?viewpage=3', 'label' => get_string('template', 'block_sms')));
        $attributes = array('rows' => '6', 'cols' => '45', 'maxlength' => '160');
        $mform->setType('r_id', PARAM_INT);
        $mform->addElement('textarea', 'sms_body', get_string('sms_body', 'block_sms'), $attributes);
        $mform->addRule('sms_body','Please write Message' , 'required', 'client');
        $mform->addRule('sms_body', $errors = null, 'required', null, 'server');
        $mform->setType('sms_body', PARAM_TEXT);
        $mform->addElement('html', '<img src="Loading.gif" id="load" style="margin-left:6cm;" />');
        $mform->addElement('hidden', 'viewpage', '2');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('button', 'nextbtn', 'Show Users', array("id" => "btnajax"));
    }
    public function display_report($c_id = null, $r_id = null) {
        global $DB, $OUTPUT, $CFG, $USER;
        $table = new html_table();
        $table->attributes = array("name" => "userlist");
        $table->attributes = array("id" => "userlist");
        $table->width = '100%';
        $table->data  = array();
        if(empty($c_id)) {
            $c_id=1;
            $r_id=3;
        }
        $sql="SELECT usr.firstname, usr.id, usr.lastname, usr.email,usr.phone2,c.fullname
            FROM {course} c
            INNER JOIN {context} cx ON c.id = cx.instanceid
            AND cx.contextlevel = '50' and c.id=$c_id
            INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
            INNER JOIN {role} r ON ra.roleid = r.id
            INNER JOIN {user} usr ON ra.userid = usr.id
            WHERE r.id = $r_id";
        $count  =  $DB->record_exists_sql($sql, array ($params=null));
        if($count >= 1) {
            $table->head  = array(get_string('serial_no', 'block_sms'), get_string('name', 'block_sms'), get_string('cell_no', 'block_sms'), get_string('select', 'block_sms'));
            $table->size  = array('10%', '20%', '20%', '20%');
            $table->align  = array('center', 'left', 'center', 'center');
            $rs = $DB->get_recordset_sql($sql, array(), null, null);
            $i=0;
            foreach ($rs as $log) {
                $fullname = $log->firstname;
                $row = array();
                $row[] = ++$i;
                $row[] = $log->firstname;
                $row[] = $log->phone2;
                $row[] = "<input type='checkbox' class='usercheckbox' name='user[]' value='$log->id'/>";
                $table->data[] = $row;
            }
        }
        else {
            $row = array();
            $row[] = "<div id='load-users' style='border: 1px solid;margin: 10px 0px;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #00529B;background-image: url(".'pic/info.png'."); background-color: #BDE5F8;border-color: #3b8eb5;'>Record not Found</div>";
            $table->data[] = $row;
        }
        return $table;
    }
}
// Display SMS Template.
class template_form extends moodleform {
    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'sms_template_header', get_string('sms_template_header', 'block_sms'));
        $mform->addElement('text', 'tname', 'Name:', array('size' => 44, 'maxlength' => 160));
        $mform->addRule('tname', 'Please Insert Template Name', 'required', 'client');
        $mform->setType('tname', PARAM_TEXT);
        $mform->addElement('textarea', 'template', 'Message:', array('rows' => '6', 'cols' => '47', 'maxlength' => '160', 'id' => 'asd123'));
        $mform->addRule('template', 'Please Insert Template Message', 'required', 'client');
        $mform->setType('template', PARAM_TEXT);
         $mform->addElement('hidden', 'viewpage', '2');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons();
    }
  public function validation($data, $files) {
        global $DB;
        $errors = array();
        if ($data['tname'] == "") {
            $errors['tname'] = "Please Insert Temaplte Name.";

            if ($DB->record_exists('block_sms_template', array('tname' => $data['tname']))) {
                $errors['template'] = 'Template Name is already exists';
            }
            return $errors;
        } else
            return true;
    }

    public function display_report() {
        global $DB, $OUTPUT, $CFG, $USER;
        $table = new html_table();
        $table->head  = array(get_string('serial_no', 'block_sms'), get_string('name', 'block_sms'), get_string('msg_body', 'block_sms'), get_string('edit', 'block_sms'), get_string('delete', 'block_sms'));
        $table->size  = array('10%', '20%', '50%', '10%', '10%');
        $table->align  = array('center', 'left', 'left', 'center', 'center');
        $table->width = '100%';
        $table->data  = array();
        $sql="SELECT * FROM {block_sms_template}";
        $rs = $DB->get_recordset_sql($sql, array(),  null, null);
        
        $i=0;
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$i;
            $row[] = $log->tname;
            $row[] = $log->template;
            $row[] = '<a  title="Edit" href="'.$CFG->wwwroot.'/blocks/sms/view.php?viewpage=3&edit=edit&id='.$log->id.'"/><img src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" /></a> ';
            $row[] = '<a  title="Remove" href="'.$CFG->wwwroot.'/blocks/sms/view.php?viewpage=3&rem=remove&id='.$log->id.'"/><img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a>';
            $table->data[] = $row;
        }
        return $table;
    }
}