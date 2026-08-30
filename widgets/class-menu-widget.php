<?php
/**
 * Elementor navigation menu widget.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 */

namespace HFFlow\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Outputs a chosen WordPress navigation menu inside an Elementor layout.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 * @extends \Elementor\Widget_Base
 * @api
 */
class Menu_Widget extends Widget_Base {

	/**
	 * Widget slug used by Elementor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'hfflow-menu';
	}

	/**
	 * Widget label shown in the Elementor panel.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Nav Menu (Flow)', 'headerfooterflow-for-elementor' );
	}

	/**
	 * Panel icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-menu-bar';
	}

	/**
	 * Elementor categories this widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return list<string>
	 */
	public function get_categories(): array {
		return array( 'basic' );
	}

	/**
	 * Search keywords for the Elementor panel.
	 *
	 * @since 1.0.0
	 *
	 * @return list<string>
	 */
	public function get_keywords(): array {
		return array( 'menu', 'nav', 'navigation', 'header', 'footer' );
	}

	/**
	 * Stylesheet handles this widget depends on.
	 *
	 * @since 1.1.0
	 *
	 * @return list<string>
	 */
	public function get_style_depends(): array {
		return array( 'hfflow-menu' );
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_item_style_controls();
		$this->register_dropdown_style_controls();
	}

	/**
	 * Content tab: which menu to show and how it is laid out.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'hfflow_menu_section',
			array(
				'label' => esc_html__( 'Menu', 'headerfooterflow-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$menus = $this->get_menu_options();

		if ( empty( $menus ) ) {
			$this->add_control(
				'hfflow_no_menus',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						'<strong>%1$s</strong><br>%2$s',
						esc_html__( 'No menus found.', 'headerfooterflow-for-elementor' ),
						sprintf(
							/* translators: %s: link to the WordPress menus screen. */
							esc_html__( 'Create one on the %s screen first.', 'headerfooterflow-for-elementor' ),
							'<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '" target="_blank">' .
								esc_html__( 'Menus', 'headerfooterflow-for-elementor' ) . '</a>'
						)
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		} else {
			$this->add_control(
				'hfflow_menu_id',
				array(
					'label'   => esc_html__( 'Select Menu', 'headerfooterflow-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => (string) array_key_first( $menus ),
				)
			);

			$this->add_control(
				'hfflow_menu_depth',
				array(
					'label'   => esc_html__( 'Maximum Depth', 'headerfooterflow-for-elementor' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 10,
					'step'    => 1,
					'default' => 3,
				)
			);
		}

		$this->add_control(
			'hfflow_menu_layout',
			array(
				'label'        => esc_html__( 'Layout', 'headerfooterflow-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'horizontal',
				'options'      => array(
					'horizontal' => esc_html__( 'Horizontal', 'headerfooterflow-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'headerfooterflow-for-elementor' ),
				),
				'prefix_class' => 'hfflow-menu-layout-',
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'hfflow_menu_align',
			array(
				'label'     => esc_html__( 'Alignment', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'    => array(
						'title' => esc_html__( 'Left', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'        => array(
						'title' => esc_html__( 'Center', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'      => array(
						'title' => esc_html__( 'Right', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
					'space-between' => array(
						'title' => esc_html__( 'Stretch', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}}.hfflow-menu-layout-horizontal .hfflow-menu' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}}.hfflow-menu-layout-vertical .hfflow-menu'   => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_menu_arrows',
			array(
				'label'        => esc_html__( 'Submenu Arrows', 'headerfooterflow-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'headerfooterflow-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'headerfooterflow-for-elementor' ),
				'default'      => 'yes',
				'return_value' => 'yes',
				'selectors'    => array(
					'{{WRAPPER}} .hfflow-menu .menu-item-has-children > a::after' => 'display: block;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: top-level menu items.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_item_style_controls(): void {
		$this->start_controls_section(
			'hfflow_menu_items_style',
			array(
				'label' => esc_html__( 'Menu Items', 'headerfooterflow-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hfflow_menu_typography',
				'selector' => '{{WRAPPER}} .hfflow-menu > li > a',
			)
		);

		$this->start_controls_tabs( 'hfflow_menu_item_tabs' );

		$this->start_controls_tab(
			'hfflow_menu_item_normal',
			array( 'label' => esc_html__( 'Normal', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_menu_color',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu > li > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_menu_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu > li > a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_menu_item_hover',
			array( 'label' => esc_html__( 'Hover', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_menu_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu > li > a:hover, {{WRAPPER}} .hfflow-menu > li > a:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_menu_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu > li > a:hover, {{WRAPPER}} .hfflow-menu > li > a:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_menu_item_active',
			array( 'label' => esc_html__( 'Active', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_menu_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu > li.current-menu-item > a, {{WRAPPER}} .hfflow-menu > li.current-menu-ancestor > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_menu_bg_active',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu > li.current-menu-item > a, {{WRAPPER}} .hfflow-menu > li.current-menu-ancestor > a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'hfflow_menu_item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_menu_item_gap',
			array(
				'label'      => esc_html__( 'Space Between Items', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_menu_item_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hfflow_menu_item_border',
				'selector' => '{{WRAPPER}} .hfflow-menu > li > a',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: sub-menus.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_dropdown_style_controls(): void {
		$this->start_controls_section(
			'hfflow_menu_dropdown_style',
			array(
				'label' => esc_html__( 'Dropdown', 'headerfooterflow-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hfflow_menu_dropdown_typography',
				'selector' => '{{WRAPPER}} .hfflow-menu .sub-menu a',
			)
		);

		$this->start_controls_tabs( 'hfflow_menu_dropdown_tabs' );

		$this->start_controls_tab(
			'hfflow_menu_dropdown_normal',
			array( 'label' => esc_html__( 'Normal', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_menu_dropdown_color',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_menu_dropdown_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_menu_dropdown_hover',
			array( 'label' => esc_html__( 'Hover', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_menu_dropdown_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu a:hover, {{WRAPPER}} .hfflow-menu .sub-menu a:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_menu_dropdown_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu a:hover, {{WRAPPER}} .hfflow-menu .sub-menu a:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'hfflow_menu_dropdown_width',
			array(
				'label'      => esc_html__( 'Width', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'separator'  => 'before',
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 600,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 220,
				),
				'condition'  => array( 'hfflow_menu_layout' => 'horizontal' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_menu_dropdown_padding',
			array(
				'label'      => esc_html__( 'Box Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_menu_dropdown_item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'unit'     => 'px',
					'top'      => 8,
					'right'    => 12,
					'bottom'   => 8,
					'left'     => 12,
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_menu_dropdown_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-menu .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hfflow_menu_dropdown_border',
				'selector' => '{{WRAPPER}} .hfflow-menu .sub-menu',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'hfflow_menu_dropdown_shadow',
				'selector' => '{{WRAPPER}} .hfflow-menu .sub-menu',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build the menu options list for the select control.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int|string, string> Menu term ID => menu name.
	 * @internal
	 */
	private function get_menu_options(): array {
		$menus   = wp_get_nav_menus();
		$options = array();

		if ( is_array( $menus ) ) {
			foreach ( $menus as $menu ) {
				$options[ (string) $menu->term_id ] = $menu->name;
			}
		}

		return $options;
	}

	/**
	 * Render the widget on the front end and in the editor preview.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$menu_id  = isset( $settings['hfflow_menu_id'] ) ? absint( $settings['hfflow_menu_id'] ) : 0;
		$depth    = isset( $settings['hfflow_menu_depth'] ) ? absint( $settings['hfflow_menu_depth'] ) : 3;
		$layout   = isset( $settings['hfflow_menu_layout'] ) ? (string) $settings['hfflow_menu_layout'] : 'horizontal';

		if ( ! $menu_id ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Select a menu in the widget settings.', 'headerfooterflow-for-elementor' ) . '</p>';
			}

			return;
		}

		wp_nav_menu(
			array(
				'menu'            => $menu_id,
				'container'       => 'nav',
				'container_class' => 'hfflow-nav hfflow-nav--' . ( 'vertical' === $layout ? 'vertical' : 'horizontal' ),
				'menu_class'      => 'hfflow-menu',
				'depth'           => max( 1, $depth ),
				'fallback_cb'     => false,
				'echo'            => true,
			)
		);
	}
}
