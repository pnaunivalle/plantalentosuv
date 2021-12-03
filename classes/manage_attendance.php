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
 * Attendance manager class
 *
 * @package    local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

use mod_attendance_summary;

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/../../../mod/attendance/classes/summary.php');

/**
 * Attendance manager class
 *
 * @package   local_plantalentosuv
 * @copyright 2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_attendance {

    /**
     * Get attendance for users
     *
     * @param  array $usersids
     * @return array $userattendance
     * @since Moodle 3.10
     */
    public function get_attendance_users($usersids) {
        global $DB;

        $userattendance = array();

        foreach ($usersids as $userid) {

            $usercourses = enrol_get_users_courses($userid);

            // Get user data.
            $sqlquery = "SELECT u.id, u.username, u.lastname, u.firstname, u.email
                        FROM {user} u
                        WHERE id = $userid";

            $userdata = $DB->get_record_sql($sqlquery);

            if (!empty($usercourses)) {

                list($usql, $uparams) = $DB->get_in_or_equal(array_keys($usercourses), SQL_PARAMS_NAMED, 'cid0');

                $sqlquery = "SELECT att.id as attid, att.course as courseid, course.fullname as coursefullname,
                                course.shortname as courseshortname, course.startdate as coursestartdate, att.name as attname,
                                att.grade as attgrade
                            FROM {attendance} att
                            JOIN {course} course
                                ON att.course = course.id
                            WHERE att.course $usql
                            ORDER BY coursefullname ASC, attname ASC";

                $params = array_merge($uparams, array('uid' => $userid));

                $courseattendance = $DB->get_records_sql($sqlquery, $params);

                foreach ($courseattendance as $attendance) {
                    if (!empty($attendance)) {
                        $summary = new mod_attendance_summary($attendance->attid, $userid);
                        $userattendance[$userid]['id'] = $userid;
                        $userattendance[$userid]['username'] = $userdata->username;
                        $userattendance[$userid]['lastname'] = $userdata->lastname;
                        $userattendance[$userid]['firstname'] = $userdata->firstname;
                        $userattendance[$userid]['email'] = $userdata->email;
                        $userattendance[$userid]['course-'.$attendance->courseid]['id'] = $attendance->courseid;
                        $userattendance[$userid]['course-'.$attendance->courseid]['shortname'] = $attendance->courseshortname;
                        $userattendance[$userid]['course-'.$attendance->courseid]['fullname'] = $attendance->coursefullname;
                        $userattendance[$userid]['course-'.$attendance->courseid]['attendance-'.$attendance->attid] = array();

                        $attendancedata = array();
                        $attendancedata['attendance_id'] = $attendance->attid;
                        $attendancedata['attendance_name'] = $attendance->attname;
                        $attendancedata['all_sessions_summary'] = $summary->get_all_sessions_summary_for($userid);

                        array_push(
                            $userattendance[$userid]['course-'.$attendance->courseid]['attendance-'.$attendance->attid],
                            $attendancedata);
                    }
                }
            }
        }

        return $userattendance;
    }

}
