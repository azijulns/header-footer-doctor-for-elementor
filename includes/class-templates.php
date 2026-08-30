<?php
/**
 * Header/footer template post type, meta boxes, matching and rendering.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 */

namespace HFFlow;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the template post type and resolves which template renders where.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 * @api
 */
class Templates {

	/**
	 * Custom post type name for header/footer templates.
	 *
	 * @var string
	 */
	public const POST_TYPE = 'hfflow_template';

	/**
	 * Post meta key holding the template type (`header` or `footer`).
	 *
	 * @var string
	 */
	public const META_TYPE = '_hfflow_template_type';

	/**
	 * Post meta key holding the display rules array.
	 *
	 * @var string
	 */
	public const META_RULES = '_hfflow_display_rules';

	/**
	 * Resolved templates, keyed by template type, memoised per request.
	 *
	 * @var array<string, \WP_Post|null>
	 */
	private $resolved = array();

	/**
	 * Wire up the post type, admin UI and front-end rendering hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'elementor/init', array( $this, 'enable_elementor_support' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'render_admin_column' ), 10, 2 );

		add_action( 'hfflow_header', array( $this, 'render_header' ) );
		add_action( 'hfflow_footer', array( $this, 'render_footer' ) );

		add_filter( 'wp_robots', array( $this, 'filter_robots' ), 20 );
		add_action( 'send_headers', array( $this, 'send_noindex_header' ) );
	}

	/**
	 * Register the header/footer template post type.
	 *
	 * The type is not public, but stays publicly queryable and REST enabled
	 * because the Elementor editor needs to load and preview it.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function register_post_type(): void {
		$labels = array(
			'name'               => __( 'Header/Footer', 'header-footer-doctor-for-elementor' ),
			'singular_name'      => __( 'Template', 'header-footer-doctor-for-elementor' ),
			'menu_name'          => __( 'HeaderFooterFlow', 'header-footer-doctor-for-elementor' ),
			'name_admin_bar'     => __( 'Template', 'header-footer-doctor-for-elementor' ),
			'add_new'            => __( 'Add New', 'header-footer-doctor-for-elementor' ),
			'add_new_item'       => __( 'Add New Template', 'header-footer-doctor-for-elementor' ),
			'new_item'           => __( 'New Template', 'header-footer-doctor-for-elementor' ),
			'edit_item'          => __( 'Edit Template', 'header-footer-doctor-for-elementor' ),
			'view_item'          => __( 'View Template', 'header-footer-doctor-for-elementor' ),
			'all_items'          => __( 'All Templates', 'header-footer-doctor-for-elementor' ),
			'search_items'       => __( 'Search Templates', 'header-footer-doctor-for-elementor' ),
			'not_found'          => __( 'No templates found.', 'header-footer-doctor-for-elementor' ),
			'not_found_in_trash' => __( 'No templates found in Trash.', 'header-footer-doctor-for-elementor' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'show_in_rest'        => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_icon'           => 'dashicons-layout',
				'menu_position'       => 26,
				'rewrite'             => false,
				'supports'            => array( 'title', 'editor', 'revisions' ),
				'capability_type'     => 'page',
			)
		);
	}

	/**
	 * Opt the template post type into the Elementor editor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function enable_elementor_support(): void {
		add_post_type_support( self::POST_TYPE, 'elementor' );
	}

	/* ---------------------------------------------------------------------
	 * Search engine visibility
	 * ------------------------------------------------------------------ */

	/**
	 * Mark template singulars as noindex/nofollow.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<string, bool|string> $robots Robots directives.
	 * @return array<string, bool|string>
	 * @internal
	 */
	public function filter_robots( array $robots ): array {
		if ( is_singular( self::POST_TYPE ) ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}

		return $robots;
	}

	/**
	 * Send an `X-Robots-Tag` header for template singulars.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function send_noindex_header(): void {
		if ( ! headers_sent() && is_singular( self::POST_TYPE ) ) {
			header( 'X-Robots-Tag: noindex, nofollow', true );
		}
	}

	/* ---------------------------------------------------------------------
	 * Meta boxes
	 * ------------------------------------------------------------------ */

	/**
	 * Register the "Template Type" and "Display Rules" meta boxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function register_meta_boxes(): void {
		add_meta_box(
			'hfflow_template_type',
			__( 'Template Type', 'header-footer-doctor-for-elementor' ),
			array( $this, 'render_type_meta_box' ),
			self::POST_TYPE,
			'side',
			'high'
		);

		add_meta_box(
			'hfflow_display_rules',
			__( 'Display Rules', 'header-footer-doctor-for-elementor' ),
			array( $this, 'render_rules_meta_box' ),
			self::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Render the template type selector.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_Post $post Post being edited.
	 * @return void
	 * @internal
	 */
	public function render_type_meta_box( \WP_Post $post ): void {
		wp_nonce_field( 'hfflow_save_meta', 'hfflow_meta_nonce' );

		$value = (string) get_post_meta( $post->ID, self::META_TYPE, true );
		?>
		<div class="hfflow-field">
			<label for="hfflow_template_type_field">
				<?php esc_html_e( 'This template is a', 'header-footer-doctor-for-elementor' ); ?>
			</label>
			<select name="hfflow_template_type" id="hfflow_template_type_field" class="widefat">
				<option value=""><?php esc_html_e( '— Select type —', 'header-footer-doctor-for-elementor' ); ?></option>
				<option value="header" <?php selected( $value, 'header' ); ?>>
					<?php esc_html_e( 'Header', 'header-footer-doctor-for-elementor' ); ?>
				</option>
				<option value="footer" <?php selected( $value, 'footer' ); ?>>
					<?php esc_html_e( 'Footer', 'header-footer-doctor-for-elementor' ); ?>
				</option>
			</select>
			<p class="description">
				<?php esc_html_e( 'Choose whether this template replaces the site header or the site footer.', 'header-footer-doctor-for-elementor' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the display rules controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  \WP_Post $post Post being edited.
	 * @return void
	 * @internal
	 */
	public function render_rules_meta_box( \WP_Post $post ): void {
		$rules    = $this->get_rules( $post->ID );
		$selected = $rules['pages'];

		$pages = get_posts(
			array(
				'post_type'              => 'page',
				'post_status'            => 'publish',
				'posts_per_page'         => 300,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		?>
		<div class="hfflow-field">
			<label for="hfflow_rule_type_field">
				<?php esc_html_e( 'Display on', 'header-footer-doctor-for-elementor' ); ?>
			</label>
			<select name="hfflow_rule_type" id="hfflow_rule_type_field" class="widefat hfflow-rule-type">
				<?php foreach ( $this->rule_type_labels() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $rules['type'], $key ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description">
				<?php esc_html_e( 'More specific rules win: specific pages beat homepage, which beats posts and pages, which beat global.', 'header-footer-doctor-for-elementor' ); ?>
			</p>
		</div>

		<div class="hfflow-pagepicker" <?php echo 'specific' === $rules['type'] ? '' : 'hidden'; ?>>
			<label for="hfflow-pagepicker-search">
				<?php esc_html_e( 'Select pages', 'header-footer-doctor-for-elementor' ); ?>
			</label>

			<input
				type="search"
				id="hfflow-pagepicker-search"
				class="hfflow-pagepicker__search widefat"
				placeholder="<?php esc_attr_e( 'Search pages…', 'header-footer-doctor-for-elementor' ); ?>"
				autocomplete="off"
			/>

			<?php if ( empty( $pages ) ) : ?>
				<p class="description"><?php esc_html_e( 'No published pages yet.', 'header-footer-doctor-for-elementor' ); ?></p>
			<?php else : ?>
				<ul class="hfflow-pagepicker__list">
					<?php foreach ( $pages as $page ) : ?>
						<li class="hfflow-pagepicker__item">
							<label>
								<input
									type="checkbox"
									name="hfflow_rule_pages[]"
									value="<?php echo esc_attr( (string) $page->ID ); ?>"
									<?php checked( in_array( (int) $page->ID, $selected, true ) ); ?>
								/>
								<span><?php echo esc_html( get_the_title( $page ) ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="hfflow-pagepicker__empty" hidden>
					<?php esc_html_e( 'No pages match your search.', 'header-footer-doctor-for-elementor' ); ?>
				</p>
				<p class="description">
					<span class="hfflow-pagepicker__count"><?php echo esc_html( (string) count( $selected ) ); ?></span>
					<?php esc_html_e( 'selected', 'header-footer-doctor-for-elementor' ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Human readable labels for each display rule type.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> Rule key => translated label.
	 * @internal
	 */
	private function rule_type_labels(): array {
		return array(
			'all'      => __( 'Entire site (global)', 'header-footer-doctor-for-elementor' ),
			'specific' => __( 'Specific pages', 'header-footer-doctor-for-elementor' ),
			'homepage' => __( 'Homepage only', 'header-footer-doctor-for-elementor' ),
			'posts'    => __( 'All single posts', 'header-footer-doctor-for-elementor' ),
			'pages'    => __( 'All pages', 'header-footer-doctor-for-elementor' ),
		);
	}

	/**
	 * Persist the template type and display rules.
	 *
	 * @since 1.0.0
	 *
	 * @param  int      $post_id Post being saved.
	 * @param  \WP_Post $post    Post object.
	 * @return void
	 * @internal
	 */
	public function save_meta( int $post_id, \WP_Post $post ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$nonce = isset( $_POST['hfflow_meta_nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['hfflow_meta_nonce'] ) )
			: '';

		if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'hfflow_save_meta' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$type = isset( $_POST['hfflow_template_type'] )
			? sanitize_key( wp_unslash( $_POST['hfflow_template_type'] ) )
			: '';

		if ( in_array( $type, array( 'header', 'footer' ), true ) ) {
			update_post_meta( $post_id, self::META_TYPE, $type );
		} else {
			delete_post_meta( $post_id, self::META_TYPE );
		}

		$rule_type = isset( $_POST['hfflow_rule_type'] )
			? sanitize_key( wp_unslash( $_POST['hfflow_rule_type'] ) )
			: 'all';

		if ( ! array_key_exists( $rule_type, $this->rule_type_labels() ) ) {
			$rule_type = 'all';
		}

		$pages = array();

		if ( isset( $_POST['hfflow_rule_pages'] ) && is_array( $_POST['hfflow_rule_pages'] ) ) {
			$raw   = array_map( 'absint', wp_unslash( $_POST['hfflow_rule_pages'] ) );
			$pages = array_values( array_unique( array_filter( $raw ) ) );
		}

		update_post_meta(
			$post_id,
			self::META_RULES,
			array(
				'type'  => $rule_type,
				'pages' => $pages,
			)
		);

		unset( $post );
	}

	/**
	 * Enqueue the meta box stylesheet and script on the template edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $hook_suffix Current admin page hook suffix.
	 * @return void
	 * @internal
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || self::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'hfflow-admin',
			HFFLOW_URL . 'assets/css/admin.css',
			array(),
			HFFLOW_VERSION
		);

		wp_enqueue_script(
			'hfflow-admin',
			HFFLOW_URL . 'assets/js/admin.js',
			array(),
			HFFLOW_VERSION,
			true
		);
	}

	/* ---------------------------------------------------------------------
	 * Admin list table columns
	 * ------------------------------------------------------------------ */

	/**
	 * Insert the "Type" and "Display On" columns after the title column.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<string, string> $columns Existing columns.
	 * @return array<string, string>
	 * @internal
	 */
	public function admin_columns( array $columns ): array {
		$merged = array();

		foreach ( $columns as $key => $label ) {
			$merged[ $key ] = $label;

			if ( 'title' === $key ) {
				$merged['hfflow_type']  = __( 'Type', 'header-footer-doctor-for-elementor' );
				$merged['hfflow_rules'] = __( 'Display On', 'header-footer-doctor-for-elementor' );
			}
		}

		return $merged;
	}

	/**
	 * Render a custom admin column cell.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $column  Column key.
	 * @param  int    $post_id Row post ID.
	 * @return void
	 * @internal
	 */
	public function render_admin_column( string $column, int $post_id ): void {
		if ( 'hfflow_type' === $column ) {
			$type = (string) get_post_meta( $post_id, self::META_TYPE, true );

			if ( 'header' === $type ) {
				echo '<strong>' . esc_html__( 'Header', 'header-footer-doctor-for-elementor' ) . '</strong>';
			} elseif ( 'footer' === $type ) {
				echo '<strong>' . esc_html__( 'Footer', 'header-footer-doctor-for-elementor' ) . '</strong>';
			} else {
				echo '&mdash;';
			}

			return;
		}

		if ( 'hfflow_rules' !== $column ) {
			return;
		}

		$rules  = $this->get_rules( $post_id );
		$labels = $this->rule_type_labels();

		if ( 'specific' !== $rules['type'] ) {
			echo esc_html( $labels[ $rules['type'] ] ?? $labels['all'] );

			return;
		}

		if ( empty( $rules['pages'] ) ) {
			echo esc_html__( 'Specific pages — none selected', 'header-footer-doctor-for-elementor' );

			return;
		}

		$titles = array();

		foreach ( array_slice( $rules['pages'], 0, 3 ) as $page_id ) {
			$title = get_the_title( $page_id );

			if ( '' !== $title ) {
				$titles[] = $title;
			}
		}

		$output    = implode( ', ', $titles );
		$remaining = count( $rules['pages'] ) - count( $titles );

		if ( $remaining > 0 ) {
			$output .= ' ' . sprintf(
				/* translators: %d: number of additional pages not listed. */
				_n( '(+%d more)', '(+%d more)', $remaining, 'header-footer-doctor-for-elementor' ),
				$remaining
			);
		}

		echo esc_html( $output );
	}

	/* ---------------------------------------------------------------------
	 * Front-end resolution and rendering
	 * ------------------------------------------------------------------ */

	/**
	 * Read and normalise the display rules stored on a template.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $post_id Template post ID.
	 * @return array{type: string, pages: list<int>}
	 * @internal
	 */
	private function get_rules( int $post_id ): array {
		$rules = get_post_meta( $post_id, self::META_RULES, true );

		if ( ! is_array( $rules ) ) {
			$rules = array();
		}

		$type = isset( $rules['type'] ) ? (string) $rules['type'] : 'all';

		if ( ! array_key_exists( $type, $this->rule_type_labels() ) ) {
			$type = 'all';
		}

		$pages = isset( $rules['pages'] ) && is_array( $rules['pages'] )
			? array_values( array_unique( array_filter( array_map( 'absint', $rules['pages'] ) ) ) )
			: array();

		return array(
			'type'  => $type,
			'pages' => $pages,
		);
	}

	/**
	 * Determine whether a template of the given type will render on this request.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type Either `header` or `footer`.
	 * @return bool
	 * @api
	 */
	public function has_template( string $type ): bool {
		return null !== $this->resolve( $type );
	}

	/**
	 * Resolve the winning template for the current request, memoised per type.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type Either `header` or `footer`.
	 * @return \WP_Post|null The matching template, or null when none applies.
	 * @api
	 */
	public function resolve( string $type ): ?\WP_Post {
		if ( array_key_exists( $type, $this->resolved ) ) {
			return $this->resolved[ $type ];
		}

		$this->resolved[ $type ] = null;

		if ( ! in_array( $type, array( 'header', 'footer' ), true ) ) {
			return null;
		}

		$templates = get_posts(
			array(
				'post_type'              => self::POST_TYPE,
				'post_status'            => 'publish',
				'posts_per_page'         => 50,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Bounded set of plugin-owned templates.
					array(
						'key'   => self::META_TYPE,
						'value' => $type,
					),
				),
			)
		);

		if ( empty( $templates ) ) {
			return null;
		}

		$best_priority = -1;

		foreach ( $templates as $template ) {
			$priority = $this->match_priority( $this->get_rules( $template->ID ) );

			if ( $priority > $best_priority ) {
				$best_priority           = $priority;
				$this->resolved[ $type ] = $template;
			}
		}

		return $this->resolved[ $type ];
	}

	/**
	 * Score a rule set against the current request.
	 *
	 * Higher is more specific. `-1` means the rule does not match at all.
	 *
	 * @since 1.0.0
	 *
	 * @param  array{type: string, pages: list<int>} $rules Normalised rules.
	 * @return int Match priority, or -1 when the rule does not apply.
	 * @internal
	 */
	private function match_priority( array $rules ): int {
		switch ( $rules['type'] ) {
			case 'specific':
				$current = $this->current_object_id();

				return ( $current && in_array( $current, $rules['pages'], true ) ) ? 100 : -1;

			case 'homepage':
				return ( is_front_page() || is_home() ) ? 80 : -1;

			case 'posts':
				return ( is_singular( 'post' ) ) ? 60 : -1;

			case 'pages':
				return ( is_page() ) ? 50 : -1;

			case 'all':
			default:
				return 10;
		}
	}

	/**
	 * Best-effort resolution of the object ID for the current request.
	 *
	 * `get_queried_object_id()` can legitimately return 0 while the header is
	 * being rendered on some routes, so a few fallbacks are applied.
	 *
	 * @since 1.0.0
	 *
	 * @return int Object ID, or 0 when it cannot be determined.
	 * @internal
	 */
	private function current_object_id(): int {
		$id = (int) get_queried_object_id();

		if ( ! $id && is_singular() ) {
			$id = (int) get_the_ID();
		}

		if ( ! $id && is_front_page() ) {
			$id = (int) get_option( 'page_on_front' );
		}

		if ( ! $id && is_home() ) {
			$id = (int) get_option( 'page_for_posts' );
		}

		return $id;
	}

	/**
	 * Print the resolved header template.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function render_header(): void {
		$this->render( 'header' );
	}

	/**
	 * Print the resolved footer template.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * @internal
	 */
	public function render_footer(): void {
		$this->render( 'footer' );
	}

	/**
	 * Print the Elementor-rendered content of the winning template.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type Either `header` or `footer`.
	 * @return void
	 * @internal
	 */
	private function render( string $type ): void {
		$template = $this->resolve( $type );

		if ( ! $template instanceof \WP_Post ) {
			return;
		}

		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance->frontend ) ) {
			return;
		}

		/*
		 * Elementor returns fully rendered, already-sanitised builder markup.
		 * Escaping it again would destroy the layout.
		 */
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
