<?php

namespace SheerID\Service;

use SheerID\Model\Collection;
use SheerID\Model\Webhook;

class WebhookService extends AbstractService {

	protected $namespace = '/rest/oem/program/%s';

	/**
	 * @param $programId
	 * @param $params
	 * @param $opts
	 *
	 * @return Collection
	 */
	public function create( $programId, $params, $opts = null ) {
		return $this->post( $this->buildPath( '/webhooks', $programId ), [
			Collection::class,
			Webhook::class
		], $params, $opts );
	}

	public function retrieve( $programId, $webhookId, $opts = null ) {
		return $this->get( $this->buildPath( '/webhooks/%s', $programId, $webhookId ), Webhook::class, null, $opts );
	}

	public function update( $programId, $id, $params, $opts = null ) {
		return $this->post( $this->buildPath( '/webhooks/%s', $programId, $id ), Webhook::class, $params, $opts );
	}

	/**
	 * @param string $programId
	 * @param $opts
	 *
	 * @return Collection
	 */
	public function all( $programId, $opts = null ) {
		return $this->get( $this->buildPath( '/webhooks', $programId ), [
			Collection::class,
			Webhook::class
		], null, $opts );
	}

	public function delete( $programId, $webhookId, $opts = null ) {
		return $this->request( 'DELETE', $this->buildPath( '/webhooks/%s', $programId, $webhookId ), Webhook::class, null, $opts );
	}
}