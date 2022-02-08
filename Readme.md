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

## Especificaciones de los reportes retornados

El plugin obtiene los datos de calificaciones y asistencias de los estudiantes pertenecientes a una cohorte y que estén matriculados en los cursos de una categoría determinada. Además obtiene la estructura de ítems de calificación y los datos de las sesiones de asistencia configuradas en dichos cursos.

Los reportes retornados por el plugin son los siguientes:

- ** Reporte de calificaciones: ** Reporte de los estudiantes pertenecientes a una cohorte y matriculados en una categoría determinada.
- ** Reporte de asistencias: ** Reporte de los estudiantes pertenecientes a una cohorte y matriculados en una categoría determinada.
- ** Reporte de ítems de calificación:  ** Estructura de los ítems de calificación de los cursos asociados a una categoría.
- ** Reporte de sesiones de asistencia: ** Datos de las sesiones de asistencia configuradas en los cursos de una categoría determinada.

##  Versiones

###  Versión 2021112600
Desarrollo del esqueleto básico del plugin

###  Versión 2021113000
Control de acceso. Se añaden permisos y capacidades.