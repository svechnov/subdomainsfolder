<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (!class_exists('modRequest')) {
	require_once MODX_CORE_PATH . 'model/modx/modrequest.class.php';
}

class SubdomainsFolderRequest extends modRequest
{
	/** @var SubdomainsFolder $SubdomainsFolder */
	public $SubdomainsFolder;
	public $domains;

	function __construct(xPDO &$modx)
	{
		$modx = new SubdomainsFolderModX();
		$modx->initialize('web');
		$this->SubdomainsFolder = &$modx->SubdomainsFolder;
		$this->domains = &$modx->domains;

		parent::__construct($modx);
	}

	public function handleRequest()
	{
		$this->loadErrorHandler();
		$this->sanitizeRequest();
		$this->modx->invokeEvent('OnHandleRequest');
		if (!$this->modx->checkSiteStatus()) {
			header('HTTP/1.1 503 Service Unavailable');
			if (!$this->modx->getOption('site_unavailable_page', null, 1)) {
				$this->modx->resource = $this->modx->newObject('modDocument');
				$this->modx->resource->template = 0;
				$this->modx->resource->content = $this->modx->getOption('site_unavailable_message');
			} else {
				$this->modx->resourceMethod = "id";
				$this->modx->resourceIdentifier = $this->modx->getOption('site_unavailable_page', null, 1);
			}
		} else {
			$this->checkPublishStatus();
			$this->modx->resourceMethod = $this->getResourceMethod();
			$this->modx->resourceIdentifier = $this->getResourceIdentifier($this->modx->resourceMethod);
			if ($this->modx->resourceMethod == 'id' AND $this->modx->getOption('friendly_urls', null, false) AND $this->modx->getOption('request_method_strict', null, false)) {
				$uri = $this->modx->context->getResourceURI($this->modx->resourceIdentifier);
				if (!empty($uri)) {
					if ((integer)$this->modx->resourceIdentifier === (integer)$this->modx->getOption('site_start', null, 1)) {
						$url = $this->modx->getOption('site_url', null, MODX_SITE_URL);
					} else {
						$url = $this->modx->getOption('site_url', null, MODX_SITE_URL) . $uri;
					}
					$this->modx->sendRedirect($url, array('responseCode' => 'HTTP/1.1 301 Moved Permanently'));
				}
			}
		}

		if (empty ($this->modx->resourceMethod)) {
			$this->modx->resourceMethod = "id";
		}
		if ($this->modx->resourceMethod == "alias") {
			$this->modx->resourceIdentifier = $this->_cleanResourceIdentifier($this->modx->resourceIdentifier);
		}

		$urlScheme = $this->modx->getOption('url_scheme', null, 'http://');
		$domainHost = $this->modx->getOption('http_host', null, MODX_HTTP_HOST);

		switch (true) {
			case !in_array(MODX_HTTP_HOST, array_keys($this->domains)) AND MODX_HTTP_HOST != $domainHost:
				$this->modx->sendRedirect($urlScheme . $domainHost, array('responseCode' => 'HTTP/1.1 301 Moved Permanently'));
				break;
			case !in_array(MODX_HTTP_HOST, array_keys($this->domains)) AND MODX_HTTP_HOST == $domainHost:
				break;
			case $this->modx->resourceMethod == 'id':
				$this->modx->resourceIdentifier = $this->domains[MODX_HTTP_HOST]['id'];
				break;
			case $this->modx->resourceMethod == 'alias':
				$this->modx->resourceIdentifier = rtrim($this->modx->resourceIdentifier, '/');
				$this->modx->resourceIdentifier = $this->domains[MODX_HTTP_HOST]['alias'] . '/' . $this->modx->resourceIdentifier;
				break;
		}

		if (isset($this->domains[MODX_HTTP_HOST])) {
			$this->modx->setPlaceholders($this->domains[MODX_HTTP_HOST], 'sbdf.');
		}

		if ($this->modx->resourceMethod == "alias") {
			$found = $this->modx->findResource($this->modx->resourceIdentifier);
			if ($found) {
				$this->modx->resourceIdentifier = $found;
				$this->modx->resourceMethod = 'id';
			} else {
				$this->modx->sendRedirect('/', array('responseCode' => 'HTTP/1.1 301 Moved Permanently'));
			}
		}

		$this->modx->beforeRequest();
		$this->modx->invokeEvent("OnWebPageInit");
		if (!is_object($this->modx->resource)) {
			if (!$this->modx->resource = $this->getResource($this->modx->resourceMethod, $this->modx->resourceIdentifier)) {
				$this->modx->sendErrorPage();

				return true;
			}
		}

		return $this->prepareResponse();
	}


	public function getResource($method, $identifier, array $options = array())
	{
		$resource = parent::getResource($method, $identifier, $options);


		if (!$resource AND $this->modx->user->isAuthenticated('mgr')) {
			$resource = $this->modx->getObject('modResource', (int)$this->modx->resourceIdentifier);
		}

		if (!$resource) {
			$this->modx->sendRedirect('/', array('responseCode' => 'HTTP/1.1 301 Moved Permanently'));
		}

		return $resource;
	}

}

class SubdomainsFolderModX extends modX
{

	/** @var SubdomainsFolder $SubdomainsFolder */
	public $SubdomainsFolder;
	public $domains;

	public function initialize($contextKey = 'web', $options = null)
	{

		parent::initialize($contextKey, $options);
		$corePath = $this->getOption('subdomainsfolder_core_path', null, $this->getOption('core_path', null, MODX_CORE_PATH) . 'components/subdomainsfolder/');
		$this->SubdomainsFolder = $this->getService('SubdomainsFolder', 'SubdomainsFolder', $corePath . 'model/subdomainsfolder/');
		$this->SubdomainsFolder->initialize($this->context->key);
		$this->domains = $this->SubdomainsFolder->Tools->getDomains();
	}

	public function makeUrl($id, $context = '', $args = '', $scheme = -1, array $options = array())
	{
		$url = '';
		if ($validid = intval($id)) {
			$id = $validid;
			if ($context == '' || $this->context->get('key') == $context) {
				$url = $this->context->makeUrl($id, $args, $scheme, $options);
			}
			if (empty($url) AND ($context !== $this->context->get('key'))) {
				$ctx = null;
				if ($context == '') {
					/** @var PDOStatement $stmt */
					if ($stmt = $this->prepare("SELECT context_key FROM " . $this->getTableName('modResource') . " WHERE id = :id")) {
						$stmt->bindValue(':id', $id);
						if ($contextKey = $this->getValue($stmt)) {
							$ctx = $this->getContext($contextKey);
						}
					}
				} else {
					$ctx = $this->getContext($context);
				}
				if ($ctx) {
					$url = $ctx->makeUrl($id, $args, 'full', $options);
				}
			}

			if (!empty($url) AND $this->getOption('xhtml_urls', $options, false)) {
				$url = preg_replace("/&(?!amp;)/", "&amp;", $url);
			}

			if (in_array(MODX_HTTP_HOST, array_keys($this->domains))) {
				$url = str_replace($this->domains[MODX_HTTP_HOST]['alias'], '', $url);
			}

		} else {
			$this->log(modX::LOG_LEVEL_ERROR, '`' . $id . '` is not a valid integer and may not be passed to makeUrl()');
		}

		return $url;
	}

	public function _postProcess()
	{
		if ($this->resourceGenerated AND $this->getOption('cache_resource', null, true)) {
			if (
				is_object($this->resource) AND
				$this->resource instanceof modResource AND
				$this->resource->get('id') AND
				$this->resource->get('cacheable') AND
				in_array(MODX_HTTP_HOST, array_keys($this->domains))
			) {
				$this->resource->_contextKey = $this->context->get('key');
				$this->invokeEvent('OnBeforeSaveWebPageCache');
				$this->cacheManager->generateResource($this->resource);
			}
		}
		$this->invokeEvent('OnWebPageComplete');
	}

}
