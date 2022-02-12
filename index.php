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

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

$categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');

// Validate params.
$category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);
$categoryid = $category->id;

$categorycontext = context_coursecat::instance($categoryid);

if (!has_capability('local/plantalentosuv:viewreport', $categorycontext)) {
    require_capability('local/plantalentosuv:viewreport', $categorycontext);
}

$data = new \stdClass();

$PAGE->set_url(new moodle_url('/local/plantalentosuv/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_plantalentosuv'));
$PAGE->set_heading(get_string('header_plantalentosuv', 'local_plantalentosuv'));
$PAGE->set_pagelayout('standard');

$today = getdate();

$attendancefilename = "attendancereport_ptuv_".$today['mday']."_".$today['mon']."_".$today['year'].".json";
$gradesfilename = "gradesreport_ptuv_".$today['mday']."_".$today['mon']."_".$today['year'].".json";

$urltoattendancereport = moodle_url::make_pluginfile_url($systemcontext->id,
                                                        'local_plantalentosuv',
                                                        'plantalentosuvarea',
                                                        0,
                                                        '/',
                                                        $attendancefilename,
                                                        true);

$urltogradesreport = moodle_url::make_pluginfile_url($systemcontext->id,
                                                        'local_plantalentosuv',
                                                        'plantalentosuvarea',
                                                        0,
                                                        '/',
                                                        $gradesfilename,
                                                        true);

// Get files in the filearea.
$fs = get_file_storage();
$files = $fs->get_area_files($systemcontext->id, 'local_plantalentosuv', 'plantalentosuvarea', false, 'filename', false);

$data->filesinfilearea = count($files).get_string('counter_files', 'local_plantalentosuv');

$data->urltoattendancereport = $urltoattendancereport;
$data->urltogradesreport = $urltogradesreport;
$data->imageattendance = $OUTPUT->image_url('attendance', 'local_plantalentosuv');
$data->imagegrades = $OUTPUT->image_url('grades', 'local_plantalentosuv');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_plantalentosuv/index', $data);

echo $OUTPUT->footer();
