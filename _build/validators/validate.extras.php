<?php

/** @var $modx modX */
if (!$modx = $object->xpdo AND !$object->xpdo instanceof modX) {
	return true;
}

$tmp = $modx->getObject('transport.modTransportProvider', array('service_url:LIKE' => '%rstore.pro%'));
if (!$tmp) {
	$tmp = $modx->newObject('transport.modTransportProvider');
	$tmp->fromArray(array(
		'name'        => 'rstore.pro',
		'service_url' => 'http://rstore.pro/extras/',
		'description' => 'Repository of rstore.pro',
	), '', true, true);
	$tmp->save();
}

return true;