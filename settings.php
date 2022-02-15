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

/**
 * Settings for plantalentosuv plugin.
 *
 * @package   local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins',
                new admin_category('local_plantalentosuv_settings',
                new lang_string('pluginname', 'local_plantalentosuv')));
    $settingspage = new admin_settingpage('managelocalplantalentosuv', new lang_string('manage', 'local_plantalentosuv'));

    if ($ADMIN->fulltree) {

        // General settings.
        $settingspage->add(new admin_setting_heading(
            'generalsettingsheading',
            new lang_string('generalsettingsheading', 'local_plantalentosuv'),
            new lang_string('generalsettingsheading_desc', 'local_plantalentosuv')));

        $settingspage->add(new admin_setting_configcheckbox(
            'local_plantalentosuv/showinnavigation',
            new lang_string('showinnavigation', 'local_plantalentosuv'),
            new lang_string('showinnavigation_desc', 'local_plantalentosuv'),
            1
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/cohorttotrack',
            new lang_string('cohorttotrack', 'local_plantalentosuv'),
            new lang_string('cohorttotrack_desc', 'local_plantalentosuv'),
            0,
            PARAM_TEXT
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/categorytotrack',
            new lang_string('categorytotrack', 'local_plantalentosuv'),
            new lang_string('categorytotrack_desc', 'local_plantalentosuv'),
            0,
            PARAM_TEXT
        ));

        // Google API settings.
        $settingspage->add(new admin_setting_heading(
            'googleapiheading',
            new lang_string('googleapiheading', 'local_plantalentosuv'),
            new lang_string('googleapiheading_desc', 'local_plantalentosuv')));

        $settingspage->add(new admin_setting_configcheckbox(
            'local_plantalentosuv/uploadtogoogledrive',
            new lang_string('uploadtogoogledrive', 'local_plantalentosuv'),
            new lang_string('uploadtogoogledrive_desc', 'local_plantalentosuv'),
            1
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/jsonkey',
            new lang_string('jsonkey', 'local_plantalentosuv'),
            new lang_string('jsonkey_desc', 'local_plantalentosuv'),
            0,
            PARAM_TEXT
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/jsonpath',
            new lang_string('jsonpath', 'local_plantalentosuv'),
            new lang_string('jsonpath_desc', 'local_plantalentosuv'),
            0,
            PARAM_TEXT
        ));

        // External server settings.
        $settingspage->add(new admin_setting_heading(
            'externalserverheading',
            new lang_string('externalserverheading', 'local_plantalentosuv'),
            new lang_string('externalserverheading_desc', 'local_plantalentosuv')));

        $settingspage->add(new admin_setting_configcheckbox(
            'local_plantalentosuv/uploadtoexternalserver',
            new lang_string('uploadtoexternalserver', 'local_plantalentosuv'),
            new lang_string('uploadtoexternalserver_desc', 'local_plantalentosuv'),
            1
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/ftpusername',
            new lang_string('ftpusername', 'local_plantalentosuv'),
            new lang_string('ftpusername_desc', 'local_plantalentosuv'),
            'username',
            PARAM_TEXT
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/ftpserver',
            new lang_string('ftpserver', 'local_plantalentosuv'),
            new lang_string('ftpserver_desc', 'local_plantalentosuv'),
            'example.com.co',
            PARAM_TEXT
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/ftpport',
            new lang_string('ftpport', 'local_plantalentosuv'),
            new lang_string('ftpport_desc', 'local_plantalentosuv'),
            '21',
            PARAM_TEXT
        ));

        $settingspage->add(new admin_setting_configtext(
            'local_plantalentosuv/ftppassword',
            new lang_string('ftppassword', 'local_plantalentosuv'),
            new lang_string('ftppassword_desc', 'local_plantalentosuv'),
            'yourpasswordhere',
            PARAM_TEXT
        ));
    }

    $ADMIN->add('localplugins', $settingspage);

    $ADMIN->add('reports', new admin_category('plantalentosuv', new lang_string('pluginname', 'local_plantalentosuv')));

    $ADMIN->add('plantalentosuv',
        new admin_externalpage('index', new lang_string('reports', 'local_plantalentosuv'),
            new moodle_url('/local/plantalentosuv/index.php'), 'moodle/site:configview'
        )
    );
}
