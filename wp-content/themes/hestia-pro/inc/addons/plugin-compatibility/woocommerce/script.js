/* globals infiniteScroll */
( function( $ ) {

    /**
     * Loads new posts when users scroll near the bottom of the page.
     */
    var Scroller = function( settings ) {

        var self = this;

        // Initialize our variables
        this.id = settings.id;
        this.body = $( document.body );
        this.window = $( window );
        this.element = $( '#' + settings.id );
        this.ready = true;
        this.disabled = false;
        this.page = 2;
        this.throttle = false;
        this.pageCache = {};

        // On event the case becomes a fact
        this.window.bind( 'scroll.infinity', function() {
            self.throttle = true;
        } );

        setInterval( function() {
            if ( self.throttle ) {
                // Once the case is the case, the action occurs and the fact is no more
                self.throttle = false;
                // Fire the refresh
                self.refresh();
                // self.determineURL(); // determine the url
            }
        }, 250 );
    };

    /**
     * Check whether we should fetch any additional posts.
     */
    Scroller.prototype.check = function() {
        var container = this.element.offset();

        // If the container can't be found, stop otherwise errors result
        if ( 'object' !== typeof container ) {
            return false;
        }

        var bottom = this.window.scrollTop() + this.window.height(),
            threshold = container.top + this.element.outerHeight( false ) - this.window.height() * 2;

        return bottom > threshold;
    };

    /**
     * Renders the results from a successful response.
     */
    Scroller.prototype.render = function( response ) {
        this.element.append( response.html );
        this.body.trigger( 'post-load', response );
        this.ready = true;
    };


    /**
     * Controls the flow of the refresh. Don't mess.
     */
    Scroller.prototype.refresh = function() {
        var self = this,
            jqxhr;

        if ( this.disabled || ! this.ready || ! this.check() ) {
            return;
        }

        this.ready = false;

        // Build ajax url
        var base_url = infiniteScroll.settings.base_url + 'page/' + self.page + '/';
        var url_args = JSON.parse( infiniteScroll.settings.url_args );
        var args_length = Object.keys(url_args).length;
        if( args_length > 0 ){
            base_url = base_url + '?';
        }
        var index = 1;
        Object.keys(url_args).forEach(function (key) {
            base_url = base_url + key + '=' + url_args[key];
            if( index !== args_length ){
                base_url = base_url + '&';
            }
            index++;
        });
        $(self.element).append('<div class="loading text-center"><i class="fa fa-3x fa-spin fa-spinner" aria-hidden="true"></i></div>');


        jqxhr = $.post( base_url );

        // Allow refreshes to occur again if an error is triggered.
        jqxhr.fail( function() {
            self.ready = true;
        } );

        // Success handler
        jqxhr.done( function( response ) {
            $(self.element).find('.loading').remove();
            // Check for and parse our response.
            if ( ! response || ! response.type ) {
                return;
            }

            // If we've succeeded...
            if ( response.type === 'success' ) {

                // stash the response in the page cache
                self.pageCache[ self.page + self.offset ] = response;

                // Increment the page number
                self.page++;

                // Render the results
                self.render.apply( self, arguments );

                if ( response.lastbatch ) {
                    self.disabled = true;
                }
            }
        } );

        return jqxhr;
    };

    $( document ).ready( function() {
        if ( 'object' !== typeof infiniteScroll ) {
            return;
        }
        infiniteScroll.scroller = new Scroller( infiniteScroll.settings );
    } );

} )( jQuery );
