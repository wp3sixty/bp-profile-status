/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function( $ ) {

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
				var tval = $( this ).val();
				var len = tval.length;
				var remain = parseInt( max - len );

				if ( remain < 0 ) {
					alert( bpps_main_js.i18n.bpps_max_character_alert );

					remain++;

					$( this ).val( ( tval ).substring( 0, max ) );
				}

				$( '.bpps-add-new span span' ).html( remain );
			} );
		},
		bppsCurrentStatusEdit: function() {
			$( '#bpps-current-status-edit' ).on( 'click', function( e ) {
				e.preventDefault();

				var bpps_current_status = $( '#bpps-current-status-org' ).val();

				$( '#bpps-current-status-text, #bpps-current-status-edit, #bpps-current-status-delete' ).addClass( 'bpps_hide' );
				$( '#bpps-current-status-direct-edit' ).removeClass( 'bpps_hide' );
				$( '#bpps-current-status-textarea' ).val( bpps_current_status );
				$( '#bpps-current-status-textarea' ).focus();
			} );
		},
		bppsCurrentStatusEditCancel: function() {
			$( '#bpps_cancel' ).on( 'click', function( e ) {
				e.preventDefault();

				$( '#bpps_add_new_status' ).val( '' );
				$( '#bpps_add_new_status' ).focusout();
				$( '#bpps_add_new_status' ).trigger( 'keyup' );
				$( '#bpps_update_status_and_set' ).addClass( 'bpps_hide' );
				$( '#bpps_update_status_and_set' ).val( bpps_main_js.i18n.bpps_update_and_set_as_current );
				$( '#bpps_update_status' ).addClass( 'bpps_hide' );
				$( '#bpps_cancel' ).addClass( 'bpps_hide' );
				$( '#bpps_add_new' ).removeClass( 'bpps_hide' );
				$( '#bpps_add_new_and_set' ).removeClass( 'bpps_hide' );
			} );

			$( '#bpps_cancel_update' ).on( 'click', function( e ) {
				$( '#bpps-current-status-text, #bpps-current-status-edit, #bpps-current-status-delete' ).removeClass( 'bpps_hide' );
				$( '#bpps-current-status-direct-edit' ).addClass( 'bpps_hide' );
			} );
		},
		bppsCurrentStatusDelete: function() {
			$( '#bpps-current-status-delete' ).on( 'click', function( e ) {
				e.preventDefault();

				if ( confirm( bpps_main_js.i18n.bpps_delete_current_status_confirm ) ) {
					var data = {
						action: 'bpps_delete_current_status',
						status: $( '#bpps-current-status-org' ).val(),
						nonce: $( '#bpps_delete_current_status_nonce' ).val()
					};

					$.ajax( {
						url: ajaxurl,
						type: 'post',
						data: data,
						success: function( response ) {
							if ( response.success ) {
								alert( bpps_main_js.i18n.bpps_delete_current_status_success );
								$( '#bpps-current-status' ).html( bpps_main_js.i18n.bpps_no_current_status_set );
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

				if ( confirm( bpps_main_js.i18n.bpps_delete_status_confirm ) ) {
					var data = {
						action: 'bpps_delete_status',
						status: $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).val(),
						nonce: $( '#bpps_delete_status_nonce' ).val()
					};

					$.ajax( {
						url: ajaxurl,
						type: 'post',
						data: data,
						success: function( response ) {
							if ( response.sucess ) {
								alert( bpps_main_js.i18n.bpps_status_delete_success );

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

				if ( $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).length > 0 ) {
					var status = $( that ).parent().siblings( 'td' ).children( '.bpps_old_status_org' ).val();
				} else if ( $( that ).siblings( '.bpps-status-org' ).length > 0 ) {
					var status = $( that ).siblings( '.bpps-status-org' ).val();
				} else if ( $( that ).siblings( '#bpps-current-status-org' ).length > 0 ) {
					var status = $( that ).siblings( '#bpps-current-status-org' ).val();
				}

				var data = {
					action: 'bpps_set_current_status',
					status: status,
					nonce: bpps_main_js.set_current_status_nonce
				};

				$.ajax( {
					url: ajaxurl,
					type: 'post',
					data: data,
					success: function( response ) {
						if ( response.success ) {
							alert( bpps_main_js.i18n.bpps_status_set_success );

							window.location = window.location;
						}
					}
				} );
			} );
		}
	};

	$( document ).ready( function() {
		window.BPPS.init();
	} );

})( jQuery );
