<?php

/**
 * Remove a sbdfDomain
 */
class modsbdfDomainRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'sbdfDomain';
	public $languageTopics = array('subdomainsfolder');
	public $permission = '';

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeRemove()
	{
		if (!$this->object->get('editable')) {
			$this->failure($this->modx->lexicon('subdomainsfolder_err_lock'));
		}
		return parent::beforeRemove();
	}
}

return 'modsbdfDomainRemoveProcessor';