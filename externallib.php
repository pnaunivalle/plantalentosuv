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
                'idcategory' => new external_value(PARAM_INT, 'Academic period code')
            )
        );
    }

    /**
     * get_grade_items_by_course
     *
     * @param  int $idcategory
     * @return JSON with grade items by course
     */
    public static function get_grade_items_by_course($idcategory) {

        // Get grade report.
        $managergradereport = new \local_plantalentosuv\manage_grade_report();

        $itemsbycourse = json_encode($managergradereport->get_course_items($idcategory));

        $arrayresult = array(
            'resgradeitemsbycourseult' => $itemsbycourse,
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
            'gradeitemsbycourse' => new external_value(PARAM_RAW, 'JSON for grade items by course'),
            'warnings' => new external_warnings()
        ));
    }

}
