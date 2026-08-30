/**
 * HeaderFooterFlow for Elementor — template edit screen behaviour.
 *
 * Shows the page picker only for the "Specific pages" rule and provides a
 * client-side filter over the page list. No external dependencies.
 */
( function () {
	'use strict';

	/**
	 * Toggle the page picker to match the selected rule type.
	 *
	 * @param {HTMLSelectElement} select The rule type select.
	 * @param {HTMLElement}       picker The page picker container.
	 */
	function syncVisibility( select, picker ) {
		picker.hidden = select.value !== 'specific';
	}

	/**
	 * Filter the page list against the search field.
	 *
	 * @param {HTMLElement} picker The page picker container.
	 */
	function filterPages( picker ) {
		var search = picker.querySelector( '.hfflow-pagepicker__search' );
		var items = picker.querySelectorAll( '.hfflow-pagepicker__item' );
		var empty = picker.querySelector( '.hfflow-pagepicker__empty' );
		var needle = search ? search.value.trim().toLowerCase() : '';
		var visible = 0;

		Array.prototype.forEach.call( items, function ( item ) {
			var label = item.textContent.trim().toLowerCase();
			var match = needle === '' || label.indexOf( needle ) !== -1;

			item.hidden = ! match;

			if ( match ) {
				visible++;
			}
		} );

		if ( empty ) {
			empty.hidden = visible !== 0;
		}
	}

	/**
	 * Refresh the "n selected" counter.
	 *
	 * @param {HTMLElement} picker The page picker container.
	 */
	function updateCount( picker ) {
		var counter = picker.querySelector( '.hfflow-pagepicker__count' );

		if ( counter ) {
			counter.textContent = String(
				picker.querySelectorAll( 'input[type="checkbox"]:checked' ).length
			);
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var select = document.querySelector( '.hfflow-rule-type' );
		var picker = document.querySelector( '.hfflow-pagepicker' );

		if ( ! select || ! picker ) {
			return;
		}

		syncVisibility( select, picker );

		select.addEventListener( 'change', function () {
			syncVisibility( select, picker );
		} );

		var search = picker.querySelector( '.hfflow-pagepicker__search' );

		if ( search ) {
			search.addEventListener( 'input', function () {
				filterPages( picker );
			} );

			// Stop Enter in the filter field from submitting the post form.
			search.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Enter' ) {
					event.preventDefault();
				}
			} );
		}

		picker.addEventListener( 'change', function ( event ) {
			if ( event.target && event.target.type === 'checkbox' ) {
				updateCount( picker );
			}
		} );
	} );
} )();
