<?php

/**
 * Get a list of sbdfDomain
 */
class modsbdfDomainGetListProcessor extends modObjectGetListProcessor
{
	public $objectType = 'sbdfDomain';
	public $classKey = 'sbdfDomain';
	public $defaultSortField = 'rank';
	public $defaultSortDirection = 'ASC';
	public $languageTopics = array('default', 'subdomainsfolder');
	public $permission = '';


	/** {@inheritDoc} */
	public function beforeQuery()
	{
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		$query = trim($this->getProperty('query'));
		if ($query) {
			$c->where(array(
				'domain:LIKE'           => "%{$query}%",
				'OR:resource:LIKE' => "%{$query}%"
			));
		}

		return $c;
	}

	/** {@inheritDoc} */
	public function outputArray(array $array, $count = false)
	{
		if ($this->getProperty('addall')) {
			$array = array_merge_recursive(array(array(
				'id'   => 0,
				'name' => $this->modx->lexicon('subdomainsfolder_all')
			)), $array);
		}

		return parent::outputArray($array, $count);
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object)
	{
		$icon = 'icon';
		$array = $object->toArray();
		$array['actions'] = array();

		// Edit
		$array['actions'][] = array(
			'cls'    => '',
			'icon'   => "$icon $icon-edit green",
			'title'  => $this->modx->lexicon('subdomainsfolder_action_update'),
			'action' => 'update',
			'button' => true,
			'menu'   => true,
		);

		if (!$array['active']) {
			$array['actions'][] = array(
				'cls'    => '',
				'icon'   => "$icon $icon-toggle-off red",
				'title'  => $this->modx->lexicon('subdomainsfolder_action_active'),
				'action' => 'active',
				'button' => true,
				'menu'   => true,
			);
		} else {
			$array['actions'][] = array(
				'cls'    => '',
				'icon'   => "$icon $icon-toggle-on green",
				'title'  => $this->modx->lexicon('subdomainsfolder_action_inactive'),
				'action' => 'inactive',
				'button' => true,
				'menu'   => true,
			);
		}

		if ($array['editable']) {
			// Remove
			$array['actions'][] = array(
				'cls'    => '',
				'icon'   => "$icon $icon-trash-o red",
				'title'  => $this->modx->lexicon('subdomainsfolder_action_remove'),
				'action' => 'remove',
				'button' => true,
				'menu'   => true,
			);
		}

		return $array;
	}

}

return 'modsbdfDomainGetListProcessor';