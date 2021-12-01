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
 * This file contains the moodle hooks for the assign module.
 *
 * @package   local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Insert a link to index.php on the site front page navigation menu.
 *
 * @param global_navigation $nav Node representing the front page in the navigation tree.
 */
function local_plantalentosuv_extend_navigation(global_navigation $root) {

    if (get_config('local_plantalentosuv', 'showinnavigation')) {

        $node = navigation_node::create(
            get_string('pluginname', 'local_plantalentosuv'),
            new moodle_url('/local/plantalentosuv/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            null,
            new pix_icon('t/message', '')
        );

        $node->showinflatnavigation = true;

        $root->add_node($node);
    }
}

