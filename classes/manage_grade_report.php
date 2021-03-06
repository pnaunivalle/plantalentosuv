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
 * Grade report manager class
 *
 * @package    local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

use moodle_exception;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');
require_once($CFG->dirroot.'/grade/report/user/lib.php');

/**
 * Grade report manager class
 *
 * @package   local_plantalentosuv
 * @copyright 2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_grade_report {

    /**
     * Get user grades
     *
     * @param  array $usersids
     * @return array $usergrades
     * @since Moodle 3.10
     */
    public function get_user_grades($usersids) {

        global $DB;

        $usergradesreport = array();

        $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');

        // Validate params.
        $parentcategory = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);
        $parentcategoryid = $parentcategory->id;

        $sqlquery = "SELECT cc.id
                    FROM {course_categories} cc
                    WHERE cc.parent = ?";

        $categories = $DB->get_records_sql($sqlquery, array($parentcategoryid));

        foreach ($usersids as $userid) {

            $userreport = array();

            // Get user data.
            $sqlquery = "SELECT u.id, u.username, u.lastname, u.firstname, u.email
                        FROM {user} u
                        WHERE id = ".$userid;

            $userdata = $DB->get_record_sql($sqlquery);

            $usercourses = enrol_get_users_courses($userid, true, null, 'id');

            $userreport['userid'] = $userid;
            $userreport['username'] = $userdata->username;
            $userreport['lastname'] = $userdata->lastname;
            $userreport['firstname'] = $userdata->firstname;
            $userreport['email'] = $userdata->email;
            $userreport['courses'] = array();

            foreach ($usercourses as $course) {

                $coursereport = array();

                if (!array_key_exists($course->category, $categories)) {
                    unset($usercourses[$course->id]);
                } else {

                    $gpr = new \grade_plugin_return(
                        array(
                            'type' => 'report',
                            'plugin' => 'user',
                            'courseid' => $course->id,
                            'userid' => $userid)
                        );

                    $context = \context_course::instance($course->id);

                    $gradereport = new \grade_report_user($course->id, $gpr, $context, $userid);
                    $gradereport->fill_table();

                    $coursereport['courseid'] = $course->id;
                    $coursereport['shortname'] = $course->shortname;
                    $coursereport['fullname'] = $course->fullname;
                    $coursereport['items'] = array();

                    $itemsdata = array();

                    foreach ($gradereport->tabledata as $item) {
                        $itemdata = array();

                        if (isset($item['weight'])) {

                            $itemraw = explode('_', $item['itemname']['id']);

                            $itemid = $itemraw[1];
                            $itemtype = $itemraw[0];

                            if ($itemtype == 'cat') {
                                $table = 'grade_categories';
                                $aggregation = $DB->get_record($table, array("id" => $itemid))->aggregation;
                            } else {
                                $table = 'grade_items';
                                $aggregation = $DB->get_record($table, array("id" => $itemid))->aggregationcoef;
                            }

                            $itemdata['itemid'] = $itemid;
                            $itemdata['grade'] = $item['grade']['content'];
                            $itemdata['aggregation'] = $aggregation;

                            if ($item['feedback']['content'] == "&nbsp;") {
                                $itemdata['feedback'] = null;
                            } else {
                                $itemdata['feedback'] = $item['feedback']['content'];
                            }

                            array_push($itemsdata, $itemdata);
                        }
                    }

                    $coursereport['items'] = $itemsdata;

                    array_push($userreport['courses'], $coursereport);
                }
            }

            array_push($usergradesreport, $userreport);
        }

        return $usergradesreport;
    }

    /**
     * Get course grade items of a category
     *
     * @param int $idcategory
     * @return array $$coursesitemsreport
     * @since Moodle 3.10
     */
    public function get_course_items($idcategory) {

        global $DB;

        define("TYPE_CATEGORY", "category");
        define("TYPE_ITEM", "item");
        define("TYPE_CATEGORYITEM", "categoryitem");
        define("TYPE_FILLERLAST", "fillerlast");
        define("TYPE_COURSEITEM", "courseitem");
        define("TYPE_FILLER", "filler");
        define("TYPE_FILLERFIRST", "fillerfirst");

        $coursesitemsreport = array();

        $sqlquery = "SELECT c.id, c.fullname, c.shortname, c.idnumber, c.category, cc.name as categoryname
                    FROM {course} c
                         INNER JOIN {course_categories} cc ON cc.id = c.category
                    WHERE cc.parent = ?";

        $courses = $DB->get_records_sql($sqlquery, array($idcategory));

        foreach ($courses as $course) {

            $courseitemsreport = array();
            $courseitemsreport['courseid'] = $course->id;
            $courseitemsreport['fullname'] = $course->fullname;
            $courseitemsreport['shortname'] = $course->shortname;
            $courseitemsreport['idnumber'] = $course->idnumber;
            $courseitemsreport['items'] = array();

            $gpr = new \grade_plugin_return(
                array(
                    'type' => 'report',
                    'plugin' => 'grader',
                    'courseid' => $course->id
                    )
                );

            $context = \context_course::instance($course->id);

            $reportgrader = new \grade_report_grader($course->id, $gpr, $context);

            $coursegradeobjects = $reportgrader->gtree->top_element['children'];

            foreach ($coursegradeobjects as $gradeobject) {

                $objectreport = array();
                $objectreport['type'] = $gradeobject['type'];

                if ($gradeobject['type'] == TYPE_CATEGORY) {

                    $objectreport['categoryname'] = $gradeobject['object']->fullname;
                    $objectreport['children'] = array();

                    foreach ($gradeobject['children'] as $itemobject) {

                        $itemreport = array();

                        $itemreport['itemid'] = $itemobject['object']->id;
                        $itemreport['itemtype'] = $itemobject['object']->itemtype;
                        $itemreport['iteminstance'] = $itemobject['object']->iteminstance;
                        $itemreport['grademax'] = $itemobject['object']->grademax;
                        $itemreport['grademin'] = $itemobject['object']->grademin;
                        $itemreport['gradepass'] = $itemobject['object']->gradepass;

                        if ($itemobject['type'] == TYPE_ITEM) {

                            $itemreport['itemname'] = $itemobject['object']->itemname;

                        } else if ($itemobject['type'] == TYPE_CATEGORYITEM) {

                            $itemreport['itemname'] = $gradeobject['object']->fullname;

                        }

                        array_push($objectreport['children'], $itemreport);
                    }

                } else if ($gradeobject['type'] == TYPE_ITEM) {

                    $objectreport['itemid'] = $gradeobject['object']->id;
                    $objectreport['itemtype'] = $gradeobject['object']->itemtype;
                    $objectreport['iteminstance'] = $gradeobject['object']->iteminstance;
                    $objectreport['grademax'] = $gradeobject['object']->grademax;
                    $objectreport['grademin'] = $gradeobject['object']->grademin;
                    $objectreport['gradepass'] = $gradeobject['object']->gradepass;
                    $objectreport['itemname'] = $gradeobject['object']->itemname;

                } else if ($gradeobject['type'] == TYPE_FILLERLAST) {

                    $objectreport['itemid'] = $gradeobject['children'][0]['object']->id;
                    $objectreport['itemtype'] = $gradeobject['children'][0]['object']->itemtype;
                    $objectreport['iteminstance'] = $gradeobject['children'][0]['object']->iteminstance;
                    $objectreport['grademax'] = $gradeobject['children'][0]['object']->grademax;
                    $objectreport['grademin'] = $gradeobject['children'][0]['object']->grademin;
                    $objectreport['gradepass'] = $gradeobject['children'][0]['object']->gradepass;
                    $objectreport['itemname'] = "Total course";

                } else if ($gradeobject['type'] == TYPE_COURSEITEM) {

                    $objectreport['itemid'] = $gradeobject['object']->id;
                    $objectreport['itemtype'] = $gradeobject['object']->itemtype;
                    $objectreport['iteminstance'] = $gradeobject['object']->iteminstance;
                    $objectreport['grademax'] = $gradeobject['object']->grademax;
                    $objectreport['grademin'] = $gradeobject['object']->grademin;
                    $objectreport['gradepass'] = $gradeobject['object']->gradepass;
                    $objectreport['itemname'] = "Total course";

                } else if ($gradeobject['type'] == TYPE_FILLER || $gradeobject['type'] == TYPE_FILLERFIRST) {

                    $objectreport['itemid'] = $gradeobject['children'][0]['object']->id;
                    $objectreport['itemtype'] = $gradeobject['children'][0]['object']->itemtype;
                    $objectreport['iteminstance'] = $gradeobject['children'][0]['object']->iteminstance;
                    $objectreport['grademax'] = $gradeobject['children'][0]['object']->grademax;
                    $objectreport['grademin'] = $gradeobject['children'][0]['object']->grademin;
                    $objectreport['gradepass'] = $gradeobject['children'][0]['object']->gradepass;
                    $objectreport['itemname'] = $gradeobject['children'][0]['object']->itemname;
                }

                array_push($courseitemsreport['items'], $objectreport);
            }

            array_push($coursesitemsreport, $courseitemsreport);
        }

        return $coursesitemsreport;
    }
}
