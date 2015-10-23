/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

( function( $ ) {

    window.BPPS = {
        init: function() {
            this.bppsCharacterLimit();
        },
        bppsCharacterLimit: function() {
            $( '#bpps_add_new_status' ).on( 'keyup', function( e ) {
                var max = 140;
                var tval = $( this ).val();
                var len = tval.length;
                var remain = parseInt( max - len );

                if( remain < 0 ) {
                    alert( 'You have reached max character limit. \n\nPlease revise it.!' );

                    remain++;
                    $( this ).val( ( tval ).substring( 0, max ) );
                }

                $( '.bpps-add-new span span' ).html( remain );
            } );
        }
    };

    $( document ).ready( function() {
        window.BPPS.init();
    } );

} )( jQuery );
