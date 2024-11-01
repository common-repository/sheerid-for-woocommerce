<?php

namespace WooCommerce\SheerID\Admin\Settings;

/**
 * Class that manages the API Settings for SheerID
 */
class APISettings extends AbstractSettings {

	public $id = 'sheerid_api';

	public function __construct() {
		$this->title = __( 'API Settings', 'sheerid-for-woocommerce' );
		parent::__construct();
	}

	public function admin_options() {
		?>
        <div class="sheerid-api-settings">
            <div id="sheerid-connect-app"></div>
			<?php
			parent::admin_options();
			?>
        </div>
		<?php
	}

	public function init_form_fields() {
		$this->form_fields = [
			'title'              => [
				'type'  => 'title',
				'title' => __( 'API Settings', 'sheerid-for-woocommerce' )
			],
			'connect_production' => [
				'title'       => __( 'Connect to SheerID', 'sheerid-for-woocommerce' ),
				'type'        => 'sheerid_connect',
				'label'       => __( 'Connect Account', 'sheerid-for-woocommerce' ),
				'class'       => 'button-secondary connect-sheerid-account',
				'label'       => __( 'Click to Connect', 'sheerid-for-woocommerce' ),
				'description' => __( 'Click this button to connect the plugin to your SheerID account.', 'sheerid-for-woocommerce' )
			],
			'access_token'       => [
				'title'       => __( 'Access Token', 'sheerid-for-woocommerce' ),
				'type'        => 'password',
				'default'     => '',
				'desc_tip'    => true,
				'description' => __( 'The token used to communicate with the SheerID API.', 'sheerid-for-woocommerce' )
			],
			'test_webhook'       => [
				'title'       => __( 'Webhook Test', 'sheerid-for-woocommerce' ),
				'type'        => 'webhook_test',
				'label'       => __( 'Webhook Test', 'sheerid-for-woocommerce' ),
				'class'       => 'button button-secondary sheerid-webhook-test',
				'description' => __( 'Webhooks are endpoints on your WooCommerce store that receive messages from SheerID when important events happen. Click this button to ensure your webhook endpoint is reachable and not being blocked by
                    any 3rd party plugins.', 'sheerid-for-woocommerce' )
			],
			'webhook_url'        => [
				'title'       => __( 'Webhook URL', 'sheerid-for-woocommerce' ),
				'type'        => 'webhook_url',
				'description' => __( 'Your webhook url is the location where SheerID sends important events to your site.', 'sheerid-for-woocommerce' )
			]
		];
	}

	public function generate_sheerid_connect_html( $key, $data ) {
		$field_key    = $this->get_field_key( $key );
		$data         = wp_parse_args(
			$data,
			[
				'title'       => '',
				'label'       => '',
				'class'       => '',
				'style'       => '',
				'description' => '',
				'desc_tip'    => false,
				'id'          => 'wc-sheerid-button_' . $key,
				'disabled'    => false,
				'css'         => '',
				'environment' => 'sandbox'
			]
		);
		$access_token = $this->get_access_token();
		if ( $access_token ) {
			$data['label']       = __( 'Re-connect', 'sheerid-for-woocommerce' );
			$data['description'] = '<label class="sheerid-connect-label">' . sprintf( __( '%1$s. You can delete your access token by %2$slogging into SheerID%3$s.', 'sheerid-for-woocommerce' ),
					__( 'Status', 'sheerid-for-woocommerce' ) . ':&nbsp;' . __( 'Connected', 'sheerid-for-woocommerce' ) . '</span><span class="dashicons dashicons-yes"></span>',
					'<a target="_blank" href="' . esc_attr( esc_url( 'https://my.sheerid.com/settings/access-tokens' ) ) . '">',
					'</a>' ) . '</label>';
		}
		ob_start();
		?>
        <tr>
            <th class="titledesc">
                <label><?php echo wp_kses_post( $data['title'] ) ?><?php echo esc_html( $this->get_tooltip_html( $data ) ); ?></label>
            </th>
            <td>
                <a class="<?php echo esc_attr( $data['class'] ) ?>" style="<?php echo esc_attr( $data['css'] ); ?>"><?php echo esc_html( $data['label'] ) ?></a>
				<?php echo wp_kses_post( $this->get_description_html( $data ) ); ?>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	public function generate_webhook_test_html( $key, $data ) {
		$field_key   = $this->get_field_key( $key );
		$data        = wp_parse_args( $data, [
			'title'    => '',
			'label'    => '',
			'class'    => '',
			'css'      => '',
			'desc_tip' => false,
		] );
		$rest_url    = get_rest_url( null, '/wc-sheerid/v1/webhook' );
		$error_texts = wc_esc_json( wp_json_encode( [
			'401' => __( 'HTTP 401 unauthorized. This likely means you have a plugin installed that\'s blocking access to the WordPress REST API. You will want to make sure your SheerID webhook is added to any exclusion lists.', 'sheerid-for-woocommerce' ),
			'403' => __( 'HTTP 403 forbidden. This likely means you have a plugin installed that\'s blocking access to the WordPress REST API. You will want to make sure your SheerID webhook is added to any exclusion lists.', 'sheerid-for-woocommerce' )
		] ) );
		ob_start();
		?>
        <tr>
            <th>
                <label><?php echo wp_kses_post( $data['title'] ) ?><?php echo esc_html( $this->get_tooltip_html( $data ) ); ?></label>
            </th>
            <td>
                <a class="<?php echo esc_attr( $data['class'] ) ?>"
                   data-nonce="<?php echo esc_attr( wp_create_nonce( 'webhook_test' ) ) ?>"
                   data-processing-text="<?php esc_attr_e( 'Processing...', 'sheerid-for-woocommerce' ) ?>"
                   data-rest-url="<?php echo esc_attr( esc_url( $rest_url ) ) ?>"
                   data-success-message="<?php printf( esc_attr__( 'Success! Your webhook endpoint %s is reachable.', 'sheerid-for-woocommerce' ), esc_attr( esc_url_raw( $rest_url ) ) ) ?>"
                   data-error-texts="<?php echo esc_attr( $error_texts ) ?>"
                   style=" <?php echo esc_attr( $data['css'] ); ?>"><?php echo esc_html( $data['label'] ) ?></a>
				<?php echo wp_kses_post( $this->get_description_html( $data ) ); ?>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	public function generate_webhook_url_html( $key, $data ) {
		$data = wp_parse_args( $data, [
			'title'       => '',
			'description' => '',
			'desc_tip'    => false
		] );
		ob_start();
		?>
        <tr>
            <th>
                <label><?php echo wp_kses_post( $data['title'] ) ?><?php echo esc_html( $this->get_tooltip_html( $data ) ); ?></label>
            </th>
            <td>
                <p><?php echo esc_html( esc_url_raw( get_rest_url( null, '/wc-sheerid/v1/webhook' ) ) ) ?></p>
				<?php echo wp_kses_post( $this->get_description_html( $data ) ); ?>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	public function get_access_token() {
		return $this->get_option( 'access_token', '' );
	}

}