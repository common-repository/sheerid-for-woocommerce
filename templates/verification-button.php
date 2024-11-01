<?php
/**
 * @var \WooCommerce\SheerID\Shortcode\ShortcodeAttributes $attributes
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;

?>
<div class="'wc-sheerid-button__container">
    <button
            class="wcSheerIDButton <?php echo esc_attr( $attributes->get( 'class' ) ) ?>"
            style="<?php echo esc_attr( $attributes->get_styles() ) ?>"><?php echo esc_html( $attributes->get( 'label' ) ) ?></button>
</div>
