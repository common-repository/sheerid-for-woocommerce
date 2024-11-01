<?php

namespace WooCommerce\SheerID\Emails;

use SheerID\Client\BaseClient;
use WooCommerce\SheerID\Logger;
use WooCommerce\SheerID\TemplateLoader;

class EmailController {

	private $registry;

	private $emails_registered = false;

	public function __construct( EmailRegistry $registry ) {
		$this->registry = $registry;
	}

	public function initialize() {
		add_filter( 'woocommerce_email_classes', [ $this, 'add_email_classes' ] );
		add_filter( 'woocommerce_template_directory', [ $this, 'get_template_directory' ], 10, 2 );
		add_filter( 'woocommerce_email_actions', [ $this, 'add_email_actions' ] );
	}

	public function add_email_classes( $emails ) {
		if ( ! $this->emails_registered ) {
			$this->register_emails();
		}
		foreach ( $this->get_emails() as $email_clazz ) {
			$instance = sheerid_wc_container()->get( $email_clazz );
			$instance->initialize();
			$emails[ $instance->id ] = $instance;
			$this->registry->register( $instance );
		}

		return $emails;
	}

	public function add_email_actions( $email_types ) {
		return array_merge( $email_types, [
			'woocommerce_sheerid_verification_status_success',
			'woocommerce_sheerid_verification_reminder',
			'woocommerce_sheerid_verification_status_error',
			'woocommerce_sheerid_verification_upload_failed'
		] );
	}

	private function register_emails() {
		$container = sheerid_wc_container();

		$container->register( VerificationSuccessEmail::class, function ( $container ) {
			return new VerificationSuccessEmail(
				$container->get( BaseClient::class ),
				$container->get( TemplateLoader::class ),
				$container->get( Logger::class )
			);
		} );

		$container->register( VerificationFailedEmail::class, function ( $container ) {
			return new VerificationFailedEmail(
				$container->get( BaseClient::class ),
				$container->get( TemplateLoader::class ),
				$container->get( Logger::class )
			);
		} );

		$container->register( DocumentUploadReminderEmail::class, function ( $container ) {
			return new DocumentUploadReminderEmail(
				$container->get( BaseClient::class ),
				$container->get( TemplateLoader::class ),
				$container->get( Logger::class )
			);
		} );

		$container->register( MaxDocumentUploadEmail::class, function ( $container ) {
			return new MaxDocumentUploadEmail(
				$container->get( BaseClient::class ),
				$container->get( TemplateLoader::class ),
				$container->get( Logger::class )
			);
		} );

		$container->register( VerificationReminderEmail::class, function ( $container ) {
			return new VerificationReminderEmail(
				$container->get( BaseClient::class ),
				$container->get( TemplateLoader::class ),
				$container->get( Logger::class )
			);
		} );
	}

	private function get_emails() {
		return [
			VerificationSuccessEmail::class,
			VerificationFailedEmail::class,
			DocumentUploadReminderEmail::class,
			MaxDocumentUploadEmail::class,
			VerificationReminderEmail::class
		];
	}

	/**
	 * @param string $template_dir
	 * @param string $template
	 *
	 * @return void
	 */
	public function get_template_directory( $template_dir, $template ) {
		foreach ( $this->registry->get_registered_integrations() as $email ) {
			if ( $email->template_html === $template ) {
				$template_dir = sheerid_wc_container()->get( TemplateLoader::class )->get_template_path();
			}
		}

		return $template_dir;
	}

}