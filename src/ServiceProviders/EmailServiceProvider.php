<?php

namespace WooCommerce\SheerID\ServiceProviders;

use WooCommerce\SheerID\Emails\DocumentUploadReminderEmail;
use WooCommerce\SheerID\Emails\EmailController;
use WooCommerce\SheerID\Emails\EmailRegistry;
use WooCommerce\SheerID\TemplateLoader;

class EmailServiceProvider extends AbstractServiceProvider {

	public function register() {
		$this->container->register( EmailController::class, function ( $container ) {
			return new EmailController( $container->get( EmailRegistry::class ) );
		} );

		$this->container->register( EmailRegistry::class, function ( $container ) {
			return new EmailRegistry();
		} );
	}

	public function initialize() {
		$this->container->get( EmailController::class )->initialize();
	}

}