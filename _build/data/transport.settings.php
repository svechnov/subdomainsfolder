<?php

$settings = array();

$tmp = array(

//временные
/*
	'assets_path' => array(
		'value' => '{base_path}subdomainsfolder/assets/components/subdomainsfolder/',
		'xtype' => 'textfield',
		'area'  => 'subdomainsfolder_temp',
	),
	'assets_url'  => array(
		'value' => '/subdomainsfolder/assets/components/subdomainsfolder/',
		'xtype' => 'textfield',
		'area'  => 'subdomainsfolder_temp',
	),
	'core_path'   => array(
		'value' => '{base_path}subdomainsfolder/core/components/subdomainsfolder/',
		'xtype' => 'textfield',
		'area'  => 'subdomainsfolder_temp',
	),

*/

	//временные


	/*
	'some_setting' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area'  => 'subdomainsfolder_main',
	),
*/
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key'       => 'subdomainsfolder_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
