<?php

namespace SheerID\Service;

use SheerID\Model\Collection;
use SheerID\Model\SegmentDescription;

class SegmentService extends AbstractService {

	protected $namespace = '/rest/oem';

	/**
	 * @return Collection
	 */
	public function all() {
		return $this->get( $this->buildPath( '/segments' ), [ Collection::class, SegmentDescription::class ] );
	}
}