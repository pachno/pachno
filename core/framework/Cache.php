<?php

    namespace pachno\core\framework;

    use Exception;
    use pachno\core\entities\Scope;

    /**
     * Cache class
     *
     * @package pachno
     * @subpackage core
     */
    class Cache
    {

        public const KEY_SCOPES = '_scopes';

        public const KEY_INTERNAL_MODULES = '_internal_modules';

        public const KEY_CONFIGURATION = '_configuration';

        public const KEY_ROUTES_CACHE = '_all_routes';

        public const KEY_COMPONENT_OVERRIDE_MAP_CACHE = '_component_override_map';

        public const KEY_ANNOTATION_LISTENERS_CACHE = '_annotation_listeners';

        public const KEY_PERMISSIONS_CACHE = '_permissions';

        public const KEY_MAIN_MENU_LINKS = '_mainmenu_links';

        public const KEY_I18N = '_i18n_';

        public const KEY_TEXTPARSER_ISSUE_REGEX = 'pachno\core\framework\helpers\TextParser::getIssueRegex';

        /**
         * Cache types APC, filesystem (default)
         */
        public const TYPE_APC = 'apc';

        public const TYPE_FILE = 'file';

        protected $_enabled = true;

        protected $_logging = false;

        /**
         * Cache type [apc|file].
         * If APC is present, it will be automatically set to APC [apc].
         * If no opcache present, it will fall back to caching into filesystem [file]
         */
        protected $_type;

        protected $_prefix;

        /**
         * container holding already loaded classes from filesystem so each cached file is loaded only once and later served from memory
         */
        protected $loaded = [];

        public function __construct()
        {
            if (Context::isCLI()) {
                $this->disable();
            } else {
                if (!file_exists(PACHNO_CACHE_PATH))
                    if (!is_writable(dirname(PACHNO_CACHE_PATH))
                        || !mkdir(PACHNO_CACHE_PATH))
                        throw new exceptions\CacheException('The cache directory is not writable', exceptions\CacheException::NO_FOLDER);

                if (!is_writable(PACHNO_CACHE_PATH))
                    throw new exceptions\CacheException('The cache directory is not writable', exceptions\CacheException::NOT_WRITABLE);
            }
        }

        public function disable()
        {
            $this->_enabled = false;
        }

        /**
         * Retrieve an item from the cache
         *
         * @param string $key
         * @param boolean $prepend_scope [optional] Whether to append scope id (for non-global cache keys)
         *
         * @return mixed
         */
        public function get($key, $prepend_scope = true)
        {
            if (!$this->isEnabled())
                return null;

            $success = false;

            switch ($this->_type) {
                case self::TYPE_APC:
                    $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
                    $var = apc_fetch($key, $success);
                    break;
                case self::TYPE_FILE:
                default:
                    $var = $this->fileGet($key, $prepend_scope);
                    $success = !empty($var);
            }

            return ($success) ? $var : null;
        }

        public function isEnabled()
        {
            return (Context::isCLI()) ? false : $this->_enabled;
        }

        protected function getScopedKeyIfAppliccable($key, $prepend_scope)
        {
            $scope_id = (Context::getScope() instanceof Scope) ? Context::getScope()->getID() : '';
            $key = $this->_prefix . $key;

            return ($prepend_scope) ? "{$key}.{$scope_id}" : $key;
        }

        public function fileGet($key, $prepend_scope = true)
        {
            if (!$this->isEnabled())
                return null;

            $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
            if (!$this->fileHas($key, $prepend_scope, true))
                return null;

            if (array_key_exists($key, $this->loaded)) {
                return $this->loaded[$key];
            }

            $filename = $this->_getFilenameForKey($key);
            if (!file_exists($filename))
                throw new Exception("$filename - $key");
            $this->loaded[$key] = unserialize(file_get_contents($filename));

            return $this->loaded[$key];
        }

        public function fileHas($key, $prepend_scope = true, $scoped = false)
        {
            if (!$this->isEnabled())
                return false;

            $key = (!$scoped) ? $this->getScopedKeyIfAppliccable($key, $prepend_scope) : $key;
            $filename = $this->_getFilenameForKey($key);

            return (array_key_exists($key, $this->loaded) || file_exists($filename));
        }

        protected function _getFilenameForKey($key)
        {
            $key = $this->getKeyHash($key);

            return PACHNO_CACHE_PATH . $key . '.cache';
        }

        /**
         * Some keys have insuitable format for filepath, we must purify keys
         * To prevent from accidentally filtering into two the same keys, we must also add hash calculated from original key
         *
         * @param string $key
         */
        protected function getKeyHash($key)
        {
            $key = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $key);

            return $key . '-' . substr(md5(serialize($key)), 0, 5);
        }

        /**
         * Check if an item is in the cache
         *
         * @param string $key
         * @param boolean $prepend_scope [optional] Whether to append scope id (for non-global cache keys)
         *
         * @return boolean
         */
        public function has($key, $prepend_scope = true)
        {
            if (!$this->isEnabled())
                return false;

            $success = false;

            switch ($this->_type) {
                case self::TYPE_APC:
                    $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
                    apc_fetch($key, $success);
                    break;
                case self::TYPE_FILE:
                default:
                    $success = $this->fileHas($key, $prepend_scope);
            }

            return $success;
        }

        public function add($key, $value, $prepend_scope = true)
        {
            if (!$this->isEnabled())
                return false;

            switch ($this->_type) {
                case self::TYPE_APC:
                    $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
                    apc_store($key, $value);
                    break;
                case self::TYPE_FILE:
                default:
                    $this->fileAdd($key, $value, $prepend_scope);
            }

            if ($this->_logging)
                Logging::log('Caching value for key "' . $key . '"', 'cache');

            return true;
        }

        public function fileAdd($key, $value, $prepend_scope = true)
        {
            if (!$this->isEnabled())
                return false;

            $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
            $filename = $this->_getFilenameForKey($key);
            $new = !file_exists($filename);
            file_put_contents($filename, serialize($value));
            if ($new)
                chmod($filename, 0666);
            $this->loaded[$key] = $value;
        }

        public function checkEnabled()
        {
            if ($this->_enabled) {
                $this->_type = function_exists('apc_add') ? self::TYPE_APC : self::TYPE_FILE;
            }
        }

        public function getCacheType()
        {
            return $this->_type;
        }

        public function setPrefix($prefix)
        {
            $this->_prefix = $prefix;
        }

        public function clearCacheKeys($keys)
        {
            foreach ($keys as $key) {
                $this->delete($key);
                $this->fileDelete($key);
            }
        }

        public function delete($key, $prepend_scope = true, $force = false)
        {
            if (!$force && !$this->isEnabled())
                return false;

            switch ($this->_type) {
                case self::TYPE_APC:
                    $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
                    apc_delete($key);
                    break;
                case self::TYPE_FILE:
                default:
                    $this->fileDelete($key, $prepend_scope);
            }
        }

        public function fileDelete($key, $prepend_scope = true, $force = false)
        {
            if (!$force && !$this->isEnabled())
                return false;

            $key = $this->getScopedKeyIfAppliccable($key, $prepend_scope);
            $filename = $this->_getFilenameForKey($key);
            if (file_exists($filename))
                unlink($filename);
            unset($this->loaded[$key]);
        }

    }
