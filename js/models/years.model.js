define([
    "jquery",
    "underscore",
    "backbone"
],
    function($, _, Backbone) {

        var Years = Backbone.Model.extend({

            defaults : {

                from : null,
                to : null
            },

            constructor : function(attributes, options) {

                if ( options && options.parent ) {

                    this.parent = options.parent;
                }

                Backbone.Model.apply(this,arguments);
            },

            initialize : function() {

                this.listenTo(this,"change",this._notifyParent);
            },

            _notifyParent : function() {

                if ( this.parent && this.parent.trigger ) {

                    this.parent.trigger("change:year",this);
                }
            }
        });

        return Years;
    });