<?php

namespace SheerID\Utils;

use SheerID\Model\Audience;
use SheerID\Model\DisplayInfo;
use SheerID\Model\LastResponse;
use SheerID\Model\Metadata;
use SheerID\Model\Organization;
use SheerID\Model\PersonInfo;
use SheerID\Model\Program;
use SheerID\Model\SegmentDescription;
use SheerID\Model\Webhook;

class ModelTypes {

	public static $types = [
		Metadata::MODEL_TYPE           => Metadata::class,
		Organization::MODEL_TYPE       => Organization::class,
		Program::MODEL_TYPE            => Program::class,
		SegmentDescription::MODEL_TYPE => SegmentDescription::class,
		DisplayInfo::MODEL_TYPE        => DisplayInfo::class,
		LastResponse::MODEL_TYPE       => LastResponse::class,
		PersonInfo::MODEL_TYPE         => PersonInfo::class,
		Audience::MODEL_TYPE           => Audience::class,
		Webhook::MODEL_TYPE            => Webhook::class
	];
}