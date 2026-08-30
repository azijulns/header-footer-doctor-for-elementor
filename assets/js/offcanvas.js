/**
 * HeaderFooterFlow for Elementor — off-canvas mobile menu.
 *
 * Opens on any element matching the configured trigger selector, closes on the
 * close button, the overlay, or Escape. Keeps focus inside the panel while it
 * is open and restores it on close. No jQuery dependency.
 */
( function () {
	'use strict';

	var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])';

	var panel = null;
	var overlay = null;
	var lastFocused = null;

	/**
	 * Reflect the panel state on every trigger that declares aria-expanded.
	 *
	 * @param {boolean} expanded Whether the panel is open.
	 */
	function setExpanded( expanded ) {
		var triggers = document.querySelectorAll( '[aria-controls="hfflow-offcanvas"][aria-expanded]' );
		var i;

		for ( i = 0; i < triggers.length; i++ ) {
			triggers[ i ].setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
		}
	}

	/**
	 * Open the off-canvas panel.
	 */
	function open() {
		if ( ! panel || panel.classList.contains( 'is-open' ) ) {
			return;
		}

		lastFocused = document.activeElement;

		panel.hidden = false;

		if ( overlay ) {
			overlay.hidden = false;
		}

		// Force a reflow so the transition runs from the closed position.
		void panel.offsetWidth;

		panel.classList.add( 'is-open' );
		document.body.classList.add( 'hfflow-offcanvas-active' );
		setExpanded( true );

		var first = panel.querySelector( FOCUSABLE );

		if ( first ) {
			first.focus();
		}
	}

	/**
	 * Close the off-canvas panel.
	 */
	function close() {
		if ( ! panel || ! panel.classList.contains( 'is-open' ) ) {
			return;
		}

		panel.classList.remove( 'is-open' );
		document.body.classList.remove( 'hfflow-offcanvas-active' );
		setExpanded( false );

		if ( overlay ) {
			overlay.hidden = true;
		}

		window.setTimeout( function () {
			if ( ! panel.classList.contains( 'is-open' ) ) {
				panel.hidden = true;
			}
		}, 300 );

		if ( lastFocused && typeof lastFocused.focus === 'function' ) {
			lastFocused.focus();
		}
	}

	/**
	 * Keep Tab focus cycling inside the open panel.
	 *
	 * @param {KeyboardEvent} event The keydown event.
	 */
	function trapFocus( event ) {
		var items = panel.querySelectorAll( FOCUSABLE );

		if ( ! items.length ) {
			return;
		}

		var first = items[ 0 ];
		var last = items[ items.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		panel = document.getElementById( 'hfflow-offcanvas' );

		if ( ! panel ) {
			return;
		}

		overlay = document.querySelector( '.hfflow-offcanvas__overlay' );

		var selector = ( window.hfflowOffcanvas && window.hfflowOffcanvas.trigger ) || '';

		// Delegated so triggers rendered later by Elementor still work.
		document.addEventListener( 'click', function ( event ) {
			if ( selector && event.target.closest( selector ) ) {
				event.preventDefault();
				open();

				return;
			}

			if ( event.target.closest( '.hfflow-offcanvas__close' ) ) {
				event.preventDefault();
				close();

				return;
			}

			if ( overlay && event.target === overlay ) {
				close();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( ! panel.classList.contains( 'is-open' ) ) {
				return;
			}

			if ( event.key === 'Escape' ) {
				close();
			} else if ( event.key === 'Tab' ) {
				trapFocus( event );
			}
		} );
	} );
} )();
