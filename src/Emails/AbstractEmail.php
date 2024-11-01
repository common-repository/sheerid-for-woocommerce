<?php

namespace WooCommerce\SheerID\Emails;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Logger;
use WooCommerce\SheerID\TemplateLoader;

abstract class AbstractEmail extends \WC_Email {

	protected $client;

	protected $templates;

	protected $log;

	/**
	 * @var \SheerID\Model\Verification\VerificationDetails
	 */
	protected $verification_details;

	/**
	 * @var \SheerID\Model\Program
	 */
	protected $program;

	/**
	 * @var \WooCommerce\SheerID\Program
	 */
	protected $local_program;

	/**
	 * @var \WooCommerce\SheerID\Verification
	 */
	protected $verification;

	public $last_error;

	public function __construct( BaseClient $client, TemplateLoader $templates, Logger $log ) {
		$this->client        = $client;
		$this->templates     = $templates;
		$this->template_base = $templates->get_default_path();
		$this->log           = $log;
		parent::__construct();
	}

	public function init_form_fields() {
		parent::init_form_fields();
		unset( $this->form_fields['enabled'] );
		$this->form_fields = array_merge( [
			'email_text' => [
				'type'        => 'sheerid_text',
				'title'       => __( 'Enable/Disable', 'sheerid-for-woocommerce' ),
				'description' => sprintf( __( 'You can enable/disable SheerID emails at the %1$sprogram%2$s level.', 'sheerid-for-woocommerce' ), '<a target="_blank" href=" ' . admin_url( 'admin.php?page=wc-sheerid-settings&tab=sheerid_program' ) . '">', '</a>' )
			],
		], $this->form_fields );
	}

	public function initialize() {
	}

	public function generate_sheerid_text_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );

		ob_start();
		?>
        <tr>
            <th class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="forminp">
                <p>
					<?php echo esc_html( $data['description'] ) ?>
                </p>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	public function get_id() {
		return $this->id;
	}

	public function get_default_additional_content() {
		return __( 'Thanks for using {site_url}!', 'sheerid-for-woocommerce' );
	}

	/**
	 * @param \WooCommerce\SheerID\Verification $verification
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function before_trigger( $verification ) {
		$this->verification = $verification;
		if ( ! $this->verification_details ) {
			$this->verification_details = $this->client->verificationDetails->retrieve( $verification->get_verification_id() );
		}
		if ( ! $this->program ) {
			$this->program = $this->client->programs->retrieve( $verification->get_program_id() );
		}
		$this->local_program = sheerid_wc_get_program( [ 'program_id' => $verification->get_program_id() ] );

		$this->validate_email_fields();

		$this->populate_placeholder_data();
	}

	protected function populate_placeholder_data() {
		$this->placeholders['{verification_id}'] = $this->verification->get_verification_id();
		$this->placeholders['{program_id}']      = $this->program->getId();
		$this->placeholders['{first_name}']      = $this->verification_details->getPersonInfo()->getFirstName();
		$this->placeholders['{last_name}']       = $this->verification_details->getPersonInfo()->getLastName();
		$this->placeholders['{email}']           = $this->verification_details->getPersonInfo()->getEmail();
	}

	protected function validate_email_fields() {
		if ( is_wp_error( $this->verification_details ) ) {
			throw new \Exception( $this->verification_details->get_error_message() ); //phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
		if ( is_wp_error( $this->program ) ) {
			throw new \Exception( $this->program->get_error_message() ); //phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
	}

}