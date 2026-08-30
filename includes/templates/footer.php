<?php
/**
 * Closing body markup used when a plugin footer applies.
 *
 * Loaded in place of the theme's `footer.php`.
 *
 * @package HeaderFooterDoctor
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="hfdoctor-footer">
	<?php
	/**
	 * Fires where the plugin footer template is printed.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hfdoctor_footer' );
	?>
</div>

<?php wp_footer(); ?>
</body>
</html>
