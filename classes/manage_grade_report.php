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
 * Grade report manager class
 *
 * @package    local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

use moodle_exception;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/grade/export/xml/grade_export_xml.php');

/**
 * Grade report manager class
 *
 * @package   local_plantalentosuv
 * @copyright 2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_grade_report {

    /**
     * Get user grades
     *
     * @param  array $usersids
     * @return array $usergrades
     * @since Moodle 3.10
     */
    public function get_user_grades($usersids) {
        global $DB;

        $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');

        // Validate params.
        $category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);
        $categoryid = $category->id;

        foreach ($usersids as $userid) {

            $usercourses = enrol_get_users_courses($userid, true, null, 'id');

            foreach ($usercourses as $course) {
                if ($course->category != $categoryid) {
                    unset($usercourses[$course->id]);
                } else {
                    $courseobject = $DB->get_record('course', array('id' => $course->id));
                    $gradereport = new \grade_export_xml($courseobject, 0, null);
                    if($course->id == 64976) {
                        print_r($gradereport);
                        die();
                    }

                }

            }

            // Get user data.
            $sqlquery = "SELECT u.id, u.username, u.lastname, u.firstname, u.email
                        FROM {user} u
                        WHERE id = ".$userid;

            $userdata = $DB->get_record_sql($sqlquery);

        }
    }
}
