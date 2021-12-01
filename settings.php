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
    }

    $ADMIN->add('localplugins', $settingspage);
}
