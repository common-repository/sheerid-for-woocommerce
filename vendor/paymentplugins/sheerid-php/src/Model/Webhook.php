<?php

namespace SheerID\Model;

/**
 * @property string $id
 * @property string $callbackUri
 * @property string $type
 * @property string webhookStatus
 */
class Webhook extends AbstractModel {

	const MODEL_TYPE = 'webhook';

	const TEST = 'TEST';

	const SUCCESS = 'SUCCESS';

	const FAILURE = 'FAILURE';

	const PROGRAM_CHANGE = 'PROGRAM_CHANGE';

	const REMINDER = 'REMINDER';

	const EMAIL_LOOP = 'EMAIL_LOOP';

	const NEED_MORE_DOCS = 'NEED_MORE_DOCS';

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCallbackUri() {
		return $this->callbackUri;
	}

	/**
	 * @param string $callbackUri
	 */
	public function setCallbackUri( $callbackUri ) {
		$this->callbackUri = $callbackUri;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType( $type ) {
		$this->type = $type;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getWebhookStatus() {
		return $this->webhookStatus;
	}

	/**
	 * @param string $webhookStatus
	 */
	public function setWebhookStatus( $webhookStatus ) {
		$this->webhookStatus = $webhookStatus;

		return $this;
	}
}