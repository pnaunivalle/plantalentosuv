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
 * External local Plan Talentos UV
 * @package   local_plantalentosuv
 * @subpackage db
 * @since Moodle 3.10
 * @copyright  2022 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Plan Talentos UV functions
 * @copyright 2022 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_plantalentosuv_external extends external_api {

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.10
     */
    public static function get_grade_items_by_course_parameters() {
        return new external_function_parameters(
            array(
            )
        );
    }

    /**
     * Get grade items by course
     *
     * @param  int $idcategory
     * @return JSON with grade items by course
     */
    public static function get_grade_items_by_course() {

        global $DB;

        $result = 0;

        // Get grade report.
        $managergradereport = new \local_plantalentosuv\manage_grade_report();

        $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');
        $category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);

        $itemsbycourse = json_encode($managergradereport->get_course_items($category->id), JSON_UNESCAPED_UNICODE);

        // Prepare file record object.

        $context = \context_system::instance();

        $filename = "itemsbycoursereport_ptuv.json";
        $filestorage = get_file_storage();
        $component = 'local_plantalentosuv';
        $filearea = 'plantalentosuvarea';
        $itemid = 0;
        $filepath = '/';

        $reportfile = $filestorage->get_file($context->id, $component, $filearea, $itemid, $filepath, $filename);

        if ($reportfile) {
            $reportfile->delete();
        }

        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'local_plantalentosuv',
            'filearea' => 'plantalentosuvarea',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename);

        // Create and storage file.
        $reportfile = $filestorage->create_file_from_string($fileinfo, $itemsbycourse);

        if ($reportfile) {
            $result = 1;
        }

        $arrayresult = array(
            'result' => $result,
            'warnings' => []
        );

        return $arrayresult;
    }

    /**
     * Returns the description of the external function get grade items by course return value.
     *
     * @return external_description
     * @since Moodle 3.10
     */
    public static function get_grade_items_by_course_returns() {
        return new external_single_structure(array(
            'result' => new external_value(PARAM_INT, 'Result of report creation'),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.10
     */
    public static function get_attendance_sessions_by_course_parameters() {
        return new external_function_parameters(
            array(
            )
        );
    }

    /**
     * Get attendance sessions by course
     *
     * @param  int $idcategory
     * @return JSON with grade items by course
     */
    public static function get_attendance_sessions_by_course() {

        global $DB;

        $result = 0;

        $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');
        $category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);

        $managerattendance = new \local_plantalentosuv\manage_attendance();
        $sessionsreport = json_encode($managerattendance->get_course_sessions($category->id), JSON_UNESCAPED_UNICODE);

        // Prepare file record object.

        $context = \context_system::instance();

        $filename = "sessionsbycoursereport_ptuv.json";
        $filestorage = get_file_storage();
        $component = 'local_plantalentosuv';
        $filearea = 'plantalentosuvarea';
        $itemid = 0;
        $filepath = '/';

        $reportfile = $filestorage->get_file($context->id, $component, $filearea, $itemid, $filepath, $filename);

        if ($reportfile) {
            $reportfile->delete();
        }

        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'local_plantalentosuv',
            'filearea' => 'plantalentosuvarea',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename);

        // Create and storage file.
        $reportfile = $filestorage->create_file_from_string($fileinfo, $sessionsreport);

        if ($reportfile) {
            $result = 1;
        }

        $arrayresult = array(
            'result' => $result,
            'warnings' => []
        );

        return $arrayresult;
    }

    /**
     * Returns the description of the external function get grade items by course return value.
     *
     * @return external_description
     * @since Moodle 3.10
     */
    public static function get_attendance_sessions_by_course_returns() {
        return new external_single_structure(array(
            'result' => new external_value(PARAM_INT, 'Result of report creation'),
            'warnings' => new external_warnings()
        ));
    }
}
