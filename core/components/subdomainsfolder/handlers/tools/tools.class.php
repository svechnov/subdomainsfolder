<?php


interface SubdomainsFolderToolsInterface
{


}

class Tools implements SubdomainsFolderToolsInterface
{

	/** @var modX $modx */
	protected $modx;
	/** @var SubdomainsFolder $SubdomainsFolder */
	protected $SubdomainsFolder;
	/** @var $namespace */
	protected $namespace;
	/** @var array $config */
	public $config = array();


	/**
	 * @param $modx
	 * @param $config
	 */
	public function __construct($modx, &$config)
	{
		$this->modx = $modx;
		$this->config =& $config;

		$corePath = $this->modx->getOption('subdomainsfolder_core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/subdomainsfolder/');
		/** @var SubdomainsFolder $SubdomainsFolder */
		$this->SubdomainsFolder = $this->modx->getService(
			'SubdomainsFolder',
			'SubdomainsFolder',
			$corePath . 'model/subdomainsfolder/',
			array(
				'core_path' => $corePath
			)
		);

		$this->namespace = $this->SubdomainsFolder->namespace;
	}

	/**
	 * @param       $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}

	/**
	 * @param       $key
	 * @param array $config
	 * @param null  $default
	 *
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
	{
		return $this->SubdomainsFolder->getOption($key, $config, $default, $skipEmpty);
	}


	/**
	 * Sets data to cache
	 *
	 * @param mixed $data
	 * @param mixed $options
	 *
	 * @return string $cacheKey
	 */
	public function setCache($data = array(), $options = array())
	{
		return $this->SubdomainsFolder->setCache($data, $options);
	}

	/**
	 * Returns data from cache
	 *
	 * @param mixed $options
	 *
	 * @return mixed
	 */
	public function getCache($options = array())
	{
		return $this->SubdomainsFolder->getCache($options);
	}


	/**
	 * @param array $options
	 *
	 * @return bool
	 */
	public function clearCache($options = array())
	{
		return $this->SubdomainsFolder->clearCache($options);
	}

	/**
	 * Returns key for cache of specified options
	 *
	 * @var mixed $options
	 * @return bool|string
	 */
	public function getCacheKey($options = array())
	{
		return $this->SubdomainsFolder->getCacheKey($options);
	}

	/** @inheritdoc} */
	public function lexicon($message, $placeholders = array())
	{
		return $this->SubdomainsFolder->lexicon($message, $placeholders);
	}

	/** @inheritdoc} */
	public function failure($message = '', $data = array(), $placeholders = array())
	{
		return $this->SubdomainsFolder->failure($message, $data, $placeholders);
	}

	/** @inheritdoc} */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		return $this->SubdomainsFolder->success($message, $data, $placeholders);
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array
	 */
	public function explodeAndClean($array, $delimiter = ',')
	{
		return $this->SubdomainsFolder->explodeAndClean($array, $delimiter);
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array|string
	 */
	public function cleanAndImplode($array, $delimiter = ',')
	{
		return $this->SubdomainsFolder->cleanAndImplode($array, $delimiter);
	}


	public function getDomains()
	{
		/* array cache $options */
		$options = array(
			'cache_key' => 'config/domains',
			'cacheTime' => 0,
		);
		if (!$domains = $this->getCache($options)) {
			$q = $this->modx->newQuery('sbdfDomain');
			$q->innerJoin('modResource', 'modResource', 'modResource.id = sbdfDomain.resource');
			$q->select('sbdfDomain.domain,modResource.id,modResource.uri');
			$q->where('sbdfDomain.active=1');
			if ($q->prepare() AND $q->stmt->execute()) {
				$rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$domains[$row['domain']] = array(
						'id'    => $row['id'],
						'alias' => trim($row['uri'], '/'),
					);
				}
			}
			$this->setCache($domains, $options);
		}

		return (array)$domains;
	}

	/** @return array Window Update Client Tabs */
	public function getDomainGridFields()
	{
		$windowTabs = $this->getOption('fields_grid_domain', null,
			'id,domain,resource,active,rank,editable', true);
		$windowTabs .= ',id,active,rank,editable,properties,actions';
		$windowTabs = $this->explodeAndClean($windowTabs);

		return $windowTabs;
	}

	/**
	 * @param modManagerController $controller
	 * @param array                $opts
	 */
	public function loadControllerFiles(modManagerController $controller, array $opts = array())
	{
		$config = $this->config;
		$config['connector_url'] = $this->config['connectorUrl'];
		$config['fields_grid_domain'] = $this->getDomainGridFields();

		if (!empty($opts['css'])) {
			$controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
			$controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		}

		if (!empty($opts['config'])) {
			$controller->addHtml("<script type='text/javascript'>subdomainsfolder.config={$this->modx->toJSON($config)}</script>");
		}

		if (!empty($opts['tools'])) {
			$controller->addJavascript($this->config['jsUrl'] . 'mgr/subdomainsfolder.js');
			$controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
			$controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/combo.js');
		}

		if (!empty($opts['domain'])) {
			$controller->addLastJavascript($this->config['jsUrl'] . 'mgr/domain/domain.window.js');
			$controller->addLastJavascript($this->config['jsUrl'] . 'mgr/domain/domain.grid.js');
			$controller->addLastJavascript($this->config['jsUrl'] . 'mgr/domain/domain.panel.js');
		}

	}


}