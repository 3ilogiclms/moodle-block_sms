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
 * @date: 17-Jul-2014
 */

defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    // API SETTINGS

    $settings->add(new admin_setting_heading(
            'headerapi', 'API settings', 'Set up the API for SMS messaging'
    ));

    $settings->add(new admin_setting_configtext(get_string('block_sms_apikey', 'block_sms'), get_string('sms_api_key', 'block_sms'), get_string('sms_api_key', 'block_sms'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext(get_string('block_sms_api_username', 'block_sms'), get_string('sms_api_username', 'block_sms'), get_string('sms_api_username', 'block_sms'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtext(get_string('block_sms_api_password', 'block_sms'), get_string('sms_api_password', 'block_sms'), get_string('sms_api_password', 'block_sms'), '', PARAM_TEXT));

    $settings->add(new admin_setting_configselect('block_sms_api', 'SMS API Name', 'Select Api which you are using', 'Sendsms.pk', array('Clickatell', 'Sendsms.pk')));

    // ROLE SETTINGS

    $settings->add(new admin_setting_heading(
            'headerrolesettings', 'Role settings', 'Define the users that can receive SMS'
    ));


    $settings->add(new admin_setting_configselect('block_sms_rolenames', 'Role names to use', 'Select Moodle role names which you want to use', 'Moodle short names', array('Moodle short names', 'Custom names', 'Archetypes')), 'Moodle short names');

    //returns a list of role IDs separated by ','

    $roles = $DB->get_records('role', null, 'sortorder ASC');

    $default_sns = array('manager', 'editingteacher', 'teacher', 'student');
    $defaults = array_filter($roles, function ($role) use ($default_sns) {
        return in_array($role->shortname, $default_sns);
    });

    $only_names = function ($role) {
        return $role->shortname;
    };

    $settings->add(
            new admin_setting_configmultiselect('block_sms_roleselection', 'Select roles allowed to receive SMS', 'Select multiple roles holding CTRL key. Here roles are shown with their shortnames.', array_keys($defaults), array_map($only_names, $roles)
            )
    );
}
