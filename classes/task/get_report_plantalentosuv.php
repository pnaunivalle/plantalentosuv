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
 * A scheduled task for plan talentos UV cron.
 *
 * @package    local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv\task;

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/../../../../mod/attendance/locallib.php');

/**
 * The main scheduled task for the Plan Talentos UV.
 *
 * @package     local_plantalentosuv
 * @copyright   2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_report_plantalentosuv extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('get_report_plantalentosuv', 'local_plantalentosuv');
    }

    /**
     * Run get report Plan Talentos UV cron
     *
     * @return void
     */
    public function execute() {
        global $DB;

        $timenow = time();
        $starttime = microtime();

        mtrace("Update cron started at: " . date('r', $timenow) . "\n");

        $cohortidnumber = get_config('local_plantalentosuv', 'cohorttotrack');
        // Validate params.
        $cohort = $DB->get_record('cohort', array('idnumber' => $cohortidnumber), '*', MUST_EXIST);
        $cohortid = $cohort->id;

        $context = \context_system::instance();

        $cohortmembers = $DB->get_records_sql("SELECT u.id, u.username, u.email, u.lastname, u.firstname
                                                FROM {user} u, {cohort_members} cm
                                                WHERE u.id = cm.userid AND cm.cohortid = ?
                                                ORDER BY lastname ASC, firstname ASC", array($cohort->id));

        $members[] = array('cohortid' => $cohortid, 'userids' => array_keys($cohortmembers));

        // Get attendance report.

        $managerattendance = new \local_plantalentosuv\manage_attendance();

        $userattendance = $managerattendance->get_attendance_users($members[0]['userids']);
        $userattendancejson = json_encode($userattendance, JSON_UNESCAPED_UNICODE);

        $attendancefilename = "attendancereport_ptuv_".date("d")."_".date("m")."_".date("Y").".json";

        $filestorage = get_file_storage();

        // Prepare file record object.
        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'local_plantalentosuv',
            'filearea' => 'plantalentosuvarea',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $attendancefilename);

        // Create and storage file.
        $attendancefile = $filestorage->create_file_from_string($fileinfo, $userattendancejson);

        if ($attendancefile->get_id() > 0) {

            mtrace("Attendance report file created successfully named ".$attendancefilename);

            // Upload file attendance report to Google Drive.
            $managerupload = new \local_plantalentosuv\upload_files_google_drive();
            $filedescription = "Attendance report created on ".date("d")."_".date("m")."_".date("Y");
            $resultupload = $managerupload->upload_file($attendancefile, 'application/json', $userattendancejson, $filedescription);
        }

        // Get grade report.
        $managergradereport = new \local_plantalentosuv\manage_grade_report();

        $usergrades = $managergradereport->get_user_grades($members[0]['userids']);
        $usergradesjson = json_encode($usergrades, JSON_UNESCAPED_UNICODE);

        $gradesfilename = "gradesreport_ptuv_".date("d")."_".date("m")."_".date("Y").".json";

        $filestorage = get_file_storage();

        // Prepare file record object.
        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'local_plantalentosuv',
            'filearea' => 'plantalentosuvarea',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $gradesfilename);

        // Create and storage file.
        $gradesfile = $filestorage->create_file_from_string($fileinfo, $usergradesjson);

        // Upload file attendance report to Google Drive.
        if ($gradesfile->get_id() > 0) {

            mtrace("Grades report file created successfully named ".$gradesfilename);

            $managerupload = new \local_plantalentosuv\upload_files_google_drive();
            $filedescription = "Grades report created on ".date("d")."_".date("m")."_".date("Y");
            $resultupload = $managerupload->upload_file($gradesfilename, 'application/json', $usergradesjson, $filedescription);
        }

        // Update courses process completed.
        mtrace("\n" . 'Cron completed at: ' . date('r', time()) . "\n");
        mtrace('Used memory: ' . display_size(memory_get_usage()));
        $difftime = microtime_diff($starttime, microtime());
        mtrace("The scheduled task took " . $difftime . " seconds to finish.\n");
    }
}
