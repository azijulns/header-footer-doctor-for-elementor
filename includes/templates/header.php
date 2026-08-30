<?php
/**
 * Document head and opening body markup used when a plugin header applies.
 *
 * Loaded in place of the theme's `header.php`.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content">
	<?php esc_html_e( 'Skip to content', 'header-footer-doctor-for-elementor' ); ?>
</a>

<?php
/**
 * Fires where the plugin header template is printed.
 *
 * @since 1.0.0
 */
do_action( 'hfflow_header' );
