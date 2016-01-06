<?php

/**
 * The base class for subdomainsfolder.
 */
class SubdomainsFolder
{
	/** @var modX $modx */
	public $modx;
	/** @var mixed|null $namespace */
	public $namespace = 'subdomainsfolder';
	/** @var array $config */
	public $config = array();
	/** @var array $initialized */
	public $initialized = array();
	/** @var Tools $Tools */
	public $Tools;


	/**
	 * @param modX  $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;

		$corePath = $this->getOption('core_path', $config, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/subdomainsfolder/');
		$assetsPath = $this->getOption('assets_path', $config, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/subdomainsfolder/');
		$assetsUrl = $this->getOption('assets_url', $config, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/subdomainsfolder/');

		$this->config = array_merge(array(
			'namespace'      => $this->namespace,
			'assetsPath'     => $assetsPath,
			'assetsUrl'      => $assetsUrl,
			'cssUrl'         => $assetsUrl . 'css/',
			'jsUrl'          => $assetsUrl . 'js/',
			'imagesUrl'      => $assetsUrl . 'images/',
			'connectorUrl'   => $assetsUrl . 'connector.php',
			'actionUrl'      => $assetsUrl . 'action.php',

			'corePath'       => $corePath,
			'modelPath'      => $corePath . 'model/',
			'chunksPath'     => $corePath . 'elements/chunks/',
			'templatesPath'  => $corePath . 'elements/templates/',
			'snippetsPath'   => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',
			'handlersPath'   => $corePath . 'handlers/'
		), $config);

		$this->modx->addPackage('subdomainsfolder', $this->config['modelPath']);
		$this->modx->lexicon->load('subdomainsfolder:default');
		$this->namespace = $this->getOption('namespace', $config, 'subdomainsfolder');
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
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}
		if ($skipEmpty AND empty($option)) {
			$option = $default;
		}

		return $option;
	}

	public function initialize($ctx = 'web', $scriptProperties = array())
	{
		if (!$this->Tools) {
			$this->loadTools();
		}

		if (!empty($this->initialized[$ctx])) {
			return true;
		}

		switch ($ctx) {
			case 'mgr':
				break;
			default:
				if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {

					$this->initialized[$ctx] = true;

				}
				break;
		}

		return true;
	}

	/**
	 * Loads an instance of Tools
	 *
	 * @return boolean
	 */
	public function loadTools()
	{
		if (!is_object($this->Tools) OR !($this->Tools instanceof SubdomainsFolderToolsInterface)) {
			$toolsClass = $this->modx->loadClass('tools.Tools', $this->config['handlersPath'], true, true);
			if ($derivedClass = $this->getOption('class_tools_handler', null, '')) {
				if ($derivedClass = $this->modx->loadClass('tools.' . $derivedClass, $this->config['handlersPath'], false, true)) {
					$toolsClass = $derivedClass;
				}
			}
			if ($toolsClass) {
				$this->Tools = new $toolsClass($this->modx, $this->config);
			}
		}

		return !empty($this->Tools) AND $this->Tools instanceof SubdomainsFolderToolsInterface;
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array
	 */
	public function explodeAndClean($array, $delimiter = ',')
	{
		$array = explode($delimiter, $array);     // Explode fields to array
		$array = array_map('trim', $array);       // Trim array's values
		$array = array_keys(array_flip($array));  // Remove duplicate fields
		$array = array_filter($array);            // Remove empty values from array
		return $array;
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array|string
	 */
	public function cleanAndImplode($array, $delimiter = ',')
	{
		$array = array_map('trim', $array);       // Trim array's values
		$array = array_keys(array_flip($array));  // Remove duplicate fields
		$array = array_filter($array);            // Remove empty values from array
		$array = implode($delimiter, $array);

		return $array;
	}

	/**
	 * return lexicon message if possibly
	 *
	 * @param string $message
	 *
	 * @return string $message
	 */
	public function lexicon($message, $placeholders = array())
	{
		$key = '';
		if ($this->modx->lexicon->exists($message)) {
			$key = $message;
		} elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
			$key = $this->namespace . '_' . $message;
		}
		if ($key !== '') {
			$message = $this->modx->lexicon->process($key, $placeholders);
		}

		return $message;
	}

	/**
	 * @param string $message
	 * @param array  $data
	 * @param array  $placeholders
	 *
	 * @return array|string
	 */
	public function failure($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => false,
			'message' => $this->lexicon($message, $placeholders),
			'data'    => $data,
		);

		return $this->config['jsonResponse']
			? $this->modx->toJSON($response)
			: $response;
	}

	/**
	 * @param string $message
	 * @param array  $data
	 * @param array  $placeholders
	 *
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => true,
			'message' => $this->lexicon($message, $placeholders),
			'data'    => $data,
		);

		return $this->config['jsonResponse']
			? $this->modx->toJSON($response)
			: $response;
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
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
			$this->modx->cacheManager->set(
				$cacheKey,
				$data,
				$cacheOptions[xPDO::OPT_CACHE_EXPIRES],
				$cacheOptions
			);
		}

		return $cacheKey;
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
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		$cached = '';
		if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
			$cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
		}

		return $cached;
	}

	/**
	 * @param array $options
	 *
	 * @return bool
	 */
	public function clearCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey)) {
			$cacheOptions['cache_key'] .= $cacheKey;
		}
		if (!empty($cacheOptions) AND $this->modx->getCacheManager()) {
			return $this->modx->cacheManager->clean($cacheOptions);
		}

		return false;
	}

	/**
	 * Returns array with options for cache
	 *
	 * @param $options
	 *
	 * @return array
	 */
	protected function getCacheOptions($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		$cacheOptions = array(
			xPDO::OPT_CACHE_KEY     => empty($options['cache_key'])
				? 'default'
				: 'default/' . $this->namespace . '/',
			xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
				? $options['cache_handler']
				: $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
			xPDO::OPT_CACHE_EXPIRES => (isset($options['cacheTime']) AND $options['cacheTime'] !== '')
				? (integer)$options['cacheTime']
				: (integer)$this->modx->getOption('cache_resource_expires', null, 0),
		);

		return $cacheOptions;
	}

	/**
	 * Returns key for cache of specified options
	 *
	 * @var mixed $options
	 *
	 * @return bool|string
	 */
	public function getCacheKey($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		if (!empty($options['cache_key'])) {
			return $options['cache_key'];
		}
		$key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';

		return $key . '/' . sha1(serialize($options));
	}

}