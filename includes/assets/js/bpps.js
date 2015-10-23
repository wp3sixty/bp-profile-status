/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

( function( $ ) {

    window.BPPS = {
        init: function() {
            this.bppsCharacterLimit();
            this.bppsCurrentStatusEdit();
            this.bppsCurrentStatusEditCancel();
            this.bppsCurrentStatusDelete();
            this.bppsStatusDelete();
            this.bppsStatusEdit();
            this.bppsSetCurrentStatus();
        },
        bppsCharacterLimit: function() {
            $( '#bpps_add_new_status' ).on( 'keyup', function( e ) {
                var max = 140;
                var tval = $( this ).val( );
                var len = tval.length;
                var remain = parseInt( max - len );

                if( remain < 0 ) {
                    alert( 'You have reached max character limit. \n\nPlease revise it.!' );

                    remain++;

                    $( this ).val( ( tval ).substring( 0, max ) );
                }

                $( '.bpps-add-new span span' ).html( remain );
            } );
        },
        bppsCurrentStatusEdit: function() {
            $( '#bpps-current-status-edit' ).on( 'click', function( e ) {
                e.preventDefault();

                var bpps_current_status = $( this ).parent().find( '#bpps-current-status-org' ).val();

                $( '#bpps_add_new_status' ).val( bpps_current_status );
                $( '#bpps-eidt-status-org' ).val( bpps_current_status );
                $( '#bpps_add_new_status' ).focus();
                $( '#bpps_add_new_status' ).trigger( 'keyup' );
                $( '#bpps_add_new' ).addClass( 'bpps_hide' );
                $( '#bpps_add_new_and_set' ).addClass( 'bpps_hide' );
                $( '#bpps_update_status_and_set' ).val( 'Update' );
                $( '#bpps_update_status_and_set' ).removeClass( 'bpps_hide' );
                $( '#bpps_cancel' ).removeClass( 'bpps_hide' );
            } );
        },
        bppsCurrentStatusEditCancel: function() {
            $( '#bpps_cancel' ).on( 'click', function( e ) {
                e.preventDefault();

                $( '#bpps_add_new_status' ).val( '' );
                $( '#bpps_add_new_status' ).focusout();
                $( '#bpps_add_new_status' ).trigger( 'keyup' );
                $( '#bpps_update_status_and_set' ).addClass( 'bpps_hide' );
                $( '#bpps_update_status_and_set' ).val( 'Update & Set as Current' );
                $( '#bpps_update_status' ).addClass( 'bpps_hide' );
                $( '#bpps_cancel' ).addClass( 'bpps_hide' );
                $( '#bpps_add_new' ).removeClass( 'bpps_hide' );
                $( '#bpps_add_new_and_set' ).removeClass( 'bpps_hide' );
            } );
        },
        bppsCurrentStatusDelete: function() {
            $( '#bpps-current-status-delete' ).on( 'click', function( e ) {
                e.preventDefault();

                if( confirm( "Are you sure you want to delete current status?" ) ) {
                    var data = {
                        action: 'bpps_delete_current_status',
                        status: $( '#bpps-current-status-org' ).val( ),
                        nonce: $( '#bpps_delete_current_status_nonce' ).val()
                    };

                    $.ajax( {
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        success: function( response ) {
                            if( response == '1' ) {
                                alert( "Current status deleted successfully.!" );
                                $( '#bpps-current-status' ).html( "No current status is set yet." );
                            }
                        }
                    } );
                }
            } );
        },
        bppsStatusDelete: function() {
            $( '.bpps-status-delete' ).on( 'click', function( e ) {
                e.preventDefault();

                var that = this;

                if( confirm( "Are you sure you want to delete this status?" ) ) {
                    var data = {
                        action: 'bpps_delete_status',
                        status: $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).val( ),
                        nonce: $( '#bpps_delete_status_nonce' ).val()
                    };

                    $.ajax( {
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        success: function( response ) {
                            if( response == '1' ) {
                                alert( "Status deleted successfully.!" );

                                $( that ).parent().parent().remove();
                            }
                        }
                    } );
                }
            } );
        },
        bppsStatusEdit: function() {
            $( '.bpps-status-edit' ).on( 'click', function( e ) {
                e.preventDefault();

                var that = this;
                var bpps_status = $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).val();

                $( '#bpps-eidt-status-org' ).val( bpps_status );
                $( '#bpps_add_new_status' ).val( bpps_status );
                $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).val( bpps_status );
                $( '#bpps_add_new_status' ).focus();
                $( '#bpps_add_new_status' ).trigger( 'keyup' );
                $( '#bpps_add_new' ).addClass( 'bpps_hide' );
                $( '#bpps_add_new_and_set' ).addClass( 'bpps_hide' );
                $( '#bpps_update_status' ).removeClass( 'bpps_hide' );
                $( '#bpps_update_status_and_set' ).removeClass( 'bpps_hide' );
                $( '#bpps_cancel' ).removeClass( 'bpps_hide' );
            } );
        },
        bppsSetCurrentStatus: function() {
            $( '.bpps-set-status' ).on( 'click', function( e ) {
                e.preventDefault();

                var that = this;
                var data = {
                    action: 'bpps_set_current_status',
                    status: $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).val( )
                };

                $.ajax( {
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    success: function( response ) {
                        if( response == '1' ) {
                            alert( "Status set successfully.!" );

                            window.location.reload();
                        }
                    }
                } );
            } );
        }
    };

    $( document ).ready( function( ) {
        window.BPPS.init( );
    } );

} )( jQuery );
