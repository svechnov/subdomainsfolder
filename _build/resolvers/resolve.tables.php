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

		$manager = $modx->getManager();
		$objects = array(
			'sbdfDomain',
		);
		foreach ($objects as $tmp) {
			$manager->createObjectContainer($tmp);
		}
		break;
	case xPDOTransport::ACTION_UNINSTALL:
		break;
}

return true;
