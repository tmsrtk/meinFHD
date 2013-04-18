/**
 * The following .js-File holds all needed Methods for displaying single user
 * edit forms via AJAX.
 */

var xhr, usercount;

var UsersEditAjax = {
    init : function( config ) {
        this.config = config;
        this.bindEvents();
        this.requestSearch(this.config.roleDropdown, this.config.searchInput);
    },

    bindEvents : function() {
        var self = this;
        this.config.roleDropdown.on( 'change', function() {
            self.requestSearch( $(this), self.config.searchInput, self.config.subviewtype );
        });
        this.config.searchInput.on( 'keyup', function() {
            self.requestSearch( self.config.roleDropdown, $(this), self.config.subviewtype );
        });
    },

    requestSearch : function( filter, searchbox, subviewtpe ) {

        var self = this;

        clearTimeout( self.timer );

        /*
         * fire the command after 400 ms, so when the user types a name in the searchbox
         * not for every letter a ajax request will be fired, but for the last chain
         */
        self.timer = setTimeout(function() {

            self.config.dataContent.html("lade Daten...");

            var data = '';

            // if filter not 0 = "Bitte auswaehlen" -> no role_id var
            ( filter.val() !== '0' ) ? data+='role_id='+filter.val()+'&' : data+='role_id=&';
            // more than two letters, typed in the searchbox
            ( searchbox.val().length > 2 ) ? data+='searchletter='+searchbox.val() : data+='searchletter=';

            // add the subviewtype at the end of the data that is passed to the view
            data += '&subviewtype=' + subviewtpe;

            /*
             * if the request was already sent, check if its still running
             * if so, abort it to prevent inserting the requested content, after
             * another request was sent and responsed/inserted
             */
            if ( xhr && xhr.readyState != 4 ) {
                xhr.abort();
            }

            xhr = $.get(
                self.config.site_url + "admin/ajax_show_user/",
                data,
                function(response) {
                    self.config.dataContent.html(response);
                }).done(function() {
                    $.get(
                        self.config.site_url + "admin/ajax_show_user_count/",
                        data,
                        function(response) {
                            self.config.counter.text(response);
                        });
                });

        }, 400);


    },

    requestBySearch : function( searchinput ) {
        var self = this;
        var url = self.config.site_url + "admin/ajax_show_user/";
        var data = '';

        // add the subviewtype to the data that is passed to the view
        data += '&subviewtype=' + subviewtpe;

        // for calling this method without parameter
        if (!searchinput) {
            data += 'role_id='+this.config.roleDropdown.val();

            if ( xhr && xhr.readyState != 4 ) {
                xhr.abort();
            }

            xhr = $.get(
                url,
                data,
                function(response) {
                    self.config.dataContent.html(response);
                });
        }
        else {
            clearTimeout( self.timer );

            if (searchinput.val().length == 0) { // load all users of selected std
                data += 'role_id='+this.config.roleDropdown.val();

                if ( xhr && xhr.readyState != 4 ) {
                    xhr.abort();
                }

                xhr = $.get(
                    url,
                    data,
                    function(response) {
                        self.config.dataContent.html(response);
                    });
            }
            else if (searchinput.val().length >= 2) { // start to search when when two letters were entered
                self.timer = setTimeout(function() {
                    data += 'searchletter='+searchinput.val()+'&role_id='+self.config.roleDropdown.val();

                    if ( xhr && xhr.readyState != 4 ) {
                        xhr.abort();
                    }

                    xhr = $.get(
                        url,
                        data,
                        function(response) {
                            self.config.dataContent.html(response);
                        }
                    );

                }, 400);
            }
        }
    }
};