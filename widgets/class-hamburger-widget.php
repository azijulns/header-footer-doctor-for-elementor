<?php
/**
 * Elementor hamburger toggle widget.
 *
 * @package HeaderFooterDoctor
 * @since   1.1.0
 */

namespace HFDoctor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use HFDoctor\Mobile_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the button that opens the plugin's off-canvas mobile menu.
 *
 * The button is a plain `<button>` carrying the `data-hfdoctor-offcanvas-open`
 * attribute, which is part of the default trigger selector handled by
 * `assets/js/offcanvas.js`, so the widget ships no script of its own.
 *
 * @package HeaderFooterDoctor
 * @since   1.1.0
 * @extends \Elementor\Widget_Base
 * @see     \HFDoctor\Mobile_Menu
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
		return 'hfdoctor-hamburger';
	}

	/**
	 * Widget label shown in the Elementor panel.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Mobile Hamburger (Doctor)', 'header-footer-doctor-for-elementor' );
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
		return array( 'hfdoctor-hamburger' );
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
			'hfdoctor_hamburger_section',
			array(
				'label' => esc_html__( 'Hamburger', 'header-footer-doctor-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( ! has_nav_menu( Mobile_Menu::LOCATION ) ) {
			$this->add_control(
				'hfdoctor_hamburger_no_menu',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						'<strong>%1$s</strong><br>%2$s',
						esc_html__( 'No off-canvas menu assigned.', 'header-footer-doctor-for-elementor' ),
						sprintf(
							/* translators: %s: link to the WordPress menu locations screen. */
							esc_html__( 'Assign a menu to the off-canvas location on the %s screen, otherwise this button has no panel to open.', 'header-footer-doctor-for-elementor' ),
							'<a href="' . esc_url( admin_url( 'nav-menus.php?action=locations' ) ) . '" target="_blank">' .
								esc_html__( 'Menus', 'header-footer-doctor-for-elementor' ) . '</a>'
						)
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}

		$this->add_control(
			'hfdoctor_hamburger_type',
			array(
				'label'   => esc_html__( 'Style', 'header-footer-doctor-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lines',
				'options' => array(
					'lines' => esc_html__( 'Lines', 'header-footer-doctor-for-elementor' ),
					'icon'  => esc_html__( 'Icon', 'header-footer-doctor-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_lines',
			array(
				'label'     => esc_html__( 'Lines', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'2' => esc_html__( 'Two', 'header-footer-doctor-for-elementor' ),
					'3' => esc_html__( 'Three', 'header-footer-doctor-for-elementor' ),
					'4' => esc_html__( 'Four', 'header-footer-doctor-for-elementor' ),
				),
				'condition' => array( 'hfdoctor_hamburger_type' => 'lines' ),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_animation',
			array(
				'label'        => esc_html__( 'Animate To Close Icon', 'header-footer-doctor-for-elementor' ),
				'description'  => esc_html__( 'Morphs the lines into a cross while the panel is open.', 'header-footer-doctor-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'header-footer-doctor-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'header-footer-doctor-for-elementor' ),
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array(
					'hfdoctor_hamburger_type'  => 'lines',
					'hfdoctor_hamburger_lines' => '3',
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_icon',
			array(
				'label'     => esc_html__( 'Icon', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'eicon-menu-bar',
					'library' => 'eicons',
				),
				'condition' => array( 'hfdoctor_hamburger_type' => 'icon' ),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_text',
			array(
				'label'       => esc_html__( 'Label Text', 'header-footer-doctor-for-elementor' ),
				'description' => esc_html__( 'Optional text beside the icon. Leave empty for an icon-only button.', 'header-footer-doctor-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Menu', 'header-footer-doctor-for-elementor' ),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_text_position',
			array(
				'label'     => esc_html__( 'Text Position', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => esc_html__( 'Before Icon', 'header-footer-doctor-for-elementor' ),
					'after'  => esc_html__( 'After Icon', 'header-footer-doctor-for-elementor' ),
				),
				'condition' => array( 'hfdoctor_hamburger_text!' => '' ),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_aria_label',
			array(
				'label'       => esc_html__( 'Accessible Label', 'header-footer-doctor-for-elementor' ),
				'description' => esc_html__( 'Read out by screen readers. Used only when there is no label text.', 'header-footer-doctor-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Open menu', 'header-footer-doctor-for-elementor' ),
				'condition'   => array( 'hfdoctor_hamburger_text' => '' ),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_align',
			array(
				'label'     => esc_html__( 'Alignment', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_visibility',
			array(
				'label'          => esc_html__( 'Visibility', 'header-footer-doctor-for-elementor' ),
				'description'    => esc_html__( 'Hide the button on the breakpoints where the full menu is already visible.', 'header-footer-doctor-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'flex' => esc_html__( 'Show', 'header-footer-doctor-for-elementor' ),
					'none' => esc_html__( 'Hide', 'header-footer-doctor-for-elementor' ),
				),
				'default'        => 'flex',
				'tablet_default' => 'flex',
				'mobile_default' => 'flex',
				'selectors'      => array(
					'{{WRAPPER}} .hfdoctor-hamburger-wrap' => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_panel_style',
			array(
				'label'        => esc_html__( 'Customize The Panel', 'header-footer-doctor-for-elementor' ),
				'description'  => esc_html__( 'Adds Panel, Panel Header and Panel Menu sections to the Style tab. The off-canvas panel is shared by the whole site, so turn this on for one hamburger widget only.', 'header-footer-doctor-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'header-footer-doctor-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'header-footer-doctor-for-elementor' ),
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
			'hfdoctor_hamburger_button_style',
			array(
				'label' => esc_html__( 'Button', 'header-footer-doctor-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'header-footer-doctor-for-elementor' ),
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
					'{{WRAPPER}} .hfdoctor-hamburger' => '--hfdoctor-hamburger-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_padding',
			array(
				'label'      => esc_html__( 'Padding', 'header-footer-doctor-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfdoctor-hamburger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'header-footer-doctor-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .hfdoctor-hamburger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'hfdoctor_hamburger_button_tabs' );

		$this->start_controls_tab(
			'hfdoctor_hamburger_button_normal',
			array( 'label' => esc_html__( 'Normal', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_hamburger_color',
			array(
				'label'     => esc_html__( 'Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hfdoctor_hamburger_border',
				'selector' => '{{WRAPPER}} .hfdoctor-hamburger',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'hfdoctor_hamburger_shadow',
				'selector' => '{{WRAPPER}} .hfdoctor-hamburger',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfdoctor_hamburger_button_hover',
			array( 'label' => esc_html__( 'Hover', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_hamburger_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger:hover, {{WRAPPER}} .hfdoctor-hamburger:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger:hover, {{WRAPPER}} .hfdoctor-hamburger:focus-visible' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger:hover, {{WRAPPER}} .hfdoctor-hamburger:focus-visible' => 'border-color: {{VALUE}};',
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
			'hfdoctor_hamburger_lines_style',
			array(
				'label'     => esc_html__( 'Lines', 'header-footer-doctor-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfdoctor_hamburger_type' => 'lines' ),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_bar_height',
			array(
				'label'      => esc_html__( 'Thickness', 'header-footer-doctor-for-elementor' ),
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
					'{{WRAPPER}} .hfdoctor-hamburger' => '--hfdoctor-hamburger-bar: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_bar_gap',
			array(
				'label'      => esc_html__( 'Gap', 'header-footer-doctor-for-elementor' ),
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
					'{{WRAPPER}} .hfdoctor-hamburger' => '--hfdoctor-hamburger-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_bar_radius',
			array(
				'label'      => esc_html__( 'Line Radius', 'header-footer-doctor-for-elementor' ),
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
					'{{WRAPPER}} .hfdoctor-hamburger' => '--hfdoctor-hamburger-bar-radius: {{SIZE}}{{UNIT}};',
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
			'hfdoctor_hamburger_text_style',
			array(
				'label'     => esc_html__( 'Label Text', 'header-footer-doctor-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfdoctor_hamburger_text!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hfdoctor_hamburger_text_typography',
				'selector' => '{{WRAPPER}} .hfdoctor-hamburger__text',
			)
		);

		$this->add_control(
			'hfdoctor_hamburger_text_color',
			array(
				'label'     => esc_html__( 'Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hfdoctor-hamburger__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_hamburger_text_gap',
			array(
				'label'      => esc_html__( 'Gap From Icon', 'header-footer-doctor-for-elementor' ),
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
					'{{WRAPPER}} .hfdoctor-hamburger' => '--hfdoctor-hamburger-text-gap: {{SIZE}}{{UNIT}};',
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
			'hfdoctor_hamburger_panel_box_style',
			array(
				'label'     => esc_html__( 'Panel', 'header-footer-doctor-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfdoctor_hamburger_panel_style' => 'yes' ),
			)
		);

		$this->add_control(
			'hfdoctor_panel_side',
			array(
				'label'                => esc_html__( 'Slide In From', 'header-footer-doctor-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'right',
				'toggle'               => false,
				'options'              => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'  => '--hfdoctor-offcanvas-left: 0; --hfdoctor-offcanvas-right: auto; --hfdoctor-offcanvas-hidden-x: -100%',
					'right' => '--hfdoctor-offcanvas-left: auto; --hfdoctor-offcanvas-right: 0; --hfdoctor-offcanvas-hidden-x: 100%',
				),
				'selectors'            => array(
					'.hfdoctor-offcanvas' => '{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_width',
			array(
				'label'      => esc_html__( 'Width', 'header-footer-doctor-for-elementor' ),
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
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_color',
			array(
				'label'       => esc_html__( 'Text Color', 'header-footer-doctor-for-elementor' ),
				'description' => esc_html__( 'Base colour for the panel, used by the site title and the close icon.', 'header-footer-doctor-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_overlay',
			array(
				'label'     => esc_html__( 'Overlay Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas__overlay' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_padding',
			array(
				'label'      => esc_html__( 'Padding', 'header-footer-doctor-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'hfdoctor_hamburger_panel_bar_style',
			array(
				'label'     => esc_html__( 'Panel Header', 'header-footer-doctor-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfdoctor_hamburger_panel_style' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'header-footer-doctor-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-bar-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_logo_width',
			array(
				'label'       => esc_html__( 'Logo Width', 'header-footer-doctor-for-elementor' ),
				'description' => esc_html__( 'Applies to the site custom logo, when one is set.', 'header-footer-doctor-for-elementor' ),
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
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-logo-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'hfdoctor_panel_title_typography',
				'label'     => esc_html__( 'Site Title Typography', 'header-footer-doctor-for-elementor' ),
				'selector'  => '.hfdoctor-offcanvas .hfdoctor-offcanvas__title',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hfdoctor_panel_close_heading',
			array(
				'label'     => esc_html__( 'Close Button', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_close_size',
			array(
				'label'      => esc_html__( 'Size', 'header-footer-doctor-for-elementor' ),
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
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-close-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_close_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'header-footer-doctor-for-elementor' ),
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
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-close-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'hfdoctor_panel_close_tabs' );

		$this->start_controls_tab(
			'hfdoctor_panel_close_normal',
			array( 'label' => esc_html__( 'Normal', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_panel_close_color',
			array(
				'label'     => esc_html__( 'Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-close-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_close_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-close-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfdoctor_panel_close_hover',
			array( 'label' => esc_html__( 'Hover', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_panel_close_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-close-color-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_close_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-close-bg-hover: {{VALUE}};',
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
			'hfdoctor_hamburger_panel_menu_style',
			array(
				'label'     => esc_html__( 'Panel Menu', 'header-footer-doctor-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'hfdoctor_hamburger_panel_style' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hfdoctor_panel_menu_typography',
				'selector' => '.hfdoctor-offcanvas .hfdoctor-offcanvas__menu a',
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_menu_align',
			array(
				'label'     => esc_html__( 'Alignment', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'header-footer-doctor-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-align: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'hfdoctor_panel_menu_tabs' );

		$this->start_controls_tab(
			'hfdoctor_panel_menu_normal',
			array( 'label' => esc_html__( 'Normal', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_panel_menu_color',
			array(
				'label'     => esc_html__( 'Text Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-link-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_menu_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-link-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfdoctor_panel_menu_hover',
			array( 'label' => esc_html__( 'Hover', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_panel_menu_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-link-hover: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hfdoctor_panel_menu_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-link-bg-hover: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hfdoctor_panel_menu_active',
			array( 'label' => esc_html__( 'Active', 'header-footer-doctor-for-elementor' ) )
		);

		$this->add_control(
			'hfdoctor_panel_menu_color_active',
			array(
				'label'     => esc_html__( 'Text Color', 'header-footer-doctor-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-link-active: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'hfdoctor_panel_nav_padding',
			array(
				'label'      => esc_html__( 'Menu Padding', 'header-footer-doctor-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'separator'  => 'before',
				'selectors'  => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-nav-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_link_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'header-footer-doctor-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-link-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_item_gap',
			array(
				'label'      => esc_html__( 'Space Between Items', 'header-footer-doctor-for-elementor' ),
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
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-item-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hfdoctor_panel_sub_indent',
			array(
				'label'      => esc_html__( 'Submenu Indent', 'header-footer-doctor-for-elementor' ),
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
					'.hfdoctor-offcanvas' => '--hfdoctor-offcanvas-sub-indent: {{SIZE}}{{UNIT}};',
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

		$type     = isset( $settings['hfdoctor_hamburger_type'] ) ? (string) $settings['hfdoctor_hamburger_type'] : 'lines';
		$text     = isset( $settings['hfdoctor_hamburger_text'] ) ? trim( (string) $settings['hfdoctor_hamburger_text'] ) : '';
		$position = isset( $settings['hfdoctor_hamburger_text_position'] ) ? (string) $settings['hfdoctor_hamburger_text_position'] : 'after';
		$lines    = isset( $settings['hfdoctor_hamburger_lines'] ) ? absint( $settings['hfdoctor_hamburger_lines'] ) : 3;
		$lines    = max( 2, min( 4, $lines ) );

		$classes = array( 'hfdoctor-hamburger' );

		if ( 'icon' === $type ) {
			$classes[] = 'hfdoctor-hamburger--icon';
		} else {
			$classes[] = 'hfdoctor-hamburger--lines';

			$animate = isset( $settings['hfdoctor_hamburger_animation'] ) ? (string) $settings['hfdoctor_hamburger_animation'] : '';

			if ( 3 === $lines && 'yes' === $animate ) {
				$classes[] = 'hfdoctor-hamburger--cross';
			}
		}

		if ( '' !== $text && 'before' === $position ) {
			$classes[] = 'hfdoctor-hamburger--text-before';
		}

		$this->add_render_attribute(
			'hfdoctor_button',
			array(
				'class'                        => $classes,
				'type'                         => 'button',
				'aria-controls'                => 'hfdoctor-offcanvas',
				'aria-expanded'                => 'false',
				'data-hfdoctor-offcanvas-open' => '',
			)
		);

		if ( '' === $text ) {
			$label = isset( $settings['hfdoctor_hamburger_aria_label'] ) ? trim( (string) $settings['hfdoctor_hamburger_aria_label'] ) : '';

			if ( '' === $label ) {
				$label = esc_html__( 'Open menu', 'header-footer-doctor-for-elementor' );
			}

			$this->add_render_attribute( 'hfdoctor_button', 'aria-label', $label );
		}
		?>
		<div class="hfdoctor-hamburger-wrap">
			<button <?php $this->print_render_attribute_string( 'hfdoctor_button' ); ?>>
				<?php
				if ( '' !== $text && 'before' !== $position ) {
					echo '<span class="hfdoctor-hamburger__text">' . esc_html( $text ) . '</span>';
				}

				if ( 'icon' === $type ) {
					echo '<span class="hfdoctor-hamburger__icon">';
					Icons_Manager::render_icon(
						$settings['hfdoctor_hamburger_icon'],
						array(
							'aria-hidden' => 'true',
							'focusable'   => 'false',
						)
					);
					echo '</span>';
				} else {
					echo '<span class="hfdoctor-hamburger__box" aria-hidden="true">';

					for ( $i = 0; $i < $lines; $i++ ) {
						echo '<span class="hfdoctor-hamburger__line"></span>';
					}

					echo '</span>';
				}

				if ( '' !== $text && 'before' === $position ) {
					echo '<span class="hfdoctor-hamburger__text">' . esc_html( $text ) . '</span>';
				}
				?>
			</button>
		</div>
		<?php
		if ( ! has_nav_menu( Mobile_Menu::LOCATION ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf(
				'<p class="hfdoctor-hamburger-notice">%s</p>',
				esc_html__( 'Editor only: assign a menu to the plugin off-canvas menu location under Appearance → Menus, otherwise this button has no panel to open.', 'header-footer-doctor-for-elementor' )
			);
		}
	}
}
