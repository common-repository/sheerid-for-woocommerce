<?php

namespace WooCommerce\SheerID\Utils;

class GeneralUtils {

	public static function get_queried_product_id() {
		global $product;
		if ( ! $product || \is_string( $product ) ) {
			$object = get_queried_object();
			if ( $object && $object instanceof \WP_Post ) {
				if ( $object->post_type === 'page' ) {
					$content = $object->post_content;
					if ( $content && \has_shortcode( $content, 'product_page' ) ) {
						// find the product ID
						preg_match( '/(?<=\[product_page)\s+id=\"?([\d]+)\"?/', $content, $matches );
						if ( $matches ) {
							return $matches[1];
						}
					}
				}

				return $object->ID;
			}
		}

		return $product;
	}

	/**
	 * Decorates the provided url with parameters used ot launch the SheerID modal.
	 *
	 * @param $origin_url
	 *
	 * @return string
	 */
	public static function get_verification_url( $origin_url ) {
		return add_query_arg( [
			'launchModal' => true
		], $origin_url );
	}

}