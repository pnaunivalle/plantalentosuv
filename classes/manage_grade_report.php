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

            $gradecategories = $DB->get_records(
                'grade_categories',
                array('courseid' => $course->id,
                    'parent' => null));

            $courseitem = $DB->get_record('grade_items', array('courseid' => $course->id, 'itemtype' => 'course'))->iteminstance;

            $gradeitems = $DB->get_records('grade_items', array('courseid' => $course->id, 'categoryid' => $courseitem));

            foreach ($gradeitems as $gradeitem) {
                $item = array();

                $item['itemid'] = $gradeitem->id;
                $item['itemtype'] = $gradeitem->itemtype;
                $item['itemname'] = $gradeitem->itemname;
                $item['iteminstance'] = $gradeitem->iteminstance;
                $item['grademax'] = $gradeitem->grademax;
                $item['grademin'] = $gradeitem->grademin;
                $item['gradepass'] = $gradeitem->gradepass;

                array_push($courseitemsreport['items'], $item);
            }

            foreach ($gradecategories as $gradecategory) {
                $courseitemsreport['items'] = array_merge($courseitemsreport['items'], $this->get_child_grade_categories($gradecategory->id));
            }

            // Get professor.
            $utils = new utils();
            $professors = $utils->get_users_with_specific_role($course->id, 'professor');

            $courseitemsreport['professors'] = array();

            foreach ($professors as $professor) {
                $professortoreturn = array();
                $professortoreturn['username'] = $professor->username;
                $professortoreturn['lastname'] = $professor->lastname;
                $professortoreturn['firstname'] = $professor->firstname;

                array_push($courseitemsreport['professors'], $professortoreturn);
            }

            array_push($coursesitemsreport, $courseitemsreport);
        }

        return $coursesitemsreport;
    }

    /**
     * get_child_grade_categories
     *
     * @return void
     */
    private function get_child_grade_categories($parent = '') {
        global $DB;

        $gradecategories = $DB->get_records(
            'grade_categories',
            array('parent' => $parent));

        if (!empty($gradecategories)) {

            $childcategories = array();

            foreach ($gradecategories as $gradecategory) {
                $category = array();
                $category['itemid'] = $gradecategory->id;
                $category['type'] = "category";
                $category['categoryname'] = $gradecategory->fullname;
                $category['children'] = array();

                $gradeitems = $DB->get_records('grade_items', array('categoryid' => $gradecategory->id));

                foreach ($gradeitems as $gradeitem) {
                    $item = array();

                    $item['itemid'] = $gradeitem->id;
                    $item['itemtype'] = $gradeitem->itemtype;
                    $item['itemname'] = $gradeitem->itemname;
                    $item['iteminstance'] = $gradeitem->iteminstance;
                    $item['grademax'] = $gradeitem->grademax;
                    $item['grademin'] = $gradeitem->grademin;
                    $item['gradepass'] = $gradeitem->gradepass;

                    array_push($category['children'], $item);
                }

                $categories = $this->get_child_grade_categories($gradecategory->id);

                $category['children'] = array_merge($category['children'], $categories);

                array_push($childcategories, $category);
            }

            return $childcategories;

        } else {
            return array();
        }
    }
}
