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

require_once(dirname(__FILE__).'/../../../mod/attendance/locallib.php');
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

        $userattendances = array();

        foreach ($usersids as $userid) {

            $usercourses = enrol_get_users_courses($userid);

            // Get user data.
            $sqlquery = "SELECT u.id, u.username, u.lastname, u.firstname, u.email
                        FROM {user} u
                        WHERE id = ".$userid;

            $userdata = $DB->get_record_sql($sqlquery);

            if (!empty($usercourses)) {

                list($csql, $uparams) = $DB->get_in_or_equal(array_keys($usercourses), SQL_PARAMS_NAMED, 'cid0');

                $sqlquery = "SELECT att.id as attid,
                                    att.course as courseid,
                                    course.fullname as coursefullname,
                                    course.shortname as courseshortname,
                                    course.startdate as coursestartdate,
                                    att.name as attname,
                                    att.grade as attgrade
                            FROM {attendance} att
                            JOIN {course} course
                                ON att.course = course.id
                            WHERE att.course $csql
                            ORDER BY coursefullname ASC, attname ASC";

                $params = array_merge($uparams, array('uid' => $userid));

                $courseattendanceactivities = $DB->get_records_sql($sqlquery, $params);

                $userattendance = array();
                $userattendance['userid'] = $userid;
                $userattendance['username'] = $userdata->username;
                $userattendance['lastname'] = $userdata->lastname;
                $userattendance['firstname'] = $userdata->firstname;
                $userattendance['email'] = $userdata->email;
                $userattendance['courses'] = array();

                foreach ($courseattendanceactivities as $attendanceactivity) {
                    if (!empty($attendanceactivity)) {

                        $summary = new mod_attendance_summary($attendanceactivity->attid, $userid);

                        // Data for student sessions report.
                        $cm = get_coursemodule_from_instance('attendance', $attendanceactivity->attid, 0, false, MUST_EXIST);
                        $attendance = $DB->get_record('attendance', array('id' => $attendanceactivity->attid), '*', MUST_EXIST);
                        $courserecord = $DB->get_record('course', array('id' => $attendanceactivity->courseid), '*', MUST_EXIST);
                        $context = \context_module::instance($cm->id);

                        $pageparams = new \mod_attendance_view_page_params();

                        $pageparams->edit = -1;
                        $pageparams->studentid = $userid;
                        $pageparams->mode = 2;
                        $pageparams->view = 5;
                        $pageparams->curdate = $courserecord->startdate;
                        $pageparams->groupby = null;
                        $pageparams->sesscourses = null;

                        $pageparams->init($cm);

                        $att = new \mod_attendance_structure($attendance, $cm, $courserecord, $context, $pageparams);

                        $statuses = $att->get_statuses();
                        $fullsessionlogsraw = $att->get_user_filtered_sessions_log_extended($userid);
                        $fullsessionlogs = array();

                        foreach ($fullsessionlogsraw as $sessionlograw) {

                            // Only sessions taken are added.
                            if ($sessionlograw->statusid) {
                                $sessionlog = array();
                                $sessionlog['sessionid'] = $sessionlograw->id;
                                $sessionlog['timestamp'] = $sessionlograw->sessdate;
                                $sessionlog['description'] = $sessionlograw->description;
                                $sessionlog['statusid'] = $sessionlograw->statusid;
                                $sessionlog['statusacronym'] = $statuses[$sessionlograw->statusid]->acronym;
                                $sessionlog['statusdescription'] = $statuses[$sessionlograw->statusid]->description;
                                $sessionlog['duration'] = $sessionlograw->duration;

                                array_push($fullsessionlogs, $sessionlog);
                            }
                        }

                        $courseinfo = array();
                        $courseinfo['courseid'] = $attendanceactivity->courseid;
                        $courseinfo['courseshortname'] = $attendanceactivity->courseshortname;
                        $courseinfo['coursefullname'] = $attendanceactivity->coursefullname;
                        $courseinfo['attendance'] = array();
                        $courseinfo['attendance']['attendanceid'] = $attendanceactivity->attid;
                        $courseinfo['attendance']['attendancename'] = $attendanceactivity->attname;
                        $courseinfo['attendance']['takensessionssumary'] = $summary->get_taken_sessions_summary_for($userid);
                        $courseinfo['attendance']['fullsessionslog'] = $fullsessionlogs;
                    }

                    array_push(
                        $userattendance['courses'],
                        $courseinfo);
                }
            }

            array_push($userattendances, $userattendance);
        }

        return $userattendances;
    }
}
