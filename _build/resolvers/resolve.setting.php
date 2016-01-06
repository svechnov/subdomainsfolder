<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
	return true;
}

/** @var $options */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		/** @var modContextSetting $tmp */
		if (!$tmp = $modx->getObject('modContextSetting', array('context_key' => 'web', 'key' => 'modRequest.class'))) {
			$tmp = $modx->newObject('modContextSetting');
		}
		$tmp->fromArray(array(
			'context_key' => 'web',
			'key'         => 'modRequest.class',
			'xtype'       => 'textfield',
			'namespace'   => 'subdomainsfolder',
			'area'        => 'subdomainsfolder_main',
			'value'       => 'subdomainsFolderRequest',
		), '', true, true);
		$tmp->save();

		if (!$tmp = $modx->getObject('modContextSetting', array('context_key' => 'web', 'key' => 'http_host'))) {
			$tmp = $modx->newObject('modContextSetting');
		}
		$tmp->fromArray(array(
			'context_key' => 'web',
			'key'         => 'http_host',
			'xtype'       => 'textfield',
			'namespace'   => 'subdomainsfolder',
			'area'        => 'subdomainsfolder_main',
			'value'       => MODX_HTTP_HOST,
		), '', true, true);
		$tmp->save();

		//site_url
		break;
	case xPDOTransport::ACTION_UNINSTALL:
		$modx->removeCollection('modSystemSetting', array('area' => 'subdomainsfolder_main'));
		$modx->removeCollection('modContextSetting', array('area' => 'subdomainsfolder_main'));
		break;
}

return true;