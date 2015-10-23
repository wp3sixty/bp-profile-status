/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

( function( $ ) {

    window.BPPS = {
        init: function( ) {
            this.bppsCharacterLimit( );
            this.bppsCurrentStatusEdit( );
            this.bppsCurrentStatusEditCancel( );
        },
        bppsCharacterLimit: function( ) {
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
        bppsCurrentStatusEdit: function( ) {
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
                $( '#bpps_cancel' ).addClass( 'bpps_hide' );
                $( '#bpps_add_new' ).removeClass( 'bpps_hide' );
                $( '#bpps_add_new_and_set' ).removeClass( 'bpps_hide' );
            } );
        }
    };

    $( document ).ready( function( ) {
        window.BPPS.init( );
    } );

} )( jQuery );
