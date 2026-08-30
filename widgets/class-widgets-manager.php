<?php
/**
 * Elementor widget registration.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 */

namespace HFFlow\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Registers this plugin's Elementor widgets and their stylesheets.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 * @internal
 */
class Widgets_Manager {

	/**
	 * Widget stylesheets, keyed by handle.
	 *
	 * Each is registered up front so widgets can declare it through
	 * `get_style_depends()` and Elementor only enqueues it on pages where the
	 * widget is actually used.
	 *
	 * @var array<string, string> Handle => file name inside `assets/css/`.
	 */
	private const STYLES = array(
		'hfflow-menu'      => 'menu.css',
		'hfflow-hamburger' => 'hamburger.css',
	);

	/**
	 * Hook into Elementor's widget and style registration.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ) );
	}

	/**
	 * Register each widget with Elementor.
	 *
	 * @since 1.0.0
	 *
	 * @param  \Elementor\Widgets_Manager $widgets_manager Elementor's widget registrar.
	 * @return void
	 * @internal
	 */
	public function register_widgets( $widgets_manager ): void {
		require_once HFFLOW_DIR . 'widgets/class-menu-widget.php';
		require_once HFFLOW_DIR . 'widgets/class-hamburger-widget.php';

		$widgets_manager->register( new Menu_Widget() );
		$widgets_manager->register( new Hamburger_Widget() );
	}

	/**
	 * Register the per-widget stylesheets.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	public function register_styles(): void {
		foreach ( self::STYLES as $handle => $file ) {
			wp_register_style(
				$handle,
				HFFLOW_URL . 'assets/css/' . $file,
				array(),
				\HFFlow\Plugin::asset_version( 'assets/css/' . $file )
			);
		}
	}
}
