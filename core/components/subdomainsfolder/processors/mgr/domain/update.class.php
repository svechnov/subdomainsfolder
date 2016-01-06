<?php

/**
 * Update an sbdfDomain
 */
class modsbdfDomainUpdateProcessor extends modObjectUpdateProcessor
{
	public $objectType = 'sbdfDomain';
	public $classKey = 'sbdfDomain';
	public $languageTopics = array('subdomainsfolder');
	public $permission = '';


	/** {@inheritDoc} */
	public function beforeSave()
	{
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}

	/** {@inheritDoc} */
	public function beforeSet()
	{
		foreach (array('domain', 'resource') as $key) {
			${$key} = trim($this->getProperty($key));
			if (empty(${$key})) {
				$this->modx->error->addField($key, $this->modx->lexicon('subdomainsfolder_err_ae'));
			}
		}

		return parent::beforeSet();
	}

	/** {@inheritDoc} */
	public function afterSave() {
		$this->modx->SubdomainsFolder->clearCache();

		return parent::afterSave();
	}
}

return 'modsbdfDomainUpdateProcessor';
