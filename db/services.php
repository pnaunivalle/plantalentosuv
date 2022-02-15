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
 * Web services for Plan Talentos UV plugin
 * @package   local_plantalentosuv
 * @subpackage db
 * @since Moodle 3.10
 * @copyright  2022 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_plantalentosuv_get_course_attendance_sessions' => array(
        'classname'          => 'local_plantalentosuv_external',
        'methodname'         => 'get_attendance_sessions_by_course',
        'description'        => 'Get course attendance sessions.',
        'requiredcapability' => 'local/plantalentosuv:view_report',
        'type'               => 'read',
        'ajax'               => true,
        'restrictedusers'    => 0,
        'enabled'            => 1,
        'downloadfiles'      => 0,
        'uploadfiles'        => 0
    ),
    'local_plantalentosuv_get_grade_items_by_course' => array(
        'classname'          => 'local_plantalentosuv_external',
        'methodname'         => 'get_grade_items_by_course',
        'description'        => 'Get grade items by course.',
        'requiredcapability' => 'local/plantalentosuv:view_report',
        'type'               => 'read',
        'ajax'               => true,
        'restrictedusers'    => 0,
        'enabled'            => 1,
        'downloadfiles'      => 0,
        'uploadfiles'        => 0
    )
);
