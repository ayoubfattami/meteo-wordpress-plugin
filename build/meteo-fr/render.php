<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<p <?php echo esc_html( get_block_wrapper_attributes() ); ?>>
	<?php esc_html_e( 'meteo-fr – hello from a dynamic block!', 'meteo-fr' ); ?>
</p>
