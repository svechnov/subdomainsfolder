<?php

/**
 * Create an sbdfDomain
 */
class modsbdfDomainCreateProcessor extends modObjectCreateProcessor
{
	public $objectType = 'sbdfDomain';
	public $classKey = 'sbdfDomain';
	public $languageTopics = array('subdomainsfolder');
	public $permission = '';

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
	public function beforeSave()
	{
		$this->object->fromArray(array(
			'rank'     => $this->modx->getCount($this->classKey),
			'editable' => true
		));

		return parent::beforeSave();
	}

	/** {@inheritDoc} */
	public function afterSave() {
		$this->modx->SubdomainsFolder->clearCache();

		return parent::afterSave();
	}

}

return 'modsbdfDomainCreateProcessor';