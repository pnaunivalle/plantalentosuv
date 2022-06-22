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
 * Unit tests for attendance report.
 *
 * @package    local_plantalentosuv
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

use advanced_testcase;
use mod_attendance_structure;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/attendance/classes/attendance_webservices_handler.php');
require_once($CFG->dirroot . '/mod/attendance/classes/structure.php');
require_once($CFG->dirroot . '/mod/attendance/externallib.php');

class attendance_testcase extends advanced_testcase {

    protected $teacher;
    protected $students;
    protected $course;
    protected $attendance;

    public function test_generate_session_report() {

        global $DB;

        $this->resetAfterTest(true);

        $parentcategory = $this->getDataGenerator()->create_category();
        $childcategory = $this->getDataGenerator()->create_category(array('name' => 'Some subcategory',
                                                                        'parent' => $parentcategory->id));

        $this->course = $this->getDataGenerator()->create_course(array('category' => $childcategory->id));
        $att = $this->getDataGenerator()->create_module('attendance', array('course' => $this->course->id));

        $cm = $DB->get_record('course_modules', array('id' => $att->cmid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

        $this->attendance = new mod_attendance_structure($att, $cm, $course);

        $this->create_and_enrol_users();
        $this->setUser($this->teacher);

        $session = new stdClass();
        $session->sessdate = time();
        $session->duration = 6000;
        $session->description = "";
        $session->descriptionformat = 1;
        $session->descriptionitemid = 0;
        $session->timemodified = time();
        $session->statusset = 0;
        $session->groupid = 0;
        $session->absenteereport = 1;
        $session->calendarevent = 0;

        // Creating session.
        $this->sessions[] = $session;

        $this->attendance->add_sessions($this->sessions);

        $managerattendance = new manage_attendance();
        $reportsessions = $managerattendance->get_course_sessions($parentcategory->id);

        $this->assertIsArray($reportsessions);

    }

    public function test_get_attendance_report() {

        global $DB;

        $cohortidnumber = get_config('local_plantalentosuv', 'cohorttotrack');
        // Validate params.
        $cohort = $DB->get_record('cohort', array('idnumber' => $cohortidnumber), '*', MUST_EXIST);
        $cohortid = $cohort->id;

        $cohortmembers = $DB->get_records_sql("SELECT DISTINCT u.id, u.username, u.email, u.lastname, u.firstname
                                                FROM {user} u, {cohort_members} cm
                                                WHERE u.id = cm.userid AND cm.cohortid = ?
                                                ORDER BY lastname ASC, firstname ASC", array($cohort->id));

        $members[] = array('cohortid' => $cohortid, 'userids' => array_keys($cohortmembers));

        // Get attendance report.

        $managerattendance = new \local_plantalentosuv\manage_attendance();
        $userattendance = $managerattendance->get_attendance_users($members[0]['userids']);
    }

    /** Creating 10 students and 1 teacher. */
    protected function create_and_enrol_users() {
        $this->students = array();
        for ($i = 0; $i < 10; $i++) {
            $this->students[] = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        }

        $this->teacher = $this->getDataGenerator()->create_and_enrol($this->course, 'editingteacher');
    }

}
