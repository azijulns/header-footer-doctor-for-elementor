<?php
/**
 * Plugin bootstrap: environment checks, i18n and module wiring.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 */

namespace HFDoctor;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin controller.
 *
 * Boots the plugin only when every requirement is satisfied (Elementor active,
 * Elementor recent enough, and the Hello Elementor theme in use). When a
 * requirement is missing the plugin stays inert and shows an admin notice
 * instead of fataling.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 * @api
 */
final class Plugin {

	/**
	 * Shared instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Template registry / renderer.
	 *
	 * @var Templates|null
	 */
	private $templates = null;

	/**
	 * Retrieve the shared instance, creating and booting it on first call.
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 * @api
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Private constructor; use {@see Plugin::instance()}.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of the singleton.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	private function __clone() {}

	/**
	 * Register the WordPress hooks needed to bring the plugin up.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	private function hooks(): void {
		// Core loads translations just-in-time from the Domain Path header.
		add_action( 'plugins_loaded', array( $this, 'boot' ) );
	}

	/**
	 * Boot the plugin modules once all plugins are loaded.
	 *
	 * Bails early (with an admin notice) when a requirement is unmet.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function boot(): void {
		if ( ! $this->requirements_met() ) {
			return;
		}

		require_once HFDOCTOR_DIR . 'includes/class-templates.php';
		require_once HFDOCTOR_DIR . 'includes/class-mobile-menu.php';
		require_once HFDOCTOR_DIR . 'includes/class-hello-elementor.php';
		require_once HFDOCTOR_DIR . 'widgets/class-widgets-manager.php';

		$this->templates = new Templates();

		new Mobile_Menu();
		new Hello_Elementor( $this->templates );
		new \HFDoctor\Widgets\Widgets_Manager();
	}

	/**
	 * Access the template registry.
	 *
	 * @since 1.0.0
	 *
	 * @return Templates|null Null when the plugin has not booted.
	 * @api
	 */
	public function templates(): ?Templates {
		return $this->templates;
	}

	/**
	 * Build a cache-busting version string for a bundled asset.
	 *
	 * The plugin version alone is not enough: editing a stylesheet without
	 * bumping the version leaves browsers serving the previous file under an
	 * unchanged `?ver=`, which looks exactly like a broken style control.
	 * Appending the file's modification time makes every edit a new URL, while
	 * staying stable across requests for a given deploy.
	 *
	 * @since 1.1.0
	 *
	 * @param  string $relative_path Path inside the plugin directory, e.g. `assets/css/menu.css`.
	 * @return string Version string for `wp_enqueue_style()` / `wp_enqueue_script()`.
	 * @api
	 */
	public static function asset_version( string $relative_path ): string {
		$file = HFDOCTOR_DIR . ltrim( $relative_path, '/\\' );

		$mtime = is_readable( $file ) ? filemtime( $file ) : false;

		return $mtime ? HFDOCTOR_VERSION . '.' . $mtime : HFDOCTOR_VERSION;
	}

	/**
	 * Determine whether every runtime requirement is satisfied.
	 *
	 * Queues an admin notice describing the first unmet requirement.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the plugin may boot.
	 * @internal
	 */
	private function requirements_met(): bool {
		if ( ! did_action( 'elementor/loaded' ) ) {
			$this->notice(
				sprintf(
					/* translators: %s: Elementor plugin name, wrapped in <strong>. */
					esc_html__( 'Header Footer Doctor requires %s to be installed and activated.', 'header-footer-doctor-for-elementor' ),
					'<strong>' . esc_html__( 'Elementor', 'header-footer-doctor-for-elementor' ) . '</strong>'
				)
			);

			return false;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, HFDOCTOR_MIN_ELEMENTOR_VERSION, '>=' ) ) {
			$this->notice(
				sprintf(
					/* translators: 1: Elementor plugin name, 2: minimum required Elementor version. */
					esc_html__( 'Header Footer Doctor requires %1$s version %2$s or greater.', 'header-footer-doctor-for-elementor' ),
					'<strong>' . esc_html__( 'Elementor', 'header-footer-doctor-for-elementor' ) . '</strong>',
					'<strong>' . esc_html( HFDOCTOR_MIN_ELEMENTOR_VERSION ) . '</strong>'
				)
			);

			return false;
		}

		if ( HFDOCTOR_SUPPORTED_THEME !== get_template() ) {
			$this->notice(
				sprintf(
					/* translators: %s: Hello Elementor theme name, wrapped in <strong>. */
					esc_html__( 'Header Footer Doctor is built for the %s theme. Activate that theme (or a child theme of it) to use the header and footer builder.', 'header-footer-doctor-for-elementor' ),
					'<strong>' . esc_html__( 'Hello Elementor', 'header-footer-doctor-for-elementor' ) . '</strong>'
				)
			);

			return false;
		}

		return true;
	}

	/**
	 * Queue a dismissible admin notice for users who can manage plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $message Notice body. May contain safe inline HTML.
	 * @return void
	 * @internal
	 */
	private function notice( string $message ): void {
		add_action(
			'admin_notices',
			static function () use ( $message ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				printf(
					'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
					wp_kses( $message, array( 'strong' => array() ) )
				);
			}
		);
	}
}
