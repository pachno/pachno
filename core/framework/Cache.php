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
    abstract class Cache
    {

        public const DRIVER_APCU = 'apcu';

        public const DRIVER_FILESYSTEM = 'filesystem';

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

        protected bool $_enabled = true;

        protected bool $_logging = false;

        protected string $_type = self::DRIVER_FILESYSTEM;

        protected string $_prefix = '';

        /**
         * container holding entries already cached
         * @var array<int, mixed>
         */
        protected array $loaded = [];

        abstract public function __construct();
    
        /**
         * Retrieve an item from the cache
         *
         * @param string $key Unique key of the element to look up
         * @param bool $prepend_scope Whether to append scope id (for non-global cache keys)
         *
         * @return ?mixed
         */
        abstract public function get(string $key, bool $prepend_scope = true);
    
        abstract public function getCacheType(): string;
    
        /**
         * Check if an item is in the cache
         *
         * @param string $key Unique key of the element to look up
         * @param bool $prepend_scope Whether to append scope id (for non-global cache keys)
         *
         * @return bool
         */
        abstract public function has(string $key, bool $prepend_scope = true): bool;
    
        /**
         * @param string $key
         * @param mixed $value
         * @param bool $prepend_scope [optional] Whether to append scope id (for non-global cache keys)
         *
         * @return bool
         */
        abstract public function add(string $key, $value, bool $prepend_scope = true): bool;

        /**
         * @param string $key Unique key of the element to look up
         * @param bool $prepend_scope [optional] Whether to append scope id (for non-global cache keys)
         * @param bool $force
         *
         * @return void
         */
        abstract public function delete(string $key, bool $prepend_scope = true, bool $force = false): void;
    
        /**
         * @param string $key The cache key to scope
         * @param bool $prepend_scope Whether to prepend scope to the cache key
         *
         * @return string
         */
        protected function getScopedKeyIfApplicable(string $key, bool $prepend_scope): string
        {
            $scope_id = (Context::getScope() instanceof Scope) ? Context::getScope()->getID() : '';
            $key = $this->_prefix . $key;
        
            return ($prepend_scope) ? "{$key}.{$scope_id}" : $key;
        }
    
        /**
         * Enable or disable the caching
         *
         * @return void
         */
        public function disable(): void
        {
            $this->_enabled = false;
        }
    
        /**
         * Returns whether the cache is enabled or not
         *
         * @return bool
         */
        public function isEnabled(): bool
        {
            if (Context::isCLI()) {
                return false;
            }
        
            return $this->_enabled;
        }
    
        /**
         * Sets a global cache key prefix used when storing elements in the cache
         *
         * @param string $prefix
         * @return void
         */
        public function setPrefix(string $prefix): void
        {
            $this->_prefix = $prefix;
        }
    
        /**
         * Clears a specific list of cache key entries
         *
         * @param string[] $keys List of keys to clear
         *
         * @return void
         */
        public function clearCacheKeys(array $keys): void
        {
            foreach ($keys as $key) {
                $this->delete($key);
            }
        }

    }
