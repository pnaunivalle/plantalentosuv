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
 * @return void
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

/**
 * Serves the forum attachments. Implements needed access control ;)
 *
 * @package  local_plantalentosuv
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function local_plantalentosuv_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    if ($filearea !== 'plantalentosuvarea') {
        return false;
    }

    require_login();

    if (!has_capability('local/plantalentosuv:viewreport', $context)) {
        return false;
    }

    $itemid = 0;

    $filename = array_pop($args);
    $filepath = '/';

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_plantalentosuv', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
