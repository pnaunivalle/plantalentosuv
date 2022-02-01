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
 * Report view
 *
 * @package    local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_login();

$data = new \stdClass();

$context = \context_system::instance();

$PAGE->set_url(new moodle_url('/local/plantalentosuv/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_plantalentosuv'));
$PAGE->set_heading(get_string('header_plantalentosuv', 'local_plantalentosuv'));
$PAGE->set_pagelayout('standard');

$today = getdate();

$attendancefilename = "attendancereport_ptuv_".$today['mday']."_".$today['mon']."_".$today['year'].".json";
$gradesfilename = "gradesreport_ptuv_".$today['mday']."_".$today['mon']."_".$today['year'].".json";

$urltoattendancereport = moodle_url::make_pluginfile_url($context->id,
                                                        'local_plantalentosuv',
                                                        'plantalentosuvarea',
                                                        0,
                                                        '/',
                                                        $attendancefilename,
                                                        true);

$urltogradesreport = moodle_url::make_pluginfile_url($context->id,
                                                        'local_plantalentosuv',
                                                        'plantalentosuvarea',
                                                        0,
                                                        '/',
                                                        $gradesfilename,
                                                        true);

// Get files in the filearea.
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'local_plantalentosuv', 'plantalentosuvarea', false, 'filename', false);

$data->filesinfilearea = count($files).get_string('counter_files', 'local_plantalentosuv');

$data->urltoattendancereport = $urltoattendancereport;
$data->urltogradesreport = $urltogradesreport;

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_plantalentosuv/index', $data);
echo $OUTPUT->footer();
