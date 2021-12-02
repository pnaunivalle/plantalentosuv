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

        // Now security checks.
        $context = \context::instance_by_id($cohort->contextid, MUST_EXIST);
        if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
            throw new \invalid_parameter_exception('Invalid context');
        }

        $cohortmembers = $DB->get_records_sql("SELECT u.id, u.username, u.email, u.lastname, u.firstname
                                                FROM {user} u, {cohort_members} cm
                                                WHERE u.id = cm.userid AND cm.cohortid = ?
                                                ORDER BY lastname ASC, firstname ASC", array($cohort->id));

        $members[] = array('cohortid' => $cohortid, 'userids' => array_keys($cohortmembers));

        $managerattendance = new \local_plantalentosuv\manage_attendance();

        $userattendance = $managerattendance->get_attendance_users($members[0]['userids']);

        print_r($userattendance);

        // Update courses process completed.
        mtrace("\n" . 'Cron completado a las: ' . date('r', time()) . "\n");
        mtrace('Memoria utilizada: ' . display_size(memory_get_usage()));
        $difftime = microtime_diff($starttime, microtime());
        mtrace("Tarea programada tardó " . $difftime . " segundos para finalizar.\n");
    }
}
