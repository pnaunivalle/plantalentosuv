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
 * Send files to Google Drive class
 *
 * @package    local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_plantalentosuv;

defined('MOODLE_INTERNAL') || die;

// All Google API classes support autoload with this.
require_once($CFG->dirroot . '/local/plantalentosuv/googleapi/vendor/autoload.php');

/**
 * Grade report manager class
 *
 * @package   local_plantalentosuv
 * @copyright 2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_files_google_drive {

    /**
     * upload_file
     *
     * @param  mixed $filename
     * @param  mixed $mimetype
     * @param  mixed $filecontent
     * @param  mixed $description
     * @return void
     */
    public function upload_file ($filename, $mimetype, $filecontent, $description) {

        global $CFG;

        // Credential variables.
        $jsonkey = get_config('local_plantalentosuv', 'jsonkey');
        $jsonpath = $CFG->dirroot.'/local/plantalentosuv/'.get_config('local_plantalentosuv', 'jsonpath');

        $client = new \Google_Client();
        $client->setAuthConfig($jsonpath);
        $client->addScope("https://www.googleapis.com/auth/drive");

        $service = new \Google_Service_Drive($client);

        $drivefile = new \Google_Service_Drive_DriveFile();
        $drivefile->setName($filename);
        $drivefile->setMimeType($mimetype);
        $drivefile->setParents([$jsonkey]);
        $drivefile->setDescription($description);

        $result = $service->files->create(
            $drivefile,
            array('data' => $filecontent,
                  'mimeType' => $mimetype,
                  'uploadType' => 'media')
        );

        return $result;
    }
}
