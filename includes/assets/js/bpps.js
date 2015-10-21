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
            $( '#bpps_add_new_status' ).on( 'keypress', function( e ) {
                var max = 10;
                var tval = $( this ).val();
                var len = tval.length + 1;
                var remain = parseInt( max - len );

                if( remain <= 0 && e.which !== 0 && e.charCode !== 0 ) {
                    alert( 'You have reached max character limit. \n\nPlease revise it.!' );

                    $( this ).val( ( tval ).substring( 0, len - 1 ) );
                }

                $( '.bpps-add-new span span' ).html( remain );
            } );
        }
    };

    $( document ).ready( function() {
        window.BPPS.init();
    } );

} )( jQuery );
