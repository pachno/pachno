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
    class FileCache extends Cache
    {

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
        
        public function getCacheType(): string
        {
            return Cache::DRIVER_FILESYSTEM;
        }
    
        /**
         * @param string $key
         * @return string
         */
        protected function getFilenameForKey(string $key): string
        {
            $key = $this->getKeyHash($key);
        
            return PACHNO_CACHE_PATH . $key . '.cache';
        }
    
        /**
         * Some keys have unsuitable format for filepath, we must purify keys
         * To prevent from accidentally filtering into two the same keys, we must also add hash calculated from original key
         *
         * @param string $key
         *
         * @return string
         */
        protected function getKeyHash(string $key): string
        {
            $key = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $key);
        
            return $key . '-' . substr(md5(serialize($key)), 0, 5);
        }
    
        /**
         * Retrieve an item from the cache
         *
         * @param string $key Unique key of the element to look up
         * @param bool $prepend_scope Whether to append scope id (for non-global cache keys)
         *
         * @return ?mixed
         */
        public function get(string $key, bool $prepend_scope = true)
        {
            if (!$this->isEnabled())
                return null;
    
            if (!$this->has($key, $prepend_scope))
                return null;

            $key = $this->getScopedKeyIfApplicable($key, $prepend_scope);
    
            if (array_key_exists($key, $this->loaded)) {
                return $this->loaded[$key];
            }
    
            $filename = $this->getFilenameForKey($key);
            if (!file_exists($filename))
                throw new Exception("$filename - $key");
            
            $this->loaded[$key] = unserialize(file_get_contents($filename));
    
            return $this->loaded[$key];
        }

        public function has(string $key, bool $prepend_scope = true): bool
        {
            if (!$this->isEnabled())
                return false;
    
            $key = $this->getScopedKeyIfApplicable($key, $prepend_scope);
            $filename = $this->getFilenameForKey($key);
    
            return (array_key_exists($key, $this->loaded) || file_exists($filename));
        }

        public function add(string $key, $value, bool $prepend_scope = true): bool
        {
            if (!$this->isEnabled())
                return false;
    
            $key = $this->getScopedKeyIfApplicable($key, $prepend_scope);
            $filename = $this->getFilenameForKey($key);
            $new = !file_exists($filename);
            file_put_contents($filename, serialize($value));
            if ($new)
                chmod($filename, 0666);
    
            $this->loaded[$key] = $value;

            return true;
        }

        public function delete(string $key, bool $prepend_scope = true, bool $force = false): void
        {
            if (!$force && !$this->isEnabled())
                return;
    
            $key = $this->getScopedKeyIfApplicable($key, $prepend_scope);
            $filename = $this->getFilenameForKey($key);
            
            if (file_exists($filename)) {
                unlink($filename);
            }

            unset($this->loaded[$key]);
        }

    }
