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
 * A scheduled task to clean the file area of the Plugin Plan Talentos UV.
 *
 * @package    local_plantalentosuv
 * @copyright  2022 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv\task;

defined('MOODLE_INTERNAL') || die;

/**
 * Task to clean the area of files of plugin Plan Talentos UV
 *
 * @package     local_plantalentosuv
 * @copyright   2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clean_plantalentosuv_filearea extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('clean_plantalentosuv_filearea', 'local_plantalentosuv');
    }

    /**
     * Run clean file area Plan Talentos UV cron
     *
     * @return void
     */
    public function execute() {
        global $DB;

        $timenow = time();
        $starttime = microtime();

        mtrace("Update cron started at: " . date('r', $timenow) . "\n");

        // Code for clean file area.

        // Update courses process completed.
        mtrace("\n" . 'Cron completado a las: ' . date('r', time()) . "\n");
        mtrace('Memoria utilizada: ' . display_size(memory_get_usage()));
        $difftime = microtime_diff($starttime, microtime());
        mtrace("Tarea programada tardó " . $difftime . " segundos para finalizar.\n");
    }
}
