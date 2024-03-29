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

    global $DB;

    $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');

    if ($categoryidnumber != 0) {
        // Validate params.
        $category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);

        $categorycontext = context_coursecat::instance($category->id);

        $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', context_system::instance());
        $hasviewreportaccess = has_capability('local/plantalentosuv:viewreport', $categorycontext);

        if (!$hasmaintenanceaccess
            && get_config('local_plantalentosuv', 'showinnavigation')
            && $hasviewreportaccess) {

            $node = navigation_node::create(
                get_string('pluginname', 'local_plantalentosuv'),
                new moodle_url('/local/plantalentosuv/index.php'),
                navigation_node::TYPE_SYSTEM,
                'plantalentosuv',
                'plantalentosuv',
                new pix_icon('i/report', 'local_plantalentosuv')
            );

            $node->showinflatnavigation = true;

            $root->add_node($node, 'calendar');
        }
    }
}

/**
 * Serves the forum attachments. Implements needed access control ;)
 *
 * @package  local_plantalentosuv
 * @category files
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function local_plantalentosuv_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {

    global $DB;

    require_login(null, false);

    if (!($context->contextlevel == CONTEXT_SYSTEM || $context->contextlevel == CONTEXT_COURSECAT)) {
        return false;
    }

    if ($filearea !== 'plantalentosuvarea') {
        return false;
    }

    $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');

    $category = $DB->get_record('course_categories', array('idnumber' => $categoryidnumber), '*', MUST_EXIST);
    $categorycontext = context_coursecat::instance($category->id);

    if (!has_capability('local/plantalentosuv:viewreport', $categorycontext)) {
        return false;
    }

    $itemid = 0;

    $filename = array_pop($args);
    $filepath = '/';

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_plantalentosuv', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Returns table html with files in plugin filearea
 *
 * @param stored_file[] $files
 * @return string $htmlfiles
 */
function local_plantalentosuv_list_files_html($files) {

    $table = new html_table();
    $table->id = 'table-files-ptuv';
    $table->head = array(
        get_string('filename', 'local_plantalentosuv'),
        get_string('filesize', 'local_plantalentosuv'),
        get_string('filetype', 'local_plantalentosuv')
    );

    $table->data = array();

    foreach ($files as $file) {

        $filename = new html_table_cell($file->get_filename());
        $filesize = new html_table_cell(strval(round($file->get_filesize() / 1000, 2))." kB");
        $filemimetype = new html_table_cell($file->get_mimetype());

        $row = new html_table_row(array($filename, $filesize, $filemimetype));

        $table->data[] = $row;
    }

    $htmlwriter = new html_writer();
    $htmltable = $htmlwriter->table($table);

    return $htmltable;
}

/**
 * Returns the plugin setting status
 *
 * @return array $settingstatus
 */
function local_platalentosuv_validate_settings() {

    $settingstatus = array();

    $categoryidnumber = get_config('local_plantalentosuv', 'categorytotrack');
    $cohortidnumber = get_config('local_plantalentosuv', 'categorytotrack');

    $uploadtogoogledrive = get_config('local_plantalentosuv', 'uploadtogoogledrive');
    $jsonkey = get_config('local_plantalentosuv', 'jsonkey');
    $jsonpath = get_config('local_plantalentosuv', 'jsonpath');

    $uploadtoexternalserver = get_config('local_plantalentosuv', 'uploadtoexternalserver');
    $ftpusername = get_config('local_plantalentosuv', 'ftpusername');
    $ftpport = get_config('local_plantalentosuv', 'ftpport');
    $ftpserver = get_config('local_plantalentosuv', 'ftpserver');
    $ftppassword = get_config('local_plantalentosuv', 'ftppassword');

    if (!$categoryidnumber || !$cohortidnumber) {
        $settingstatus['main'] = get_string('no_main_settings', 'local_plantalentosuv');
    }

    if (!$uploadtogoogledrive || !$jsonkey || !$jsonpath) {
        $settingstatus['googledrive'] = get_string('no_googledrive_settings', 'local_plantalentosuv');
    }

    if (!$uploadtoexternalserver || !$ftpusername || !$ftpport || !$ftpserver || !$ftppassword ) {
        $settingstatus['ftp'] = get_string('no_ftp_settings', 'local_plantalentosuv');
    }

    return $settingstatus;
}

