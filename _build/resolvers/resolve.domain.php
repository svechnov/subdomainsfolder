<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
	return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		$modelPath = $modx->getOption('subdomainsfolder_core_path', null, $modx->getOption('core_path') . 'components/subdomainsfolder/') . 'model/';
		$modx->addPackage('subdomainsfolder', $modelPath);

		$domains = array(
			/*'1' => array(
				'domain'   => '9190228211.ru',
				'resource' => '1'
			),*/
			'2' => array(
				'domain'   => 'sub1.9190228211.ru',
				'resource' => '11'
			),
			'3' => array(
				'domain'   => 'sub2.9190228211.ru',
				'resource' => '12'
			),
			'4' => array(
				'domain'   => 'sub3.9190228211.ru',
				'resource' => '18'
			),
		);

		foreach ($domains as $id => $properties) {
			if (!$domain = $modx->getCount('sbdfDomain', array('id' => $id))) {
				$domain = $modx->newObject('sbdfDomain', array_merge(array(
					'editable' => 1,
					'active'   => 1,
					'rank'     => $modx->getCount('sbdfDomain'),
				), $properties));
				$domain->set('id', $id);
				$domain->save();
			}
		}

		break;
	case xPDOTransport::ACTION_UNINSTALL:
		break;
}

return true;
