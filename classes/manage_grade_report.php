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

                            $itemnameraw = $item['itemname']['content'];

                            $dom = new \DOMDocument();
                            $dom->loadHTML($itemnameraw);
                            $domx = new \DOMXPath($dom);

                            $entries = $domx->evaluate("//a");

                            if ($entries->length > 0) {
                                foreach ($entries as $entry) {
                                    $itemid = "";
                                    $itemname = "";
                                    $itemurl = $entry->getAttribute('href');
                                    $itemid = explode('&', explode('id=', $itemurl)[1])[0];
                                    $itemname = $entry->nodeValue;

                                }
                            } else {
                                $entries = $domx->evaluate("//span");

                                foreach ($entries as $entry) {
                                    $itemid = $course->id;
                                    $itemname = $entry->nodeValue;

                                }
                            }

                            $itemdata['itemid'] = $itemid;
                            $itemdata['itemname'] = $itemname;
                            $itemdata['grade'] = $item['grade']['content'];

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

            $gpr = new \grade_plugin_return(
                array(
                    'type' => 'report',
                    'plugin' => 'grader',
                    'courseid' => $course->id
                    )
                );

            $context = \context_course::instance($course->id);

            $reportgrader = new \grade_report_grader($course->id, $gpr, $context);

            $courseitemsraw = $reportgrader->gtree->top_element['children'];

            foreach ($courseitemsraw as $courseitem) {
                $itemreport = array();
                $itemreport['itemid'] = $courseitem['object']->id;

                $itemreport['itemtype'] = $courseitem['object']->itemtype;

                $itemreport['iteminstance'] = $courseitem['object']->iteminstance;
                $itemreport['grademax'] = $courseitem['object']->grademax;
                $itemreport['grademin'] = $courseitem['object']->grademin;
                $itemreport['gradepass'] = $courseitem['object']->gradepass;

                if ($courseitem['object']->itemname
                    && $courseitem['object']->itemmodule) {
                    $itemreport['itemname'] = $courseitem['object']->itemname;
                    $itemreport['itemmodule'] = $courseitem['object']->itemmodule;
                } else {
                    $itemreport['itemname'] = "Total del curso";
                    $itemreport['itemmodule'] = "course";
                }

                array_push($courseitemsreport['items'], $itemreport);
            }

            array_push($coursesitemsreport, $courseitemsreport);
        }

        return $coursesitemsreport;
    }
}
