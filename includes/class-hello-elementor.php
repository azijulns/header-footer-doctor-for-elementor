<?php
/**
 * Hello Elementor theme integration.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 */

namespace HFDoctor;

defined( 'ABSPATH' ) || exit;

/**
 * Swaps the Hello Elementor theme header and footer for plugin templates.
 *
 * The theme's own `header.php` / `footer.php` are still executed (so any code
 * they hook into keeps running) but their output is discarded, and the plugin
 * templates are printed instead. The swap only happens when a template
 * actually matches the current request, so an unconfigured plugin never
 * removes the theme chrome.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 * @see     Templates::resolve()
 * @internal
 */
class Hello_Elementor {

	/**
	 * Template registry used to decide whether an override applies.
	 *
	 * @var Templates
	 */
	private $templates;

	/**
	 * Hook into the theme's header and footer loading.
	 *
	 * @since 1.0.0
	 *
	 * @param Templates $templates Template registry.
	 */
	public function __construct( Templates $templates ) {
		$this->templates = $templates;

		add_action( 'get_header', array( $this, 'override_header' ), 0 );
		add_action( 'get_footer', array( $this, 'override_footer' ), 0 );
	}

	/**
	 * Print the plugin header and swallow the theme header.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function override_header(): void {
		if ( ! $this->templates->has_template( 'header' ) ) {
			return;
		}

		require HFDOCTOR_DIR . 'includes/templates/header.php';

		// The plugin template already ran wp_head(); stop the theme repeating it.
		remove_all_actions( 'wp_head' );

		$this->discard_theme_template( 'header.php' );
	}

	/**
	 * Print the plugin footer and swallow the theme footer.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function override_footer(): void {
		if ( ! $this->templates->has_template( 'footer' ) ) {
			return;
		}

		require HFDOCTOR_DIR . 'includes/templates/footer.php';

		// The plugin template already ran wp_footer(); stop the theme repeating it.
		remove_all_actions( 'wp_footer' );

		$this->discard_theme_template( 'footer.php' );
	}

	/**
	 * Execute a theme template while throwing away everything it prints.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template Theme-relative template file name.
	 * @return void
	 * @internal
	 */
	private function discard_theme_template( string $template ): void {
		ob_start();
		locate_template( array( $template ), true, false );
		ob_end_clean();
	}
}
