<?php
/**
 * Elementor hamburger toggle widget.
 *
 * @package HeaderFooterFlow
 * @since   1.1.0
 */

namespace HFFlow\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use HFFlow\Mobile_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the button that opens the plugin's off-canvas mobile menu.
 *
 * The button is a plain `<button>` carrying the `data-hfflow-offcanvas-open`
 * attribute, which is part of the default trigger selector handled by
 * `assets/js/offcanvas.js`, so the widget ships no script of its own.
 *
 * @package HeaderFooterFlow
 * @since   1.1.0
 * @extends \Elementor\Widget_Base
 * @see     \HFFlow\Mobile_Menu
 * @api
 */
class Hamburger_Widget extends Widget_Base {

	/**
	 * Widget slug used by Elementor.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'hfflow-hamburger';
	}

	/**
	 * Widget label shown in the Elementor panel.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Mobile Hamburger (Flow)', 'headerfooterflow-for-elementor' );
	}

	/**
	 * Panel icon.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-menu-toggle';
	}

	/**
	 * Elementor categories this widget belongs to.
	 *
	 * @since 1.1.0
	 *
	 * @return list<string>
	 */
	public function get_categories(): array {
		return array( 'basic' );
	}

	/**
	 * Search keywords for the Elementor panel.
	 *
	 * @since 1.1.0
	 *
	 * @return list<string>
	 */
	public function get_keywords(): array {
		return array( 'hamburger', 'burger', 'toggle', 'mobile', 'menu', 'offcanvas', 'off-canvas', 'nav' );
	}

	/**
	 * Stylesheet handles this widget depends on.
	 *
	 * @since 1.1.0
	 *
	 * @return list<string>
	 */
	public function get_style_depends(): array {
		return array( 'hfflow-hamburger' );
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_button_style_controls();
		$this->register_lines_style_controls();
		$this->register_text_style_controls();
		$this->register_panel_style_controls();
		$this->register_panel_bar_style_controls();
		$this->register_panel_menu_style_controls();
	}

	/**
	 * Content tab: what the button looks like and where it shows.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_section',
			array(
				'label' => esc_html__( 'Hamburger', 'headerfooterflow-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( ! has_nav_menu( Mobile_Menu::LOCATION ) ) {
			$this->add_control(
				'hfflow_hamburger_no_menu',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						'<strong>%1$s</strong><br>%2$s',
						esc_html__( 'No off-canvas menu assigned.', 'headerfooterflow-for-elementor' ),
						sprintf(
							/* translators: %s: link to the WordPress menu locations screen. */
							esc_html__( 'Assign a menu to the off-canvas location on the %s screen, otherwise this button has no panel to open.', 'headerfooterflow-for-elementor' ),
							'<a href="' . esc_url( admin_url( 'nav-menus.php?action=locations' ) ) . '" target="_blank">' .
								esc_html__( 'Menus', 'headerfooterflow-for-elementor' ) . '</a>'
						)
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}

		$this->add_control(
			'hfflow_hamburger_type',
			array(
				'label'   => esc_html__( 'Style', 'headerfooterflow-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lines',
				'options' => array(
					'lines' => esc_html__( 'Lines', 'headerfooterflow-for-elementor' ),
					'icon'  => esc_html__( 'Icon', 'headerfooterflow-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_lines',
			array(
				'label'     => esc_html__( 'Lines', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'2' => esc_html__( 'Two', 'headerfooterflow-for-elementor' ),
					'3' => esc_html__( 'Three', 'headerfooterflow-for-elementor' ),
					'4' => esc_html__( 'Four', 'headerfooterflow-for-elementor' ),
				),
				'condition' => array( 'hfflow_hamburger_type' => 'lines' ),
			)
		);

		$this->add_control(
			'hfflow_hamburger_animation',
			array(
				'label'        => esc_html__( 'Animate To Close Icon', 'headerfooterflow-for-elementor' ),
				'description'  => esc_html__( 'Morphs the lines into a cross while the panel is open.', 'headerfooterflow-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'headerfooterflow-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'headerfooterflow-for-elementor' ),
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array(
					'hfflow_hamburger_type'  => 'lines',
					'hfflow_hamburger_lines' => '3',
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_icon',
			array(
				'label'     => esc_html__( 'Icon', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'eicon-menu-bar',
					'library' => 'eicons',
				),
				'condition' => array( 'hfflow_hamburger_type' => 'icon' ),
			)
		);

		$this->add_control(
			'hfflow_hamburger_text',
			array(
				'label'       => esc_html__( 'Label Text', 'headerfooterflow-for-elementor' ),
				'description' => esc_html__( 'Optional text beside the icon. Leave empty for an icon-only button.', 'headerfooterflow-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Menu', 'headerfooterflow-for-elementor' ),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'hfflow_hamburger_text_position',
			array(
				'label'     => esc_html__( 'Text Position', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => esc_html__( 'Before Icon', 'headerfooterflow-for-elementor' ),
					'after'  => esc_html__( 'After Icon', 'headerfooterflow-for-elementor' ),
				),
				'condition' => array( 'hfflow_hamburger_text!' => '' ),
			)
		);

		$this->add_control(
			'hfflow_hamburger_aria_label',
			array(
				'label'       => esc_html__( 'Accessible Label', 'headerfooterflow-for-elementor' ),
				'description' => esc_html__( 'Read out by screen readers. Used only when there is no label text.', 'headerfooterflow-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Open menu', 'headerfooterflow-for-elementor' ),
				'condition'   => array( 'hfflow_hamburger_text' => '' ),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_align',
			array(
				'label'     => esc_html__( 'Alignment', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_visibility',
			array(
				'label'          => esc_html__( 'Visibility', 'headerfooterflow-for-elementor' ),
				'description'    => esc_html__( 'Hide the button on the breakpoints where the full menu is already visible.', 'headerfooterflow-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'flex' => esc_html__( 'Show', 'headerfooterflow-for-elementor' ),
					'none' => esc_html__( 'Hide', 'headerfooterflow-for-elementor' ),
				),
				'default'        => 'flex',
				'tablet_default' => 'flex',
				'mobile_default' => 'flex',
				'selectors'      => array(
					'{{WRAPPER}} .hfflow-hamburger-wrap' => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_panel_style',
			array(
				'label'        => esc_html__( 'Customize The Panel', 'headerfooterflow-for-elementor' ),
				'description'  => esc_html__( 'Adds Panel, Panel Header and Panel Menu sections to the Style tab. The off-canvas panel is shared by the whole site, so turn this on for one hamburger widget only.', 'headerfooterflow-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'headerfooterflow-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'headerfooterflow-for-elementor' ),
				'default'      => '',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: the button box itself.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_button_style_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_button_style',
			array(
				'label' => esc_html__( 'Button', 'headerfooterflow-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px'  => array(
						'min' => 10,
						'max' => 120,
					),
					'em'  => array(
						'min'  => 0.5,
						'max'  => 8,
						'step' => 0.1,
					),
					'rem' => array(
						'min'  => 0.5,
						'max'  => 8,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => '--hfflow-hamburger-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_padding',
			array(
				'label'      => esc_html__( 'Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'hfflow_hamburger_button_tabs' );

		$this->start_controls_tab(
			'hfflow_hamburger_button_normal',
			array( 'label' => esc_html__( 'Normal', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_hamburger_color',
			array(
				'label'     => esc_html__( 'Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hfflow_hamburger_border',
				'selector' => '{{WRAPPER}} .hfflow-hamburger',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'hfflow_hamburger_shadow',
				'selector' => '{{WRAPPER}} .hfflow-hamburger',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_hamburger_button_hover',
			array( 'label' => esc_html__( 'Hover', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_hamburger_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger:hover, {{WRAPPER}} .hfflow-hamburger:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger:hover, {{WRAPPER}} .hfflow-hamburger:focus-visible' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger:hover, {{WRAPPER}} .hfflow-hamburger:focus-visible' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style tab: the animated lines.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_lines_style_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_lines_style',
			array(
				'label'     => esc_html__( 'Lines', 'headerfooterflow-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfflow_hamburger_type' => 'lines' ),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_bar_height',
			array(
				'label'      => esc_html__( 'Thickness', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 12,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 2,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => '--hfflow-hamburger-bar: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_bar_gap',
			array(
				'label'      => esc_html__( 'Gap', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 7,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => '--hfflow-hamburger-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hfflow_hamburger_bar_radius',
			array(
				'label'      => esc_html__( 'Line Radius', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 2,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => '--hfflow-hamburger-bar-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: the optional label text.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_text_style_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_text_style',
			array(
				'label'     => esc_html__( 'Label Text', 'headerfooterflow-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfflow_hamburger_text!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hfflow_hamburger_text_typography',
				'selector' => '{{WRAPPER}} .hfflow-hamburger__text',
			)
		);

		$this->add_control(
			'hfflow_hamburger_text_color',
			array(
				'label'     => esc_html__( 'Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfflow-hamburger__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_hamburger_text_gap',
			array(
				'label'      => esc_html__( 'Gap From Icon', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .hfflow-hamburger' => '--hfflow-hamburger-text-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: the off-canvas panel box.
	 *
	 * The panel is printed once per page on `wp_body_open`, outside any widget,
	 * so these selectors are deliberately global rather than `{{WRAPPER}}`
	 * scoped. They are gated behind an opt-in switch so two hamburger widgets
	 * on one page cannot fight over the same panel.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_panel_style_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_panel_box_style',
			array(
				'label'     => esc_html__( 'Panel', 'headerfooterflow-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfflow_hamburger_panel_style' => 'yes' ),
			)
		);

		$this->add_control(
			'hfflow_panel_side',
			array(
				'label'                => esc_html__( 'Slide In From', 'headerfooterflow-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'right',
				'toggle'               => false,
				'options'              => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'  => '--hfflow-offcanvas-left: 0; --hfflow-offcanvas-right: auto; --hfflow-offcanvas-hidden-x: -100%',
					'right' => '--hfflow-offcanvas-left: auto; --hfflow-offcanvas-right: 0; --hfflow-offcanvas-hidden-x: 100%',
				),
				'selectors'            => array(
					'.hfflow-offcanvas' => '{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_width',
			array(
				'label'      => esc_html__( 'Width', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 800,
					),
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
					'vw' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 350,
				),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_color',
			array(
				'label'       => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'description' => esc_html__( 'Base colour for the panel, used by the site title and the close icon.', 'headerfooterflow-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_overlay',
			array(
				'label'     => esc_html__( 'Overlay Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_padding',
			array(
				'label'      => esc_html__( 'Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style tab: the panel's top bar and close button.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_panel_bar_style_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_panel_bar_style',
			array(
				'label'     => esc_html__( 'Panel Header', 'headerfooterflow-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfflow_hamburger_panel_style' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-bar-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_logo_width',
			array(
				'label'       => esc_html__( 'Logo Width', 'headerfooterflow-for-elementor' ),
				'description' => esc_html__( 'Applies to the site custom logo, when one is set.', 'headerfooterflow-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%' ),
				'range'       => array(
					'px' => array(
						'min' => 40,
						'max' => 400,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 185,
				),
				'selectors'   => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-logo-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'hfflow_panel_title_typography',
				'label'     => esc_html__( 'Site Title Typography', 'headerfooterflow-for-elementor' ),
				'selector'  => '.hfflow-offcanvas .hfflow-offcanvas__title',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hfflow_panel_close_heading',
			array(
				'label'     => esc_html__( 'Close Button', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_close_size',
			array(
				'label'      => esc_html__( 'Size', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-close-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_close_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-close-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'hfflow_panel_close_tabs' );

		$this->start_controls_tab(
			'hfflow_panel_close_normal',
			array( 'label' => esc_html__( 'Normal', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_panel_close_color',
			array(
				'label'     => esc_html__( 'Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-close-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_close_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-close-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_panel_close_hover',
			array( 'label' => esc_html__( 'Hover', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_panel_close_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-close-color-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_close_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-close-bg-hover: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style tab: the menu inside the panel.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 * @internal
	 */
	private function register_panel_menu_style_controls(): void {
		$this->start_controls_section(
			'hfflow_hamburger_panel_menu_style',
			array(
				'label'     => esc_html__( 'Panel Menu', 'headerfooterflow-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfflow_hamburger_panel_style' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hfflow_panel_menu_typography',
				'selector' => '.hfflow-offcanvas .hfflow-offcanvas__menu a',
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_menu_align',
			array(
				'label'     => esc_html__( 'Alignment', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'headerfooterflow-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-align: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'hfflow_panel_menu_tabs' );

		$this->start_controls_tab(
			'hfflow_panel_menu_normal',
			array( 'label' => esc_html__( 'Normal', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_panel_menu_color',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-link-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_menu_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-link-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_panel_menu_hover',
			array( 'label' => esc_html__( 'Hover', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_panel_menu_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-link-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfflow_panel_menu_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-link-bg-hover: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfflow_panel_menu_active',
			array( 'label' => esc_html__( 'Active', 'headerfooterflow-for-elementor' ) )
		);

		$this->add_control(
			'hfflow_panel_menu_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'headerfooterflow-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-link-active: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'hfflow_panel_nav_padding',
			array(
				'label'      => esc_html__( 'Menu Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'separator'  => 'before',
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-nav-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_link_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-link-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_item_gap',
			array(
				'label'      => esc_html__( 'Space Between Items', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-item-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfflow_panel_sub_indent',
			array(
				'label'      => esc_html__( 'Submenu Indent', 'headerfooterflow-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'.hfflow-offcanvas' => '--hfflow-offcanvas-sub-indent: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget on the front end and in the editor preview.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$type     = isset( $settings['hfflow_hamburger_type'] ) ? (string) $settings['hfflow_hamburger_type'] : 'lines';
		$text     = isset( $settings['hfflow_hamburger_text'] ) ? trim( (string) $settings['hfflow_hamburger_text'] ) : '';
		$position = isset( $settings['hfflow_hamburger_text_position'] ) ? (string) $settings['hfflow_hamburger_text_position'] : 'after';
		$lines    = isset( $settings['hfflow_hamburger_lines'] ) ? absint( $settings['hfflow_hamburger_lines'] ) : 3;
		$lines    = max( 2, min( 4, $lines ) );

		$classes = array( 'hfflow-hamburger' );

		if ( 'icon' === $type ) {
			$classes[] = 'hfflow-hamburger--icon';
		} else {
			$classes[] = 'hfflow-hamburger--lines';

			$animate = isset( $settings['hfflow_hamburger_animation'] ) ? (string) $settings['hfflow_hamburger_animation'] : '';

			if ( 3 === $lines && 'yes' === $animate ) {
				$classes[] = 'hfflow-hamburger--cross';
			}
		}

		if ( '' !== $text && 'before' === $position ) {
			$classes[] = 'hfflow-hamburger--text-before';
		}

		$this->add_render_attribute(
			'hfflow_button',
			array(
				'class'                        => $classes,
				'type'                         => 'button',
				'aria-controls'                => 'hfflow-offcanvas',
				'aria-expanded'                => 'false',
				'data-hfflow-offcanvas-open' => '',
			)
		);

		if ( '' === $text ) {
			$label = isset( $settings['hfflow_hamburger_aria_label'] ) ? trim( (string) $settings['hfflow_hamburger_aria_label'] ) : '';

			if ( '' === $label ) {
				$label = esc_html__( 'Open menu', 'headerfooterflow-for-elementor' );
			}

			$this->add_render_attribute( 'hfflow_button', 'aria-label', $label );
		}
		?>
		<div class="hfflow-hamburger-wrap">
			<button <?php $this->print_render_attribute_string( 'hfflow_button' ); ?>>
				<?php
				if ( '' !== $text && 'before' !== $position ) {
					echo '<span class="hfflow-hamburger__text">' . esc_html( $text ) . '</span>';
				}

				if ( 'icon' === $type ) {
					echo '<span class="hfflow-hamburger__icon">';
					Icons_Manager::render_icon(
						$settings['hfflow_hamburger_icon'],
						array(
							'aria-hidden' => 'true',
							'focusable'   => 'false',
						)
					);
					echo '</span>';
				} else {
					echo '<span class="hfflow-hamburger__box" aria-hidden="true">';

					for ( $i = 0; $i < $lines; $i++ ) {
						echo '<span class="hfflow-hamburger__line"></span>';
					}

					echo '</span>';
				}

				if ( '' !== $text && 'before' === $position ) {
					echo '<span class="hfflow-hamburger__text">' . esc_html( $text ) . '</span>';
				}
				?>
			</button>
		</div>
		<?php
		if ( ! has_nav_menu( Mobile_Menu::LOCATION ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf(
				'<p class="hfflow-hamburger-notice">%s</p>',
				esc_html__( 'Editor only: assign a menu to the plugin off-canvas menu location under Appearance → Menus, otherwise this button has no panel to open.', 'headerfooterflow-for-elementor' )
			);
		}
	}
}
