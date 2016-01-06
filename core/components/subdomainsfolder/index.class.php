<?php

/**
 * Class SubdomainsFolderMainController
 */
abstract class SubdomainsFolderMainController extends modExtraManagerController
{
	/** @var SubdomainsFolder $SubdomainsFolder */
	public $SubdomainsFolder;

	/**
	 * @return void
	 */
	public function initialize()
	{
		$corePath = $this->modx->getOption('subdomainsfolder_core_path', null, $this->modx->getOption('core_path') . 'components/subdomainsfolder/');
		require_once $corePath . 'model/subdomainsfolder/subdomainsfolder.class.php';

		$this->SubdomainsFolder = new SubdomainsFolder($this->modx);
		$this->SubdomainsFolder->initialize($this->modx->context->key);

		$this->SubdomainsFolder->Tools->loadControllerFiles($this, array(
			'css'    => true,
			'config' => true,
			'tools'  => true,
		));

		parent::initialize();
	}

	/**
	 * @return array
	 */
	public function getLanguageTopics()
	{
		return array('SubdomainsFolder:default');
	}

	/**
	 * @return bool
	 */
	public function checkPermissions()
	{
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends SubdomainsFolderMainController
{
	/**
	 * @return string
	 */
	public static function getDefaultController()
	{
		return 'domain';
	}
}