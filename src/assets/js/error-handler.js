var nfKnitPayExpressErrorHandler = Marionette.Object.extend({

    initialize: function() {
        this.listenTo( nfRadio.channel( 'form' ), 'render:view', this.initKnitPay );
    },


    /*
     * Initialize our error handler.
     * @since 3.0
     * @param layoutView  Backbone.View  The Form view state.
     */
    initKnitPay: function( layoutView ) {
        var formModel = layoutView.model;
        // Listen for submitted attempts.
        Backbone.Radio.channel( 'form-' + formModel.get( 'id' ) ).reply( 'maybe:submit', this.beforeSubmit, this, formModel );
    },


    /*
     * Function to handle submission attempts.
     * @since 3.0
     * @param formModel  Backbone.Model  The Form model.
     */
    beforeSubmit: function( formModel ) {
        var formID = formModel.get( 'id' );
        // Remove any errors so that submission can be attempted.
        // TODO: Potenitally need to search for additional slugs later. For now, all errors use the same slug.
        Backbone.Radio.channel( 'form-' + formID ).request( 'remove:error', 'pronamic-pay' );
        Backbone.Radio.channel( 'form-' + formID ).request( 'remove:error', 'pronamic-pay-gateway' );
    }

});

jQuery( document ).ready( function( $ ) {
    new nfKnitPayExpressErrorHandler();
});
