# Plugin Plan Talentos UV

El plugin Plan Talentos UV es un plugin para Moodle específicamente diseñado para el Campus Virtual de la Universidad del Valle. Este plugin permite obtener dos reportes de usuarios con el rol de estudiantes en una o varias cohortes definidas. Estos reportes son: Reporte de Calificaciones y Reporte de Asistencias.

## Algunas especificaciones técnicas

Este plugin fue desarrollado para Moodle en su versión 3.10. Las tecnologías para las cuales fue diseñado son:

- Moodle 3.10 o posterior
- PHP 7.4
- Postgres

Algunas dependecias y librerias:

- [Plugin Attendance](https://moodle.org/plugins/mod_attendance)
- [Google API PHP client v2.12.1](https://github.com/googleapis/google-api-php-client/releases)

## Instalación

### Descargando el paquete de instalación

- Descargar el archivo zip desde el [repositorio](https://github.com/pnaunivalle/plantalentosuv/releases).
- Descomprimir el archivo en la carpeta local ubicada en la raíz del sitio Moodle.
- Es necesario asegurarse que el nombre de la carpeta descomprimida es plantalentosuv y que la ruta completa de instalación es local/plantalentosuv.
- Ingresar a Moodle como administrador y dar clic en Notificaciones en el menú de administración del sitio.

### Via git/línea de comandos

- Entrar a la carpeta local ubicada en la raíz del sitio Moodle.
- Clonar el repositorio utilizando el siguiente comando `git clone https://github.com/pnaunivalle/plantalentosuv.git plantalentosuv`
- Ingresar a Moodle como administrador y dar clic en Notificaciones en el menú de administración del sitio.


## Especificaciones de los reportes retornados

El plugin obtiene los datos de calificaciones y asistencias de los estudiantes pertenecientes a una cohorte y que estén matriculados en los cursos de una categoría determinada. Además obtiene la estructura de ítems de calificación y los datos de las sesiones de asistencia configuradas en dichos cursos.

Los reportes retornados por el plugin son los siguientes:

- **Reporte de calificaciones:** Reporte de los estudiantes pertenecientes a una cohorte y matriculados en una categoría determinada.
- **Reporte de asistencias:** Reporte de los estudiantes pertenecientes a una cohorte y matriculados en una categoría determinada.
- **Reporte de ítems de calificación:** Estructura de los ítems de calificación de los cursos asociados a una categoría.
- **Reporte de sesiones de asistencia:** Datos de las sesiones de asistencia configuradas en los cursos de una categoría determinada.

##  Versiones

## Versión 2022021502 (v1.2.0-beta)
- Carga automática de archivos de reportes programados a servidor externo
- Configuraciones para cargar archivos a servidor externo
- Modificacion de la clase upload_files_google_drive

## Versión 2022021501 (v1.1.0-beta)
- Se añade servicio para recuperar las sesiones de asistencia por cada curso
- Se modifican consultas para retornar información de cursos de las categorías hijas
- Se añade servicio para recuperar la estructura de los ítems de calificación de cada curso
- Se añade validación para saber si los reportes programados han sido creados o no
- Se mejora la disposición de los iconos en el index
- Se añade interfaz gráfica para descargar reportes no programados
- Se corrige error en nombre de archivo del reporte de asistencias
- Se corrigen permisos de acceso al plugin desde diferentes roles
- Se corrige icono de acceso en la barra de navegación lateral

## Versión 2022020801 (v1.0.0-beta)
- Se incluye la librería Google API PHP client v2.12.1.
- Se añaden configuraciones del plugin para gestionar la carga de archivos a Google Drive.
- Clase y métodos para cargar archivos a Google Drive.

## Versión 2022012602 (v0.3.0-alpha)
- Tarea programada para limpiar el área de archivos del plugin.
- Clase y métodos para gestionar el reporte de calificaciones.
- Inclusión del reporte de calificaciones en la tarea programada para recuperar los reportes.

###  Versión 2021113000 (v0.2.0-alpha)
- Control de acceso.
- Se añaden permisos y capacidades.
- Se añaden configuraciones del plugin para gestionar la consulta de usuarios por cohorte y de cursos por categorías.
- Tarea programada para recuperar los reportes.

###  Versión 2021112600 (v0.1.0-alpha)
Desarrollo del esqueleto básico del plugin.
