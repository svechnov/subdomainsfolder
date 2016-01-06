<?php

/**
 * SetProperty a sbdfDomain
 */
class modsbdfDomainSetPropertyProcessor extends modObjectUpdateProcessor
{
	/** @var sbdfDomain $object */
	public $object;
	public $objectType = 'sbdfDomain';
	public $classKey = 'sbdfDomain';
	public $languageTopics = array('subdomainsfolder');
	public $permission = '';

	/** {@inheritDoc} */
	public function beforeSet()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);

		$this->properties = array();

		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$this->setProperty('field_name', $fieldName);
			$this->setProperty('field_value', $fieldValue);
		}

		return true;
	}

	/** {@inheritDoc} */
	public function beforeSave()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);
		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$array = $this->object->toArray();
			if (isset($array[$fieldName])) {
				$this->object->fromArray(array(
					$fieldName => $fieldValue,
				));
			}
		}

		return parent::beforeSave();
	}

	/** {@inheritDoc} */
	public function afterSave() {
		$this->modx->SubdomainsFolder->clearCache();

		return parent::afterSave();
	}

}

return 'modsbdfDomainSetPropertyProcessor';