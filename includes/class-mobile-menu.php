<?php
/**
 * Off-canvas mobile menu.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 */

namespace HFDoctor;

defined( 'ABSPATH' ) || exit;

/**
 * Renders an off-canvas panel that mirrors a chosen navigation menu.
 *
 * The panel is only printed when a menu is actually assigned to the
 * `hfdoctor_offcanvas` location, so the plugin adds no markup by default.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 * @api
 */
class Mobile_Menu {

	/**
	 * Theme location registered for the off-canvas menu.
	 *
	 * @var string
	 */
	public const LOCATION = 'hfdoctor_offcanvas';

	/**
	 * Register the menu location and front-end hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_menu_location' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_body_open', array( $this, 'render' ) );
	}

	/**
	 * Register the off-canvas navigation menu location.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function register_menu_location(): void {
		register_nav_menu(
			self::LOCATION,
			__( 'Header Footer Doctor — Off-canvas Menu', 'header-footer-doctor-for-elementor' )
		);
	}

	/**
	 * Whether the off-canvas panel should be rendered on this request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 * @api
	 */
	public function is_enabled(): bool {
		/**
		 * Filter whether the off-canvas mobile menu renders.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $enabled True when a menu is assigned to the plugin location.
		 */
		return (bool) apply_filters( 'hfdoctor_enable_offcanvas', has_nav_menu( self::LOCATION ) );
	}

	/**
	 * Enqueue the off-canvas stylesheet and script.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function enqueue_assets(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		wp_enqueue_style(
			'hfdoctor-offcanvas',
			HFDOCTOR_URL . 'assets/css/offcanvas.css',
			array(),
			Plugin::asset_version( 'assets/css/offcanvas.css' )
		);

		wp_enqueue_script(
			'hfdoctor-offcanvas',
			HFDOCTOR_URL . 'assets/js/offcanvas.js',
			array(),
			Plugin::asset_version( 'assets/js/offcanvas.js' ),
			true
		);

		/**
		 * Filter the CSS selector that opens the off-canvas panel.
		 *
		 * Defaults to the Elementor icon inside an element with the
		 * `hamburger` class, plus an explicit opt-in class and attribute.
		 *
		 * @since 1.0.0
		 *
		 * @param string $selector A CSS selector list.
		 */
		$trigger = (string) apply_filters(
			'hfdoctor_offcanvas_trigger_selector',
			'.hamburger .elementor-icon, .hfdoctor-offcanvas-open, [data-hfdoctor-offcanvas-open]'
		);

		wp_localize_script(
			'hfdoctor-offcanvas',
			'hfdoctorOffcanvas',
			array(
				'trigger' => $trigger,
			)
		);
	}

	/**
	 * Print the off-canvas panel markup.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function render(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		?>
		<div class="hfdoctor-offcanvas" id="hfdoctor-offcanvas" role="dialog" aria-modal="true"
			aria-label="<?php esc_attr_e( 'Mobile menu', 'header-footer-doctor-for-elementor' ); ?>" hidden>
			<div class="hfdoctor-offcanvas__bar">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					printf(
						'<span class="hfdoctor-offcanvas__title">%s</span>',
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
				<button type="button" class="hfdoctor-offcanvas__close"
					aria-label="<?php esc_attr_e( 'Close menu', 'header-footer-doctor-for-elementor' ); ?>">
					<svg width="40" height="40" viewBox="0 0 40 40" fill="none" aria-hidden="true" focusable="false"
						xmlns="http://www.w3.org/2000/svg">
						<circle cx="20" cy="20" r="19.5" stroke="currentColor" />
						<path d="M26 14L14 26M14 14L26 26" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</button>
			</div>

			<nav class="hfdoctor-offcanvas__nav"
				aria-label="<?php esc_attr_e( 'Mobile navigation', 'header-footer-doctor-for-elementor' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => self::LOCATION,
						'container'      => false,
						'menu_class'     => 'hfdoctor-offcanvas__menu',
						'depth'          => 3,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
		<div class="hfdoctor-offcanvas__overlay" hidden></div>
		<?php
	}
}
