<?php
/**
 * Closing body markup used when a plugin footer applies.
 *
 * Loaded in place of the theme's `footer.php`.
 *
 * @package HeaderFooterFlow
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="hfflow-footer">
	<?php
	/**
	 * Fires where the plugin footer template is printed.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hfflow_footer' );
	?>
</div>

<?php wp_footer(); ?>
</body>
</html>
