define(
    [
      'jquery',
      'core/ajax',
      'core/modal_factory'
    ],
    function($, ajax, ModalFactory) {
        /* eslint no-console: ["error", { allow: ["warn", "error"] }] */

        return {
            init: function() {
              $(document).ready(function() {

                $('#generate-report-grade-items').on('click', function() {

                    var promiseGetItemsByCourse = ajax.call([{
                        methodname: 'local_plantalentosuv_get_grade_items_by_course',
                        args: {
                        }
                    }]);

                    promiseGetItemsByCourse[0].done(function(response) {

                      var modalbody = "";

                      if (response.result) {
                        modalbody = "Reporte generado éxitosamente.";
                      } else {
                        modalbody = "El reporte no fue generado.";
                      }

                      ModalFactory.create({
                        title: 'Éxito',
                        body: modalbody,
                        footer: '',
                      })
                      .done(function(modal) {
                        modal.show();
                      });
                    });
                });

                $('#generate-report-attendace-sessions').on('click', function() {

                  var promiseGetSessionsByCourse = ajax.call([{
                    methodname: 'local_plantalentosuv_get_course_attendance_sessions',
                    args: {
                    }
                  }]);

                  promiseGetSessionsByCourse[0].done(function(response) {

                    var modalbody = "";

                    if (response.result) {
                      modalbody = "Reporte generado éxitosamente.";
                    } else {
                      modalbody = "El reporte no fue generado.";
                    }

                    ModalFactory.create({
                      title: 'Éxito',
                      body: modalbody,
                      footer: '',
                    })
                    .done(function(modal) {
                      modal.show();
                    });
                  });

                });
              });
            }
        };
      });
