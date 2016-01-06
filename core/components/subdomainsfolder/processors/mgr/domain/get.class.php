<?php

/**
 * Get an sbdfDomain
 */
class modsbdfDomainGetProcessor extends modObjectGetProcessor {
	public $objectType = 'sbdfDomain';
	public $classKey = 'sbdfDomain';
	public $languageTopics = array('subdomainsfolder');
	public $permission = '';

	/** {@inheritDoc} */
	public function process() {
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		return parent::process();
	}

}

return 'modsbdfDomainGetProcessor';