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
 * Utils class
 *
 * @package    local_plantalentosuv
 * @copyright  2022 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/../../../mod/attendance/locallib.php');
require_once(dirname(__FILE__).'/../../../mod/attendance/classes/summary.php');

/**
 * Utils class
 *
 * @package   local_plantalentosuv
 * @copyright 2022 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Get all users of a course with a specific role
     * @param int $courseid
     * @param string $role string that can be 'professor' or 'student'
     * @return array $users Array with all users with a specific role
     * @since Moodle 3.10
     */
    public function get_users_with_specific_role(int $courseid, string $role = 'professor') {

        global $DB;

        $users = array();

        $sqlquery = 'SELECT u.username, u.lastname, u.firstname
                     FROM {role_assignments} ra
                          INNER JOIN {user} u ON u.id = ra.userid
                          INNER JOIN {context} ctx ON ctx.id = ra.contextid
                     WHERE ctx.contextlevel = 50
                           AND ctx.instanceid = ?';

        if ($role == 'professor') {
            $sqlquery .= 'AND (ra.roleid = ? OR ra.roleid = ?)';
            $parameters = array($courseid, 3, 30);
        } else {
            $sqlquery .= 'AND (ra.roleid = ?)';
            $parameters = array($courseid, 5);
        }

        $users = $DB->get_records_sql($sqlquery, $parameters);

        return $users;
    }
}
