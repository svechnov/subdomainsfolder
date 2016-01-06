<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

/**
 * Class ControllersDomainManagerController
 */
class ControllersDomainManagerController extends SubdomainsFolderMainController
{

	/**
	 * @return string
	 */
	public static function getDefaultController()
	{
		return 'domain';
	}

}

/**
 * Class SubdomainsFolderDomainManagerController
 */
class SubdomainsFolderDomainManagerController extends SubdomainsFolderMainController
{

	/**
	 * @return string
	 */
	public function getPageTitle()
	{
		return $this->modx->lexicon('subdomainsfolder') . ' :: ' . $this->modx->lexicon('subdomainsfolder_domain');
	}

	/**
	 * @return array
	 */
	public function getLanguageTopics()
	{
		return array('subdomainsfolder:default');
	}

	/**
	 *
	 */
	public function loadCustomCssJs()
	{
		$this->SubdomainsFolder->Tools->loadControllerFiles($this, array(
			'domain'  => true,
		));

		$script = 'Ext.onReady(function() {
			MODx.add({ xtype: "subdomainsfolder-panel-domain"});
		});';
		$this->addHtml("<script type='text/javascript'>{$script}</script>");

		$this->modx->invokeEvent('SubdomainsFolderOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'domain'));
	}

	/**
	 * @return string
	 */
	public function getTemplateFile()
	{
		return $this->SubdomainsFolder->config['templatesPath'] . 'domain.tpl';
	}

}

// MODX 2.3
/**
 * Class ControllersMgrDomainManagerController
 */
class ControllersMgrDomainManagerController extends ControllersDomainManagerController
{

	/**
	 * @return string
	 */
	public static function getDefaultController()
	{
		return 'domain';
	}

}

/**
 * Class SubdomainsFolderMgrDomainManagerController
 */
class SubdomainsFolderMgrDomainManagerController extends SubdomainsFolderDomainManagerController
{

}
