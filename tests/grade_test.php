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
 * Unit tests for grade report.
 *
 * @package    local_plantalentosuv
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

use advanced_testcase;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

class grade_testcase extends advanced_testcase {

    protected $teacher;
    protected $students;
    protected $course;

    public function test_generate_grade_report() {

        global $DB;

        $this->resetAfterTest(true);

        $parentcategory = $this->getDataGenerator()->create_category();
        $childcategory = $this->getDataGenerator()->create_category(array('name' => 'Some subcategory',
                                                                        'parent' => $parentcategory->id));

        $this->course = $this->getDataGenerator()->create_course(array('category' => $childcategory->id));
        $att = $this->getDataGenerator()->create_module('attendance', array('course' => $this->course->id));

        $cm = $DB->get_record('course_modules', array('id' => $att->cmid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);


    }
}
