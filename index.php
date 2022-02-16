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
require_once(dirname(__FILE__).'/lib.php');

require_login();

$systemcontext = context_system::instance();

// Validate access.
$categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');
$category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);
$categoryid = $category->id;
$categorycontext = context_coursecat::instance($categoryid);

if (!has_capability('local/plantalentosuv:viewreport', $categorycontext)) {
    require_capability('local/plantalentosuv:viewreport', $categorycontext);
}

// Setings page.
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/local/plantalentosuv/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_plantalentosuv'));
$PAGE->set_heading(get_string('header_plantalentosuv', 'local_plantalentosuv'));
$PAGE->set_pagelayout('standard');
$PAGE->requires->js_call_amd('local_plantalentosuv/get_unscheduled_reports', 'init');

$today = getdate();

// Validate files.
$attendancefilename = "attendancereport_ptuv_".date("d")."_".date("m")."_".date("Y").".json";
$gradesfilename = "gradesreport_ptuv_".date("d")."_".date("m")."_".date("Y").".json";
$itemsbycoursefilename = "itemsbycoursereport_ptuv.json";
$sessionsbycoursefilename = "sessionsbycoursereport_ptuv.json";

$filestorage = get_file_storage();
$component = 'local_plantalentosuv';
$filearea = 'plantalentosuvarea';
$itemid = 0;
$filepath = '/';

$fileattendanceinfo = $filestorage->get_file($systemcontext->id, $component, $filearea, $itemid, $filepath, $attendancefilename);
$filegradesinfo = $filestorage->get_file($systemcontext->id, $component, $filearea, $itemid, $filepath, $gradesfilename);
$fileitemsbycourse = $filestorage->get_file($systemcontext->id, $component, $filearea, $itemid, $filepath, $itemsbycoursefilename);
$filesessionsbycourse = $filestorage->get_file($systemcontext->id, $component, $filearea, $itemid,
                                                $filepath, $sessionsbycoursefilename);

if ($fileattendanceinfo) {
    $urltoattendancereport = moodle_url::make_pluginfile_url($systemcontext->id,
                                                        $component,
                                                        $filearea,
                                                        $itemid,
                                                        $filepath,
                                                        $attendancefilename,
                                                        true);
} else {
    $urltoattendancereport = '';
}

if ($filegradesinfo) {
    $urltogradesreport = moodle_url::make_pluginfile_url($systemcontext->id,
                                                        $component,
                                                        $filearea,
                                                        $itemid,
                                                        $filepath,
                                                        $gradesfilename,
                                                        true);
} else {
    $urltogradesreport = '';
}

if ($fileitemsbycourse) {
    $urltoitemsbycoursereport = moodle_url::make_pluginfile_url($systemcontext->id,
                                                        $component,
                                                        $filearea,
                                                        $itemid,
                                                        $filepath,
                                                        $itemsbycoursefilename,
                                                        true);
} else {
    $urltoitemsbycoursereport = '';
}

if ($filesessionsbycourse) {
    $urltosessionsreport = moodle_url::make_pluginfile_url($systemcontext->id,
                                                        $component,
                                                        $filearea,
                                                        $itemid,
                                                        $filepath,
                                                        $sessionsbycoursefilename,
                                                        true);
} else {
    $urltosessionsreport = '';
}

// Get files in the filearea.
$files = $filestorage->get_area_files($systemcontext->id, 'local_plantalentosuv', 'plantalentosuvarea', false, 'filename', false);
$htmllistsfiles = local_plantalentosuv_list_files_html($files);

$data = new \stdClass();
$data->filesinfilearea = count($files).get_string('counter_files', 'local_plantalentosuv');
$data->urltoattendancereport = $urltoattendancereport;
$data->urltogradesreport = $urltogradesreport;
$data->urltoitemsbycoursereport = $urltoitemsbycoursereport;
$data->urltosessionsreport = $urltosessionsreport;
$data->imageattendance = $OUTPUT->image_url('attendance', 'local_plantalentosuv');
$data->imagegrades = $OUTPUT->image_url('grades', 'local_plantalentosuv');
$data->iconwarning = $OUTPUT->image_url('i/warning', 'local_plantalentosuv');
$data->htmllistsfiles = $htmllistsfiles;

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_plantalentosuv/index', $data);

echo $OUTPUT->footer();
