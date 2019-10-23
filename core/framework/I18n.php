<?php

    namespace pachno\core\framework;

    use DateTime;
    use DateTimeZone;
    use DOMDocument;
    use Exception;

    /**
     * I18n class
     *
     * @package pachno
     * @subpackage core
     */
    class I18n
    {

        protected $_strings = null;

        protected $_missing_strings = [];

        protected $_language = null;

        protected $_charset = 'utf-8';

        protected $_datetime_formats = [];

        public function __construct($language)
        {
            if (!file_exists($this->getStringsFilename($language))) {
                Logging::log('Selected language not available, trying "en_US" as a last attempt', 'i18n', Logging::LEVEL_NOTICE);
                $this->_language = 'en_US';
                if (!file_exists($this->getStringsFilename($this->_language))) {
                    throw new Exception('The selected language is not available');
                }
            }
            $this->_language = $language;
        }

        public function getStringsFilename($language = null)
        {
            $language = ($language === null) ? $this->_language : $language;

            return PACHNO_PATH . 'i18n' . DS . $language . DS . 'strings.xlf';
        }

        public static function getTimezones()
        {
            $offsets = $options = [];
            $now = new DateTime();
            foreach (DateTimeZone::listIdentifiers() as $tz) {
                $now->setTimezone(new DateTimeZone($tz));
                $offsets[] = $offset = $now->getOffset();
                $hours = intval($offset / 3600);
                $minutes = abs(intval($offset % 3600 / 60));
                $hm = $offset ? sprintf('%+03d:%02d', $hours, $minutes) : '';
                $name = str_replace('_', ' ', $tz);
                $name = str_replace('St ', 'St. ', $tz);
                $options[$tz] = "GMT$hm $name";
            }

            array_multisort($offsets, $options);

            return $options;
        }

        public static function getLanguages()
        {
            $retarr = [];
            $cp_handle = opendir(PACHNO_PATH . 'i18n');
            while ($classfile = readdir($cp_handle)) {
                if (mb_strstr($classfile, '.') == '' && file_exists(PACHNO_PATH . 'i18n/' . $classfile . '/language')) {
                    $retarr[$classfile] = file_get_contents(PACHNO_PATH . 'i18n/' . $classfile . '/language');
                }
            }

            return $retarr;
        }

        public function setLanguage($language)
        {
            if ($language != $this->_language) {
                $this->_language = $language;
                $this->initialize();
            }
        }

        public function initialize()
        {
            $filename = PACHNO_PATH . 'i18n' . DS . $this->_language . DS . 'initialize.inc.php';
            if (file_exists($filename)) {
                Logging::log("Initiating with file '{$filename}", 'i18n');
                include $filename;
                Logging::log("Done Initiating", 'i18n');
            }
            if ($this->_strings === null) {
                if (Context::getCache()->fileHas(Cache::KEY_I18N . 'strings_' . $this->_language, false)) {
                    Logging::log('Trying cached strings');
                    $strings = Context::getCache()->fileGet(Cache::KEY_I18N . 'strings_' . $this->_language, false);
                    $this->_strings = (is_array($strings)) ? $strings : null;
                }
                if ($this->_strings === null) {
                    Logging::log('No usable cached strings available');
                    $this->loadStrings();
                    foreach (array_keys(Context::getModules()) as $module_name) {
                        $this->loadStrings($module_name);
                    }
                    if (is_array($this->_strings)) {
                        Context::getCache()->fileAdd(Cache::KEY_I18N . 'strings_' . $this->_language, $this->_strings, false);
                    }
                }
            }
        }

        protected function loadStrings($module = null)
        {
            if ($this->_strings === null) $this->_strings = [];
            $filename = '';
            if ($module !== null) {
                if (file_exists(PACHNO_PATH . 'i18n' . DS . $this->_language . DS . "{$module}.xlf")) {
                    $filename = PACHNO_PATH . 'i18n' . DS . $this->_language . DS . "{$module}.xlf";
                } elseif (file_exists(PACHNO_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . "{$module}.xlf")) {
                    $filename = PACHNO_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . "{$module}.xlf";
                } else {
                    $filename = PACHNO_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . "strings.xlf";
                }
            } else {
                $filename = $this->getStringsFilename();
            }

            if (file_exists($filename)) {
                Logging::log("Loading strings from file '{$filename}", 'i18n');
                $xliff_dom = new DOMDocument();
                $xliff_dom->loadXML(file_get_contents($filename));
                $trans_units = $xliff_dom->getElementsByTagName('trans-unit');
                foreach ($trans_units as $trans_unit) {
                    $source_tag = $trans_unit->getElementsByTagName('source');
                    $target_tag = $trans_unit->getElementsByTagName('target');
                    if (is_object($source_tag) && is_object($source_tag->item(0)) && is_object($target_tag) && is_object($target_tag->item(0))) {
                        $this->addString($source_tag->item(0)->nodeValue, $target_tag->item(0)->nodeValue);
                    }
                }
                Logging::log("Done loading strings from file", 'i18n');
            } else {
                $message = 'Could not find language file ' . $filename;
                Logging::log($message, 'i18n', Logging::LEVEL_NOTICE);
            }
        }

        public function addString($key, $translation)
        {
            $this->_strings[$key] = $this->__e($translation);
        }

        public function __e($text, $replacements = [])
        {
            return htmlentities($this->applyTextReplacements($text, $replacements), ENT_QUOTES, $this->getCharset());
        }

        protected function applyTextReplacements($text, $replacements)
        {
            if (!empty($replacements)) {
                $text = str_replace(array_keys($replacements), array_values($replacements), $text);
            }

            return $text;
        }

        public function getCharset()
        {
            if (Context::isInstallmode()) return $this->_charset;

            return (Settings::get('charset') != '') ? Settings::get('charset') : $this->_charset;
        }

        public function setCharset($charset)
        {
            $this->_charset = $charset;
        }

        public function getLangCharset()
        {
            return $this->_charset;
        }

        public function loadModuleStrings($module)
        {
            $this->loadStrings($module);
        }

        public function addStrings($strings)
        {
            if (is_array($strings)) {
                foreach ($strings as $key => $translation) {
                    $this->addString($key, $translation);
                }
            }
        }

        public function hasTranslatedTemplate($template, $is_component = false)
        {
            if (mb_strpos($template, '/')) {
                $templateinfo = explode('/', $template);
                $module = $templateinfo[0];
                $templatefile = ($is_component) ? '_' . $templateinfo[1] . '.inc.php' : $templateinfo[1] . '.' . Context::getRequest()->getRequestedFormat() . '.php';
            } else {
                $module = Context::getRouting()->getCurrentRoute()->getModuleName();
                $templatefile = ($is_component) ? '_' . $template . '.inc.php' : $template . '.' . Context::getRequest()->getRequestedFormat() . '.php';
            }
            if (file_exists(PACHNO_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . 'templates' . DS . $templatefile)) {
                return PACHNO_MODULES_PATH . $module . DS . 'i18n' . DS . $this->_language . DS . 'templates' . DS . $templatefile;
            } elseif (file_exists(PACHNO_PATH . 'i18n' . DS . $this->getCurrentLanguage() . DS . 'templates' . DS . $module . DS . $templatefile)) {
                return PACHNO_PATH . 'i18n' . DS . $this->getCurrentLanguage() . DS . 'templates' . DS . $module . DS . $templatefile;
            }

            return false;
        }

        public function getCurrentLanguage()
        {
            return $this->_language;
        }

        public function __($text, $replacements = [], $html_decode = false)
        {
            if (isset($this->_strings[$text])) {
                $retstring = $this->_strings[$text];
            } else {
                $retstring = $this->__e($text);
                if (Context::isDebugMode()) {
                    Logging::log('The text "' . $text . '" does not exist in list of translated strings.', 'i18n');
                    $this->_missing_strings[$text] = true;
                }
            }

            $retstring = $this->applyTextReplacements($retstring, $replacements);

            if ($html_decode) {
                $retstring = html_entity_decode($retstring);
            }

            return $retstring;
        }

        /**
         * Set local date and time formats
         *
         * @param $formats array list of applicable formats for this local
         *
         */
        public function setDateTimeFormats($formats)
        {
            if (is_array($formats)) {
                $this->_datetime_formats = $formats;
            }
        }

        /**
         * Returns a formatted string of the given timestamp
         *
         * @param integer $tstamp the timestamp to format
         * @param integer $format [optional] the format
         * @param boolean $skipusertimestamp ignore user timestamp
         * @param boolean $skiptimestamp ignore rebasing timestamp
         *
         * @return int|string
         */
        public function formatTime($tstamp, $format = 0, $skipusertimestamp = false, $skiptimestamp = false)
        {
            $tzoffset = 0;
            // offset the timestamp properly
            if (!$skiptimestamp) {
                $tzoffset = self::getTimezoneOffset($skipusertimestamp);
                $tstamp += $tzoffset;
            }

            switch ($format) {
                case 1:
                    $tstring = strftime($this->getDateTimeFormat(1), $tstamp);
                    break;
                case 2:
                    $tstring = strftime($this->getDateTimeFormat(2), $tstamp);
                    break;
                case 3:
                    $tstring = strftime($this->getDateTimeFormat(3), $tstamp);
                    break;
                case 4:
                    $tstring = strftime($this->getDateTimeFormat(4), $tstamp);
                    break;
                case 5:
                    $tstring = strftime($this->getDateTimeFormat(5), $tstamp);
                    break;
                case 6:
                    $tstring = strftime($this->getDateTimeFormat(6), $tstamp);
                    break;
                case 7:
                    $tstring = strftime($this->getDateTimeFormat(7), $tstamp);
                    break;
                case 8:
                    $tstring = strftime($this->getDateTimeFormat(8), $tstamp);
                    break;
                case 9:
                    $tstring = strftime($this->getDateTimeFormat(9), $tstamp);
                    break;
                case 10:
                    $tstring = strftime($this->getDateTimeFormat(10), $tstamp);
                    break;
                case 11:
                    $tstring = strftime($this->getDateTimeFormat(9), $tstamp);
                    break;
                case 12:
                    $tstring = '';
                    $days = day_delta($tstamp, $tzoffset);
                    if ($days == 0) {
                        $tstring .= __('Today') . ', ';
                    } elseif ($days == -1) {
                        $tstring .= __('Yesterday') . ', ';
                    } elseif ($days == 1) {
                        $tstring .= __('Tomorrow') . ', ';
                    } else {
                        $tstring .= strftime($this->getDateTimeFormat(12) . ', ', $tstamp);
                    }
                    $tstring .= strftime($this->getDateTimeFormat(14), $tstamp);
                    break;
                case 13:
                    $tstring = '';
                    $days = day_delta($tstamp, $tzoffset);
                    if ($days == 0) {
                        //$tstring .= __('Today') . ', ';
                    } elseif ($days == -1) {
                        $tstring .= __('Yesterday') . ', ';
                    } elseif ($days == 1) {
                        $tstring .= __('Tomorrow') . ', ';
                    } else {
                        $tstring .= strftime($this->getDateTimeFormat(12) . ', ', $tstamp);
                    }
                    $tstring .= strftime($this->getDateTimeFormat(14), $tstamp);
                    break;
                case 14:
                    $tstring = '';
                    $days = day_delta($tstamp, $tzoffset);
                    if ($days == 0) {
                        $tstring .= __('Today');
                    } elseif ($days == -1) {
                        $tstring .= __('Yesterday');
                    } elseif ($days == 1) {
                        $tstring .= __('Tomorrow');
                    } else {
                        $tstring .= strftime($this->getDateTimeFormat(12), $tstamp);
                    }
                    break;
                case 15:
                    $tstring = strftime($this->getDateTimeFormat(11), $tstamp);
                    break;
                case 16:
                    $tstring = strftime($this->getDateTimeFormat(12), $tstamp);
                    break;
                case 17:
                    $tstring = strftime($this->getDateTimeFormat(13), $tstamp);
                    break;
                case 18:
                    $tstring = strftime($this->getDateTimeFormat(16), $tstamp);
                    break;
                case 19:
                    $tstring = strftime($this->getDateTimeFormat(14), $tstamp);
                    break;
                case 20:
                    $tstring = '';
                    $days = day_delta($tstamp, $tzoffset);
                    if ($days == 0) {
                        $tstring .= __('Today') . ' (' . strftime('%H:%M', $tstamp) . ')';
                    } elseif ($days == -1) {
                        $tstring .= __('Yesterday') . ' (' . strftime('%H:%M', $tstamp) . ')';
                    } elseif ($days == 1) {
                        $tstring .= __('Tomorrow') . ' (' . strftime('%H:%M', $tstamp) . ')';
                    } else {
                        $tstring .= strftime($this->getDateTimeFormat(15), $tstamp);
                    }
                    break;
                case 21:
                    $tstring = strftime('%a, %d %b %Y %H:%M:%S ', $tstamp);

                    return ($tstring);
                    break;
                case 22:
                    $tstring = strftime($this->getDateTimeFormat(15), $tstamp);
                    break;
                case 23:
                    $tstring = '';
                    $days = day_delta($tstamp, $tzoffset);
                    if ($days == 0) {
                        $tstring .= __('Today');
                    } elseif ($days == -1) {
                        $tstring .= __('Yesterday');
                    } elseif ($days == 1) {
                        $tstring .= __('Tomorrow');
                    } else {
                        $tstring .= strftime($this->getDateTimeFormat(15), $tstamp);
                    }
                    break;
                case 24:
                    $tstring = strftime($this->getDateTimeFormat(18), $tstamp);
                    break;
                case 25:
                    $tstring = '';
                    $days = day_delta($tstamp, $tzoffset);
                    if ($days == 0) {
                        $tstring .= __('Today') . ' (' . strftime('%H:%M', $tstamp) . ')';
                    } elseif ($days == -1) {
                        $tstring .= __('Yesterday') . ' (' . strftime('%H:%M', $tstamp) . ')';
                    } elseif ($days == 1) {
                        $tstring .= __('Tomorrow') . ' (' . strftime('%H:%M', $tstamp) . ')';
                    } else {
                        $tstring .= strftime($this->getDateTimeFormat(10), $tstamp);
                    }
                    break;
                default:
                    return $tstamp;
            }

            return utf8_encode($tstring);
        }

        public static function getTimezoneOffset($skipusertimestamp = false)
        {
            // offset the timestamp properly
            if (!$skipusertimestamp) {
                $tz = Context::getUser()->getTimezone();
                $tstamp = $tz->getOffset(new DateTime(null, Settings::getServerTimezone()));
            } else {
                $tstamp = Settings::getServerTimezone()->getOffset(new DateTime('GMT'));
            }

            return $tstamp;
        }

        /**
         * Return localized date and time format
         * @see http://php.net/manual/en/function.date.php
         *
         * @param $id integer ID of format
         *
         * @return string
         *
         */
        public function getDateTimeFormat($id)
        {
            if (array_key_exists($id, $this->_datetime_formats)) {
                return $this->_datetime_formats[$id];
            }
            switch ($id) {
                case 1 : // 14:45 - Thu Dec 30, 2010
                    $format = '%H:%M - %a %b %d, %Y';
                    break;
                case 2 : // 14:45 - Thu 30.m, 2010
                    $format = '%H:%M - %a %d.m, %Y';
                    break;
                case 3 : // Thu Dec 30 14:45
                    $format = '%a %b %d %H:%M';
                    break;
                case 4 : // Dec 30 14:45
                    $format = '%b %d %H:%M';
                    break;
                case 5 : // December 30, 2010
                    $format = '%B %d, %Y';
                    break;
                case 6 : // December 30, 2010 (14:45)
                    $format = '%B %d, %Y (%H:%M)';
                    break;
                case 7 : // Thursday 30 December, 2010 (14:45)
                    $format = '%A %d %B, %Y (%H:%M)';
                    break;
                case 8 : // Dec 30, 2010 14:45
                    $format = '%b %d, %Y %H:%M';
                    break;
                case 9 : // Dec 30, 2010 - 14:45
                    $format = '%b %d, %Y - %H:%M';
                    break;
                case 10 : // Dec 30, 2010 (14:45)
                    $format = '%b %d, %Y (%H:%M)';
                    break;
                case 11 : // December
                    $format = '%B';
                    break;
                case 12 : // Dec 30
                    $format = '%b %d';
                    break;
                case 13 : // Thu
                    $format = '%a';
                    break;
                case 14 : // 14:45
                    $format = '%H:%M';
                    break;
                case 15 : // Dec 30, 2010
                    $format = '%b %d, %Y';
                    break;
                case 16 : // 14h 45m
                    $format = '%Gh %im';
                    break;
                case 17 : // Thu, 30 December 2010 14:45:45 GMT
                    $format = '%a, %d %b %Y %H:%M:%S GMT';
                    break;
                case 18 : // Thu, 30 December 2010 14:45:45 GMT
                    $format = '%Y-%M-%D';
                    break;
                default : // local server setting
                    $format = '%c';
            }

            return $format;
        }

        /**
         * Returns an ISO-8859-1 encoded string if UTF-8 encoded and current charset not UTF-8
         *
         * @param string $str the encode string
         * @param boolean $htmlentities [optional] whether to convert applicable characters to HTML entities
         *
         * @return string
         */
        public function decodeUTF8($str, $htmlentities = false)
        {
            if ($this->isUTF8($str) && !mb_stristr($this->getCharset(), 'UTF-8')) {
                $str = utf8_decode($str);
            }

            if ($htmlentities) {
                $str = htmlentities($str, ENT_NOQUOTES + ENT_IGNORE, $this->getCharset());
            }

            return $str;
        }

        /**
         * Determine if a string is UTF-8 encoded
         * @filesource http://www.php.net/manual/en/function.mb-detect-encoding.php#68607
         *
         * @param string $str the string
         *
         * @return boolean
         */
        protected function isUTF8($str)
        {
            return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]
        |\xE0[\xA0-\xBF][\x80-\xBF]
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
        |\xED[\x80-\x9F][\x80-\xBF]
        |\xF0[\x90-\xBF][\x80-\xBF]{2}
        |[\xF1-\xF3][\x80-\xBF]{3}
        |\xF4[\x80-\x8F][\x80-\xBF]{2}
        )+%xs', $str);
        }

        /**
         * The opposite of custom function "\pachno\core\framework\Context::getI18n()->decodeUTF8".
         *
         * @param string $str the encode string
         * @param boolean $htmlentities [optional] whether to convert applicable characters to HTML entities
         *
         * @return string
         */
        public function encodeUTF8($str, $htmlentities = false)
        {
            if ($htmlentities) {
                $str = html_entity_decode($str, ENT_NOQUOTES + ENT_IGNORE, $this->getCharset());
            }

            if (!$this->isUTF8($str) && !mb_stristr($this->getCharset(), 'UTF-8')) {
                $str = utf8_encode($str);
            }

            return $str;
        }

    }
