/* global ua_vars */
( function ( $ ) {
	'use strict';

	// ── Tab navigation ─────────────────────────────────────────────────────

	$( document ).on( 'click', '.ua-tab-link', function ( e ) {
		e.preventDefault();

		var tabId = $( this ).data( 'tab' );

		$( '.ua-tab-link' ).removeClass( 'ua-tab-active' );
		$( this ).addClass( 'ua-tab-active' );

		$( '.ua-tab-panel' ).removeClass( 'ua-tab-active' );
		$( '#ua-tab-' + tabId ).addClass( 'ua-tab-active' );

		if ( history.pushState ) {
			history.pushState( null, '', $( this ).attr( 'href' ) );
		}
	} );

	// ── Avatar preview ─────────────────────────────────────────────────────

	$( document ).on( 'change', '#ua_avatar_file', function () {
		var file = this.files[ 0 ];
		if ( ! file ) { return; }

		var reader = new FileReader();
		reader.onload = function ( e ) {
			$( '.ua-avatar-preview' ).attr( 'src', e.target.result );
		};
		reader.readAsDataURL( file );

		// Clear any pending removal flag.
		$( '#ua-remove-avatar-flag' ).remove();
	} );

	// ── Remove avatar ──────────────────────────────────────────────────────

	$( document ).on( 'click', '#ua-remove-avatar', function () {
		if ( ! $( '#ua-remove-avatar-flag' ).length ) {
			$( '<input>' ).attr( {
				type:  'hidden',
				id:    'ua-remove-avatar-flag',
				name:  'remove_avatar',
				value: '1'
			} ).appendTo( '#ua-profile-form' );
		}

		$( '.ua-avatar-preview' ).attr( 'src', ua_vars.default_avatar );
		$( '#ua_avatar_file' ).val( '' );
		$( this ).hide();
	} );

	// ── Profile form ───────────────────────────────────────────────────────

	$( document ).on( 'submit', '#ua-profile-form', function ( e ) {
		e.preventDefault();

		var $form     = $( this );
		var $btn      = $form.find( '[type="submit"]' );
		var $feedback = $form.find( '.ua-form-feedback' );
		var formData  = new FormData( this );

		formData.append( 'action', 'ua_save_profile' );

		$btn.prop( 'disabled', true );
		clearFeedback( $feedback );

		$.ajax( {
			url:         ua_vars.ajax_url,
			type:        'POST',
			data:        formData,
			processData: false,
			contentType: false,
			success: function ( res ) {
				if ( res.success ) {
					showFeedback( $feedback, res.data.message, 'success' );

					if ( res.data.avatar_url ) {
						$( '.ua-avatar-preview' ).attr( 'src', res.data.avatar_url );
					}

					// Clean up removal flag after a successful save.
					$( '#ua-remove-avatar-flag' ).remove();
				} else {
					showFeedback( $feedback, res.data.message, 'error' );
				}
			},
			error: function () {
				showFeedback( $feedback, ua_vars.i18n.error, 'error' );
			},
			complete: function () {
				$btn.prop( 'disabled', false );
			}
		} );
	} );

	// ── Password form ──────────────────────────────────────────────────────

	$( document ).on( 'submit', '#ua-password-form', function ( e ) {
		e.preventDefault();

		var $form     = $( this );
		var $btn      = $form.find( '[type="submit"]' );
		var $feedback = $form.find( '.ua-form-feedback' );
		var data      = $form.serialize() + '&action=ua_change_password';

		$btn.prop( 'disabled', true );
		clearFeedback( $feedback );

		$.post( ua_vars.ajax_url, data )
			.done( function ( res ) {
				if ( res.success ) {
					showFeedback( $feedback, res.data.message, 'success' );
					$form[ 0 ].reset();
				} else {
					showFeedback( $feedback, res.data.message, 'error' );
				}
			} )
			.fail( function () {
				showFeedback( $feedback, ua_vars.i18n.error, 'error' );
			} )
			.always( function () {
				$btn.prop( 'disabled', false );
			} );
	} );

	// ── Helpers ────────────────────────────────────────────────────────────

	function showFeedback( $el, message, type ) {
		$el.removeClass( 'ua-success ua-error' )
		   .addClass( 'ua-' + type )
		   .text( message );
	}

	function clearFeedback( $el ) {
		$el.removeClass( 'ua-success ua-error' ).text( '' );
	}

} )( jQuery );
