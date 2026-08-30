<?php
/**
 * Removes every trace of the plugin when it is deleted from WordPress.
 *
 * Runs on plugin deletion only — deactivating the plugin leaves data intact.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Post type created by this plugin.
 *
 * Redefined locally because the plugin bootstrap is not loaded during uninstall.
 *
 * @var string
 */
const HFDOCTOR_UNINSTALL_POST_TYPE = 'hfdoctor_template';

/**
 * Delete every template post (and its meta) on a single site.
 *
 * @since 1.0.0
 *
 * @return void
 */
function hfdoctor_uninstall_site(): void {
	$args = array(
		'post_type'              => HFDOCTOR_UNINSTALL_POST_TYPE,
		'post_status'            => 'any',
		'posts_per_page'         => 200,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	// Capped so a deletion blocked by another plugin cannot spin forever.
	for ( $batch = 0; $batch < 100; $batch++ ) {
		$query = new WP_Query( $args );

		if ( empty( $query->posts ) ) {
			return;
		}

		$deleted = 0;

		foreach ( $query->posts as $post_id ) {
			if ( wp_delete_post( (int) $post_id, true ) ) {
				++$deleted;
			}
		}

		// The same batch would be returned again; stop instead of looping.
		if ( 0 === $deleted ) {
			return;
		}
	}
}

if ( is_multisite() ) {
	$hfdoctor_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $hfdoctor_site_ids as $hfdoctor_site_id ) {
		switch_to_blog( (int) $hfdoctor_site_id );
		hfdoctor_uninstall_site();
		restore_current_blog();
	}

	unset( $hfdoctor_site_ids, $hfdoctor_site_id );
} else {
	hfdoctor_uninstall_site();
}
