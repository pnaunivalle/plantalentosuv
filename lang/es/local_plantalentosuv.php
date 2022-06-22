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
 * Language file.
 *
 * @package   local_plantalentosuv
 * @copyright  2021 Plan Talentos Universidad del Valle
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Plan Talentos UV';
$string['plugindescription'] = 'Extensión desarrollada para el Plan Talentos de la Universidad del Valle.';
$string['header_plantalentosuv'] = 'Plan Talentos Universidad del Valle';

// Access.
$string['reports'] = 'Reportes para el Plan Talentos UV';

// Plugin settings.
$string['manage'] = 'Configuración para la extensión Plan Talentos Universidad del Valle';
$string['showinnavigation'] = 'Mostrar en la navegación';
$string['showinnavigation_desc'] = 'Cuando se habilita, la navegación del sitio mostrará un enlace a la extensión Plan Talentos UV';
$string['categorycoursestotrack'] = 'ID de la categoría';
$string['categorycoursestotrack_desc'] = 'ID de la categoría a monitorear.';
$string['cohorttotrack'] = 'Identificador de la cohorte';
$string['cohorttotrack_desc'] = 'Identificador de la cohorte a monitorear.';
$string['categorytotrack'] = 'Identificador de la categoría de cursos';
$string['categorytotrack_desc'] = 'Identificador de la categoría de cursos a monitorear';

// Capabilities.
$string['plantalentosuv:viewreport'] = 'Ver reporte';

// Tasks.
$string['get_report_plantalentosuv'] = 'Obtener reporte Plan Talentos UV';
$string['clean_plantalentosuv_filearea'] = 'Limpiar el área de archivos del plugin Plan Talentos UV';

// Index.
$string['download_attendance_report'] = 'Descargar reporte de asistencias';
$string['attendance_report_no_processed'] = 'El reporte de asistencias no ha sido procesado';
$string['download_grade_report'] = 'Descargar reporte de calificaciones';
$string['grades_report_no_processed'] = 'El reporte de calificaciones no ha sido procesado';
$string['counter_files'] = ' archivos son almacenados en el área de archivos del plugin.';
$string['info'] = 'Información';
$string['scheduled_reports'] = 'Reportes de ejecución programada';
$string['unscheduled_reports'] = 'Reportes de ejecución no programada';
$string['course_grade_items_report'] = 'Reporte de ítems de calificación en los cursos';
$string['course_attendance_sessions_report'] = 'Reporte de sesiones de asistencia en los cursos';
$string['generate_report'] = 'Generar reporte';
$string['download_report'] = 'Descargar reporte';
$string['filename'] = 'Nombre del archivo';
$string['filetype'] = 'Tipo';
$string['filesize'] = 'Tamaño';
$string['total_size_files_in_area'] = 'El total del tamaño de los archivos en el área del plugin es de ';
$string['no_main_settings'] = 'Las configuraciones principales del plugin no están definidas o están incompletas.';
$string['no_googledrive_settings'] = 'Las configuraciones de Google Drive del plugin no están definidas o están incompletas.';
$string['no_ftp_settings'] = 'Las configuraciones FTP del plugin no están definidas o están incompletas.';
$string['warnings'] = 'Advertencias';
$string['no_category_settings'] = 'Categoría de cursos no configurada. Revise las configuraciones del plugin.';

// Modals.
$string['title_confirm_report'] = 'Confirme la generación del reporte';
$string['text_confirm_report'] = '¿Usted desea generar este reporte nuevamente?';

// Settings.
$string['generalsettingsheading'] = 'Configuraciones generales';
$string['generalsettingsheading_desc'] = 'Configuraciones generales para el plugin';
$string['googleapiheading'] = 'Google API';
$string['googleapiheading_desc'] = 'Credenciales para la API de Google que permite cargar archivos en Google Drive';
$string['uploadtogoogledrive'] = 'Cargar archivos a Google Drive';
$string['uploadtogoogledrive_desc'] = 'Si está activo, se cargarán automáticamente los archivos de reportes a Google Drive';
$string['jsonkey'] = 'JSON key';
$string['jsonkey_desc'] = 'JSON key obtenida desde la aplicación de servicios de Google';
$string['jsonpath'] = 'JSON path';
$string['jsonpath_desc'] = 'JSON path obtenida desde la aplicación de servicios de Google';
$string['externalserverheading'] = 'Servidor externo';
$string['externalserverheading_desc'] = 'Configuraciones para acceso FTP a un servidor externo';
$string['uploadtoexternalserver'] = 'Cargar archivos a un servidor externo';
$string['uploadtoexternalserver_desc'] = 'Si está activo se cargan automáticamente los archivos a un servidor externo';
$string['ftpusername'] = 'Nombre de usuario FTP';
$string['ftpusername_desc'] = 'Digite el nombre de usuario FTP';
$string['ftpserver'] = 'Servidor FTP';
$string['ftpserver_desc'] = 'Servidor FTP';
$string['ftpport'] = 'Puerto FTP';
$string['ftpport_desc'] = 'Digite el puerto FTP';
$string['ftppassword'] = 'Contraseña FTP';
$string['ftppassword_desc'] = 'Digite la contraseña FTP';
