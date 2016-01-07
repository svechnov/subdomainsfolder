<?php

/**
 * The base class for modModX.
 */
class modModX extends modX
{
	/** @var SubdomainsFolder $SubdomainsFolder */
	public $SubdomainsFolder;
	public $domains;

	public function initialize($contextKey = 'web', $options = null)
	{
		parent::initialize($contextKey, $options);
		$corePath = $this->getOption('subdomainsfolder_core_path', null, $this->getOption('core_path', null, MODX_CORE_PATH) . 'components/subdomainsfolder/');
		$this->SubdomainsFolder = $this->getService('SubdomainsFolder', 'SubdomainsFolder', $corePath . 'model/subdomainsfolder/');
		if ($this->SubdomainsFolder) {
			$this->SubdomainsFolder->initialize($this->context->key);
			$this->domains = $this->SubdomainsFolder->Tools->getDomains();
		} else {
			$this->log(modX::LOG_LEVEL_ERROR, 'modModX requires installed SubdomainsFolder.');
		}
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
				(
				in_array(MODX_HTTP_HOST, array_keys($this->domains))
				)
			) {
				$this->resource->_contextKey = $this->context->get('key');
				$this->invokeEvent('OnBeforeSaveWebPageCache');
				$this->cacheManager->generateResource($this->resource);
			}
		}
		$this->invokeEvent('OnWebPageComplete');
	}

}