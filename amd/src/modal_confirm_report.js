define(['jquery',
        'core/ajax',
        'core/modal_factory',
        'core/custom_interaction_events',
        'core/modal',
        'core/modal_events',
        'core/modal_registry'],
        function($, ajax, ModalFactory, CustomEvents, Modal, ModalEvents, ModalRegistry) {

    var registered = false;
    var SELECTORS = {
        ACCEPT_BUTTON: '[data-action="acept-generation"]',
        CANCEL_BUTTON: '[data-action="cancel-generation"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalConfirmReport = function(root) {
        Modal.call(this, root);

        if (!this.getFooter().find(SELECTORS.ACCEPT_BUTTON).length) {
            Notification.exception({message: 'No login button found'});
        }

        if (!this.getFooter().find(SELECTORS.CANCEL_BUTTON).length) {
            Notification.exception({message: 'No cancel button found'});
        }
    };

    ModalConfirmReport.TYPE = 'local_plantalentosuv-modal_confirm_report';

    ModalConfirmReport.prototype = Object.create(Modal.prototype);
    ModalConfirmReport.prototype.constructor = ModalConfirmReport;

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
     ModalConfirmReport.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        this.getModal().on(CustomEvents.events.activate, SELECTORS.CANCEL_BUTTON, function(e, data) {
            var cancelEvent = $.Event(ModalEvents.cancel);
            this.getRoot().trigger(cancelEvent, this);

            if (!cancelEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        }.bind(this));
    };

    // Automatically register with the modal registry the first time this module is imported so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(ModalConfirmReport.TYPE, ModalConfirmReport, 'local_plantalentosuv/modal_confirm_report');
        registered = true;
    }

    return ModalConfirmReport;
});