define(
    [
      'jquery',
      'core/ajax',
      'core/modal_factory',
      'core/custom_interaction_events',
      'core/modal_events',
      'local_plantalentosuv/modal_confirm_report'
    ],
    function($, ajax, ModalFactory, CustomEvents, ModalEvents, ModalConfirmReport) {

      /**
       * Creates the modal for the course copy form
       *
       * @param {modalObject} modal
       */
      function reloadPage(modal) {
        modal.show();
        modal.getRoot().on(ModalEvents.hidden, location.reload(true));
      }

      return {
        init: function() {
          $(document).ready(function() {

            var triggerAttendanceReport = $('#generate-report-attendace-sessions');
            var triggerGradeReport = $('#generate-report-grade-items');

            // Modal to confirm the generation of the attendance session report.
            ModalFactory.create({
                type: ModalConfirmReport.TYPE,
                large: false,
                scrollable: false
            }, triggerAttendanceReport)
            .done(function(modal) {
              var SELECTORS = {
                ACCEPT_BUTTON: '[data-action="acept-generation"]',
                CANCEL_BUTTON: '[data-action="cancel-generation"]',
              };

              modal.getModal().on(CustomEvents.events.activate, SELECTORS.ACCEPT_BUTTON, function() {
                modal.hide();

                var promiseGetItemsByCourse = ajax.call([{
                    methodname: 'local_plantalentosuv_get_course_attendance_sessions',
                    args: {
                    }
                }]);

                promiseGetItemsByCourse[0].done(function() {
                  ModalFactory.create({
                    title: 'Éxito',
                    body: 'El reporte fue creado correctamente'
                  })
                  .done(reloadPage);
                });
              });
            });

            // Modal to confirm the generation of the items grade report.
            ModalFactory.create({
              type: ModalConfirmReport.TYPE,
              large: false,
              scrollable: false
            }, triggerGradeReport)
            .done(function(modal) {
              var SELECTORS = {
                ACCEPT_BUTTON: '[data-action="acept-generation"]',
                CANCEL_BUTTON: '[data-action="cancel-generation"]',
              };

              modal.getModal().on(CustomEvents.events.activate, SELECTORS.ACCEPT_BUTTON, function() {
                modal.hide();

                var promiseGetItemsByCourse = ajax.call([{
                    methodname: 'local_plantalentosuv_get_grade_items_by_course',
                    args: {
                    }
                }]);

                promiseGetItemsByCourse[0].done(function() {
                  ModalFactory.create({
                    title: 'Éxito',
                    body: 'El reporte fue creado correctamente'
                  })
                  .done(reloadPage);
                });
              });
            });
          });
        }
      };
    });
