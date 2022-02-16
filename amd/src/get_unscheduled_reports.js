define(
    [
      'jquery',
      'core/ajax',
      'core/modal_factory',
      'core/modal_events'
    ],
    function($, ajax, ModalFactory, ModalEvents) {
        /* eslint no-console: ["error", { allow: ["warn", "error"] }] */

        /**
         * Callback for modals
         * @param {Event} e
         */
        function modalcallback(e) {
          e.preventDefault();
          location.reload(true);
        }

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
                        body: modalbody
                      })
                      .done(function(modal) {
                        modal.show();
                        modal.getRoot().on(ModalEvents.hidden, modalcallback);
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
                      body: modalbody
                    })
                    .done(function(modal) {
                      modal.show();
                      modal.getRoot().on(ModalEvents.hidden, modalcallback);
                    });
                  });

                });
              });
            }
        };
      });
