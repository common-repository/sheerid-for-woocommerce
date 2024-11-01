<?php

namespace WooCommerce\SheerID\Admin;

use WooCommerce\SheerID\Assets\AssetsApi;
use WooCommerce\SheerID\Registry\BaseRegistry;

class AdminSettingsController {

	private $registry;

	private $assets;

	private $settings;

	private $default_settings_id = 'sheerid_api';

	public function __construct( BaseRegistry $registry, AssetsApi $assets ) {
		$this->registry = $registry;
		$this->assets   = $assets;
	}

	public function initialize() {
		add_action( 'woocommerce_sheerid_settings_registration', [ $this, 'register_settings' ] );
		add_action( 'wc_sheerid_admin_section', [ $this, 'render' ], 10, 2 );
		add_action( 'wp_loaded', [ $this, 'save' ] );
		$this->registry->initialize();
		$this->register_scripts();
	}

	public function register_settings( $container ) {
		foreach ( $this->settings as $setting ) {
			$this->registry->register( $setting );
		}
	}

	public function set_settings( $settings ) {
		$this->settings = $settings;
	}

	public function register_scripts() {
		$this->assets->register_script(
			'wc-sheerid-admin-settings',
			'assets/build/sheer-id-settings.js',
			[ 'jquery-tiptip', 'wc-sheerid-admin-commons' ]
		);
		$this->assets->register_script(
			'wc-sheerid-activation-modal',
			'assets/build/activation-modal.js',
			[ 'wc-backbone-modal' ]
		);
		$this->assets->register_script( 'wc-sheerid-program-app', 'assets/build/admin-program-app.js', [ 'wc-sheerid-admin-commons' ] );
		$this->assets->register_script( 'wc-sheerid-admin-commons', 'assets/build/admin-commons.js' );

		$this->assets->register_style( 'wc-sheerid-admin-styles', 'assets/build/admin-styles.css' );
	}

	public function render( $page_id, $tab = null ) {
		$id = $tab ? $tab : $page_id;
		if ( $id ) {
			$settings = $this->registry->get( $id );
			if ( ! $settings ) {
				return \trigger_error( sprintf( 'Invalid settings ID %s', esc_html( $id ) ) );
			}
		} else {
			global $sheerid_section;
			$sheerid_section = $this->default_settings_id;
			$settings        = $this->registry->get( $this->default_settings_id );
		}

		$settings->set_is_selected( true );

		wp_enqueue_script( 'wc-sheerid-admin-settings' );
		wp_enqueue_script( 'wc-sheerid-program-app' );

		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'jquery-ui-style' );
		wp_enqueue_style( 'wc-sheerid-admin-styles' );
		wp_enqueue_style( 'wp-components' );

		if ( $this->is_activation_screen() ) {
			wp_enqueue_script( 'wc-sheerid-activation-modal' );
			wp_enqueue_style( 'woocommerce_admin_styles' );
			$assets = $this->assets;
			include_once __DIR__ . '/Views/html-activation-template.php';
		}

		$this->add_script_add();

		$settings->render_options( $this->get_tabs() );
	}

	public function save() {
		if ( is_admin() && isset( $_POST['sheerid_save'] ) ) {
			$tab      = sanitize_text_field( wp_unslash( $_POST['sheerid_save'] ) );
			$settings = $this->registry->get( $tab );
			if ( $settings ) {
				// security to prevent un-restricted settings save
				check_admin_referer( 'woocommerce-sheerid-settings' );

				$settings->process_admin_options();
			}
		}
	}

	private function get_tabs() {
		$tabs = [];
		foreach ( $this->registry->get_registered_integrations() as $settings ) {
			$tabs[ $settings->get_id() ] = $settings->get_title();
		}

		return $tabs;
	}

	private function add_script_add() {
		$data = rawurlencode( wp_json_encode( [
			'ajaxUrl'         => add_query_arg( 'action', '%%action%%', admin_url( 'admin-ajax.php', 'relative' ) ),
			'webhookUri'      => \wp_parse_url( get_rest_url( null, '/wc-sheerid/v1/webhook' ) )['path'],
			'schemeOptions'   => [
				[ 'label' => 'https', 'value' => 'https' ],
				[ 'label' => 'http', 'value' => 'http' ]
			],
			'connectTabs'     => [
				[ 'name' => 'login', 'title' => __( 'Log in', 'sheerid-for-woocommerce' ) ],
				[ 'name' => 'register', 'title' => __( 'Register', 'sheerid-for-woocommerce' ) ]
			],
			'editProgramTabs' => [
				[ 'name' => 'eligibility', 'title' => __( 'Eligibility', 'sheerid-for-woocommerce' ) ],
				[ 'name' => 'offer', 'title' => __( 'Offer', 'sheerid-for-woocommerce' ) ],
				[ 'name' => 'text', 'title' => __( 'Text', 'sheerid-for-woocommerce' ) ],
				[ 'name' => 'emails', 'title' => __( 'Emails', 'sheerid-for-woocommerce' ) ],
				[ 'name' => 'general', 'title' => __( 'General Settings', 'sheerid-for-woocommerce' ) ]
			],
			'registerSteps'   => [
				__( 'Create an account with SheerID', 'sheerid-for-woocommerce' ),
				__( 'Confirm your email address and set your password', 'sheerid-for-woocommerce' ),
				__( 'Return here and click Connect', 'sheerid-for-woocommerce' )
			],
			'registerLink'    => 'https://my.sheerid.com/auth/oem-registration',
			'text'            => [
				'cancel'                   => __( 'Cancel', 'sheerid-for-woocommerce' ),
				'connect'                  => __( 'Connect', 'sheerid-for-woocommerce' ),
				'connecting'               => __( 'Connecting...', 'sheerid-for-woocommerce' ),
				'create'                   => __( 'Create', 'sheerid-for-woocommerce' ),
				'creating'                 => __( 'Creating...', 'sheerid-for-woocommerce' ),
				'delete'                   => __( 'Delete', 'sheerid-for-woocommerce' ),
				'deleting'                 => __( 'Deleting...', 'sheerid-for-woocommerce' ),
				'saving'                   => __( 'Saving...', 'sheerid-for-woocommerce' ),
				'save'                     => __( 'Save', 'sheerid-for-woocommerce' ),
				'close'                    => __( 'Close', 'sheerid-for-woocommerce' ),
				'enabledEmails'            => __( 'Enabled Emails', 'sheerid-for-woocommerce' ),
				'enabledSegments'          => __( 'Enabled Segments', 'sheerid-for-woocommerce' ),
				'webhookUrl'               => __( 'Webhook URL', 'sheerid-for-woocommerce' ),
				'actions'                  => __( 'Actions', 'sheerid-for-woocommerce' ),
				'firstResponderDesc'       => __( 'In this section you can select all of the first responder categories that this program supports.', 'sheerid-for-woocommerce' ),
				'military-trial-v2'        => [
					'desc' => __( 'In this section you can select all of the military categories that this program supports.', 'sheerid-for-woocommerce' ),
				],
				'student-trial-v2'         => [
					'desc' => __( 'In this section you can select all of the student categories that this program supports.', 'sheerid-for-woocommerce' )
				],
				'teacher-trial-v2'         => [
					'desc' => __( 'In this section you can select all of the teacher categories that this program supports.', 'sheerid-for-woocommerce' )
				],
				'medical-trial-v2'         => [
					'desc' => __( 'In this section you can select all of the medical categories that this program supports.', 'sheerid-for-woocommerce' )
				],
				'age-trial'                => [
					'desc'        => __( 'In this section you can configure the ages which are eligible for this program.', 'sheerid-for-woocommerce' ),
					'minRequired' => __( 'Minimum age is a required field.', 'sheerid-for-woocommerce' ),
					'maxRequired' => __( 'Maximum age is a required field.', 'sheerid-for-woocommerce' )
				],
				'lowincome-trial-v2'       => [

				],
				'firstresponder-trial-v2'  => [
					'desc' => __( 'In this section you can configure the first responder segments which are eligible for this program.', 'sheerid-for-woocommerce' ),
				],
				'webhooks'                 => __( 'Webhooks', 'sheerid-for-woocommerce' ),
				'domainLabel'              => __( 'Webhook domain', 'sheerid-for-woocommerce' ),
				'domain'                   => __( 'Domain', 'sheerid-for-woocommerce' ),
				'ageSettings'              => __( 'Age Settings', 'sheerid-for-woocommerce' ),
				'errors'                   => [
					'webhook_domain' => __( 'Webhook domain is a required field.', 'sheerid-woocmmerce' ),
					'email_required' => __( 'Email address is a required field.', 'sheerid-for-woocommerce' )
				],
				'programDeleteNotice'      => __( 'Archiving this program will disable consumers from being able to use it.', 'sheerid-for-woocommerce' ),
				'verificationDeleteNotice' => __( 'Click "Delete" to delete the verification.', 'sheerid-for-woocommerce' ),
				'deleteVerification'       => __( 'Delete Verification', 'sheerid-for-woocommerce' ),
				'sendReminder'             => __( 'Send', 'sheerid-for-woocommerce' ),
				'sendReminderEmailTitle'   => __( 'Send Email Reminder', 'sheerid-for-woocommerce' ),
				'emailReminderNotice'      => __( 'By clicking "Send" an email will be sent to the user reminding them to complete their verification.' ),
				'emailLabel'               => __( 'Email Address', 'sheerid-for-woocommerce' ),
				'sending'                  => __( 'Sending...', 'sheerid-for-woocommerce' ),
				'passwordNotice'           => __( 'Haven’t set a password? Go to your inbox and confirm your email address to set a password.', 'sheerid-for-woocommerce' ),
				'registerNotice'           => __( 'Connect to a SheerID account in 3 easy steps ', 'sheerid-for-woocommerce' ),
				'createAccount'            => __( 'Create your account' ),
				'offerDescription'         => sprintf(
					__( 'Manage offers using the Woocommerce coupons page. Click %1$shere%2$s to create a coupon in WooCommerce.' ),
					'<a target="_blank" href="' . admin_url( 'edit.php?post_type=shop_coupon' ) . '">',
					'</a>'
				),
				'textDescription'          => sprintf(
					__( 'The verification modal text can be customized by using the filters provided in the plugin. Click %1$shere%2$s to read more about text customization and view example code in our docs.', 'sheerid-for-woocommerce' ),
					'<a target="_blank" href="https://docs.paymentplugins.com/wc-sheerid/config/#/code_examples?id=customize-text">',
					'</a>'
				),
				'emailDesc'                => __( 'These are the email notifications that your users will receive when verifying agaist the program.', 'sheerid-for-woocommerce' )
			]
		] ) );
		wp_add_inline_script(
			'wc-sheerid-program-app',
			'var wcSheerIdApp = JSON.parse(decodeURIComponent("' . esc_js( $data ) . '"))',
			'before'
		);
	}

	private function is_activation_screen() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['wc_sheerid_activation'] );
	}

}