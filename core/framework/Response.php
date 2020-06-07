<?php

    namespace pachno\core\framework;

    use pachno\core\entities\Client;
    use pachno\core\entities\Project;
    use pachno\core\entities\Team;

    /**
     * Response class used in the MVC part of the framework
     *
     * @package pachno
     * @subpackage mvc
     */
    class Response
    {

        const DECORATE_NONE = 0;

        const DECORATE_HEADER = 1;

        const DECORATE_FOOTER = 2;

        const DECORATE_DEFAULT = 3;

        const DECORATE_CUSTOM = 4;

        const HTTP_STATUS_OK = 200;

        const HTTP_STATUS_FOUND = 302;

        const HTTP_STATUS_NOT_MODIFIED = 304;

        const HTTP_STATUS_BAD_REQUEST = 400;

        const HTTP_STATUS_FORBIDDEN = 403;

        const HTTP_STATUS_NOT_FOUND = 404;

        /**
         * The current page (used to identify the selected tab
         *
         * @var string
         */
        protected $_page;

        /**
         * Breadcrumb trail for the current page
         *
         * @var array
         */
        protected $_breadcrumb;

        /**
         * Current page title
         *
         * @var string
         */
        protected $_title;

        /**
         * List of headers
         *
         * @var array
         */
        protected $_headers = [];

        /**
         * @var string
         */
        protected $page_name;

        /**
         * List of javascripts
         *
         * @var array
         */
        protected $_javascripts = [
            'jquery.min',
            'pachno/index'
        ];

        /**
         * List of stylesheets
         *
         * @var array
         */
        protected $_stylesheets = [];

        /**
         * List of feeds
         *
         * @var array
         */
        protected $_feeds = [];

        /**
         * Current response status
         *
         * @var integer
         */
        protected $_http_status = self::HTTP_STATUS_OK;

        /**
         * Response content-type
         *
         * @var string
         */
        protected $_content_type = 'text/html';

        /**
         * Current template
         *
         * @var string
         */
        protected $_template = '';

        /**
         * Current layout path
         *
         * @var string
         */
        protected $_layout_path = '';

        /**
         * What decoration to use (default normal)
         *
         * @var integer
         */
        protected $_decoration = 3;

        /**
         * Decoration used to decorate the header
         *
         * @var string
         */
        protected $_decor_header;

        /**
         * Decoration used to decorate the footer
         * @var unknown_type
         */
        protected $_decor_footer;

        protected $_is_fullscreen = false;

        /**
         * Whether to show the project menu strip or not
         *
         * @var boolean
         */
        protected $_project_menu_strip_visible = true;

        /**
         * Forward the user to a different url via meta tag
         *
         * @param string $url The url to forward to
         */
        static function metaForward($url)
        {
            print "<meta http-equiv=\"refresh\" content=\"0;URL={$url}\">";
            exit();
        }

        /**
         * Template escaping function without translation
         *
         * @param string $text the text to translate
         *
         * @return string
         */
        public static function escape($text)
        {
            return htmlentities($text, ENT_QUOTES, Context::getI18n()->getCharset());
        }

        public function ajaxResponseText($code, $error)
        {
            if (Context::isDebugMode()) return true;

            $this->cleanBuffer();
            $this->setContentType('application/json');
            $this->setHttpStatus($code);
            $this->renderHeaders();
            echo json_encode(['error' => $error]);
            die();
        }

        public function cleanBuffer()
        {
            $ob_status = ob_get_status();
            if (!empty($ob_status) && ((isset($ob_status['status']) && $ob_status['status'] != PHP_OUTPUT_HANDLER_END) || (isset($ob_status['flags']) && !($ob_status['flags'] & PHP_OUTPUT_HANDLER_END)))) {
                ob_end_clean();
            }
        }

        /**
         * Render current headers
         */
        public function renderHeaders($disableCache = false)
        {
            header("HTTP/1.0 " . $this->_http_status);
            if ($disableCache) {
                /* headers to stop caching in browsers and proxies */
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
                header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
                header("Pragma: no-cache"); // HTTP/1.0
            }
            if (Context::isDebugMode()) {
                header("x-pachno-debugid: " . Context::getDebugID());
                $session_time = Context::getSessionLoadTime();
                $session_time = ($session_time >= 1) ? round($session_time, 2) . 's' : round($session_time * 1000, 1) . 'ms';
                header("x-pachno-sessiontime: " . $session_time);
                $load_time = Context::getLoadTime();
                $calculated_load_time = $load_time - Context::getSessionLoadTime();
                $load_time = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
                $calculated_load_time = ($calculated_load_time >= 1) ? round($calculated_load_time, 2) . 's' : round($calculated_load_time * 1000, 1) . 'ms';
                header("x-pachno-loadtime: " . $load_time);
                header("x-pachno-calculatedtime: " . $calculated_load_time);
            }
            if (Context::isI18nInitialized()) {
                header("Content-Type: " . $this->_content_type . "; charset=" . Context::getI18n()->getCharset());
            } else {
                header("Content-Type: " . $this->_content_type . "; charset=utf-8");
            }

            foreach ($this->_headers as $header) {
                header($header);
            }
        }

        public function setupResponseContentType($request_content_type)
        {
            $this->setDecoration(self::DECORATE_NONE);
            switch ($request_content_type) {
                case 'xml':
                    $this->setContentType('application/xml');
                    break;
                case 'rss':
                    $this->setContentType('application/xml');
                    break;
                case 'xlsx':
                    $this->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    break;
                case 'ods':
                    $this->setContentType('application/vnd.oasis.opendocument.spreadsheet');
                    break;
                case 'json':
                    $this->setContentType('application/json');
                    break;
                case 'csv':
                    $this->setContentType('text/csv');
                    break;
                default:
                    $this->setDecoration(self::DECORATE_DEFAULT);
                    break;
            }

        }

        /**
         * Return current template
         *
         * @return string
         */
        public function getTemplate()
        {
            return $this->_template;
        }

        /**
         * Set the template
         *
         * @param string $template The template name
         */
        public function setTemplate($template)
        {
            $this->_template = $template;
        }

        /**
         * Return current layout path
         *
         * @return string
         */
        public function getLayoutPath()
        {
            return $this->_layout_path;
        }

        /**
         * Set the layout path
         *
         * @param string $layout_path The layout path
         */
        public function setLayoutPath($layout_path)
        {
            $this->_layout_path = $layout_path;
        }

        /**
         * Set the breadcrumb trail for the current page
         *
         * @param array $breadcrumb
         */
        public function setBreadcrumb($breadcrumb)
        {
            $this->_breadcrumb = $breadcrumb;
        }

        /**
         * Add to the breadcrumb trail for the current page
         *
         * @param string $breadcrumb
         * @param string $url [optional] The menu item's url if any
         * @param array $subitems [optional] An array of submenu items to add
         * @param string $class [optional] An optional class
         */
        public function addBreadcrumb($breadcrumb, $url = null, $subitems = null, $class = null)
        {
            if ($this->_breadcrumb === null) {
                $this->_breadcrumb = [];
                Context::populateBreadcrumbs();
            }

            $this->_breadcrumb[] = ['title' => $breadcrumb, 'url' => $url, 'subitems' => $subitems, 'class' => $class];
        }

        /**
         * Get the current title
         *
         * @return string
         */
        public function getTitle()
        {
            return $this->_title;
        }

        /**
         * Set the current title
         *
         * @param string $title The title
         */
        public function setTitle($title)
        {
            $this->_title = $title;
        }

        /**
         * Check to see if a title is set
         *
         * @return boolean
         */
        public function hasTitle()
        {
            return (trim($this->_title) != '') ? true : false;
        }

        /**
         * Get the current page name
         *
         * @return string
         */
        public function getPageName()
        {
            return $this->page_name;
        }

        /**
         * Set the current page name
         *
         * @param string $page_name The page name
         */
        public function setPageName($page_name)
        {
            $this->page_name = $page_name;
        }

        /**
         * Check to see if a page name is set
         *
         * @return boolean
         */
        public function hasPageName()
        {
            return (trim($this->page_name) != '') ? true : false;
        }

        /**
         * Get the current page name
         *
         * @return string
         */
        public function getPage()
        {
            return $this->_page;
        }

        /**
         * Set which page we're on
         *
         * @param string $page A unique page identifier
         */
        public function setPage($page)
        {
            $this->_page = $page;
        }

        /**
         * Return the breadcrumb trail for the current page
         *
         * @return array
         */
        public function getBreadcrumbs()
        {
            if (!is_array($this->_breadcrumb)) {
                $this->_breadcrumb = [];
                Context::populateBreadcrumbs();
            }

            return $this->_breadcrumb;
        }

        /**
         * Add a javascript
         *
         * @param string $javascript javascript name
         * @param bool $priority Mark this script for being loaded before others
         */
        public function addJavascript($javascript, $priority = false)
        {
            if (!$priority) {
                $this->_javascripts[$javascript] = $javascript;
            } else {
                $this->_javascripts = array_merge([$javascript => $javascript], $this->_javascripts);
            }
        }

        /**
         * Add a stylesheet
         *
         * @param string $stylesheet stylesheet name
         * @param bool $priority Mark this stylesheet for being loaded before others
         */
        public function addStylesheet($stylesheet, $priority = false)
        {
            if (!$priority) {
                $this->_stylesheets[$stylesheet] = $stylesheet;
            } else {
                $this->_stylesheets = array_merge([$stylesheet => $stylesheet], $this->_stylesheets);
            }
        }

        /**
         * Add a feed
         *
         * @param string $url feed url
         * @param string $description feed description
         */
        public function addFeed($url, $description)
        {
            $this->_feeds[$url] = $description;
        }

        public function isModified($timestamp)
        {
            $last_modified_string = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
            $etag = md5($timestamp);

            $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
            $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;
            if ((($if_none_match && $if_none_match == $etag) || (!$if_none_match)) &&
                ($if_modified_since && $if_modified_since == $last_modified_string)) {
                return false;
            }

            return true;
        }

        /**
         * Forward the user to a different URL
         *
         * @param string $url the url to forward to
         * @param integer $code HTTP status code
         */
        public function headerRedirect($url, $code = self::HTTP_STATUS_FOUND)
        {
            Logging::log('Running header redirect function');
            $this->clearHeaders();
            $this->setHttpStatus($code);
            if (Context::getRequest()->isAjaxCall() || Context::getRequest()->getRequestedFormat() == 'json') {
                $this->renderHeaders();
            } else {
                $this->addHeader("Location: $url");
                $this->renderHeaders();
            }
            exit();
        }

        /**
         * Clear current headers
         */
        public function clearHeaders()
        {
            $this->_headers = [];

        }

        /**
         * Add a header
         *
         * @param string $header The header to add
         */
        public function addHeader($header)
        {
            $this->_headers[] = $header;
        }

        /**
         * Get the HTTP status code
         *
         * @return int
         */
        public function getHttpStatus()
        {
            return $this->_http_status;
        }

        /**
         * Set the HTTP status code
         *
         * @param integer $code The code to set
         */
        public function setHttpStatus($code)
        {
            $this->_http_status = $code;
        }

        /**
         * Whether we're decorating with the header or not
         *
         * @return string
         */
        public function doDecorateHeader()
        {
            return ($this->_decoration == self::DECORATE_HEADER || ($this->_decoration == self::DECORATE_CUSTOM && $this->_decor_header)) ? true : false;
        }

        /**
         * Whether we're decorating with the footer or not
         * @return unknown_type
         */
        public function doDecorateFooter()
        {
            return ($this->_decoration == self::DECORATE_FOOTER || ($this->_decoration == self::DECORATE_CUSTOM && $this->_decor_footer)) ? true : false;
        }

        /**
         * Get the current decoration mode
         *
         * @return int
         */
        public function getDecoration()
        {
            return $this->_decoration;
        }

        /**
         * Set the decoration mode
         *
         * @param integer $mode The mode used (see class constants)
         * @param array $params [optional] optional decoration specifiers in the format "array('header' => templatename, 'footer' => templatename)"
         *
         * @return null
         */
        public function setDecoration($mode, $params = null)
        {
            $this->_decoration = $mode;
            if (is_array($params)) {
                if (array_key_exists('header', $params)) $this->_decor_header = $params['header'];
                if (array_key_exists('footer', $params)) $this->_decor_footer = $params['footer'];
            }
        }

        /**
         * Get the current custom header decoration file location
         *
         * @return string
         */
        public function getHeaderDecoration()
        {
            return $this->_decor_header;
        }

        /**
         * Get the current custom footer decoration file location
         *
         * @return string
         */
        public function getFooterDecoration()
        {
            return $this->_decor_footer;
        }

        /**
         * Sets a cookie on the client, default expiration when session end
         *
         * @param $key string the cookie key
         * @param $value string the cookie value
         *
         * @return bool
         */
        public function setSessionCookie($key, $value)
        {
            $this->setCookie($key, $value, null);

            return true;
        }

        /**
         * Sets a cookie on the client, default expiration is one day
         *
         * @param $key string the cookie key
         * @param $value string the cookie value
         * @param $expiration integer when the cookie expires (seconds from now)
         *
         * @return bool
         */
        public function setCookie($key, $value, $expiration = 864000)
        {
            $expiration = ($expiration !== null) ? intval(NOW + $expiration) : null;
            $secure = Context::getScope()->isSecure();
            setcookie($key, $value, [
                'expires' => $expiration,
                'path' => Context::getWebroot(),
                'samesite' => 'None',
                'secure' => $secure
            ]);

            return true;
        }

        /**
         * Deletes a cookie on the client
         *
         * @param $key string the cookie key to delete
         *
         * @return bool
         */
        public function deleteCookie($key)
        {
            $secure = Context::getScope()->isSecure();
            setcookie($key, '', [
                'expires' => NOW - 36000,
                'path' => (Context::getWebroot() != '/') ? Context::getWebroot() : '',
                'samesite' => 'None',
                'secure' => $secure
            ]);

            return true;
        }

        /**
         * Return the current response content type
         *
         * @return string
         */
        public function getContentType()
        {
            return $this->_content_type;
        }

        /**
         * Set the current response content type (default text/html)
         *
         * @param string $content_type The content type to set
         */
        public function setContentType($content_type)
        {
            $this->_content_type = $content_type;
        }

        /**
         * Return all active javascripts
         *
         * @return array
         */
        public function getJavascripts()
        {
            return $this->_splitLocalAndExternalResources($this->_javascripts);
        }

        protected function _splitLocalAndExternalResources($resources)
        {
            $external = [];
            $local = [];

            foreach ($resources as $resource) {
                if (strpos($resource, '://') !== false) {
                    $external[] = $resource;
                } else {
                    $local[] = $resource;
                }
            }

            return [$local, $external];
        }

        /**
         * Return all active stylesheets
         *
         * @return array
         */
        public function getStylesheets()
        {
            return $this->_splitLocalAndExternalResources($this->_stylesheets);
        }

        /**
         * Return all available feeds
         *
         * @return array
         */
        public function getFeeds()
        {
            return $this->_feeds;
        }

        public function getPredefinedBreadcrumbLinks($type, $project = null)
        {
            $i18n = Context::getI18n();
            $links = [];
            switch ($type) {
                case 'main_links':
                    $links[] = ['url' => Context::getRouting()->generate('home'), 'title' => $i18n->__('Frontpage')];
                    $links[] = ['url' => Context::getRouting()->generate('dashboard'), 'title' => $i18n->__('Personal dashboard')];
                    $links[] = ['title' => $i18n->__('Issues')];
                    $links[] = ['title' => $i18n->__('Teams')];
                    $links[] = ['title' => $i18n->__('Clients')];
                    $links = Event::createNew('core', 'breadcrumb_main_links', null, [], $links)->trigger()->getReturnList();

                    if (Context::getUser()->canAccessConfigurationPage()) {
                        $links[] = ['url' => make_url('configure'), 'title' => $i18n->__('Configure %sitename', ['%sitename' => Settings::getSiteHeaderName()])];
                    }
                    $links[] = ['url' => Context::getRouting()->generate('about'), 'title' => $i18n->__('About %sitename', ['%sitename' => Settings::getSiteHeaderName()])];
                    $links[] = ['url' => Context::getRouting()->generate('account'), 'title' => $i18n->__('Account details')];

                    $root_projects = array_merge(Project::getAllRootProjects(true), Project::getAllRootProjects(false));
                    $first = true;
                    foreach ($root_projects as $project) {
                        if (!$project->hasAccess())
                            continue;
                        if ($first) {
                            $first = false;
                            $links[] = ['separator' => true];
                        }
                        $links[] = ['url' => Context::getRouting()->generate('project_dashboard', ['project_key' => $project->getKey()]), 'title' => $project->getName()];
                    }

                    break;
                case 'project_summary':
                    $links['project_dashboard'] = ['url' => Context::getRouting()->generate('project_dashboard', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Dashboard')];
                    $links['project_releases'] = ['url' => Context::getRouting()->generate('project_releases', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Releases')];
                    $links['project_roadmap'] = ['url' => Context::getRouting()->generate('project_roadmap', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Roadmap')];
                    $links['project_team'] = ['url' => Context::getRouting()->generate('project_team', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Team overview')];
                    $links['project_statistics'] = ['url' => Context::getRouting()->generate('project_statistics', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Statistics')];
                    $links['project_timeline'] = ['url' => Context::getRouting()->generate('project_timeline', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Timeline')];
                    $links['project_issues'] = ['url' => Context::getRouting()->generate('project_issues', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Issues')];
                    $links = Event::createNew('core', 'breadcrumb_project_links', null, [], $links)->trigger()->getReturnList();
                    $links['project_release_center'] = ['url' => Context::getRouting()->generate('project_release_center', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Release center')];
                    $links['project_settings'] = ['url' => Context::getRouting()->generate('project_settings', ['project_key' => $project->getKey()]), 'title' => $i18n->__('Settings')];
                    break;
                case 'client_list':
                    foreach (Client::getAll() as $client) {
                        if ($client->hasAccess())
                            $links[] = ['url' => Context::getRouting()->generate('client_dashboard', ['client_id' => $client->getID()]), 'title' => $client->getName()];
                    }
                    break;
                case 'team_list':
                    foreach (Team::getAll() as $team) {
                        if ($team->hasAccess())
                            $links[] = ['url' => Context::getRouting()->generate('team_dashboard', ['team_id' => $team->getID()]), 'title' => $team->getName()];
                    }
                    break;
                case 'configure':
                    $config_sections = Settings::getConfigSections($i18n);
                    foreach ($config_sections as $key => $sections) {
                        foreach ($sections as $section) {
                            if ($key == Settings::CONFIGURATION_SECTION_MODULES) {
                                $url = (is_array($section['route'])) ? make_url($section['route'][0], $section['route'][1]) : make_url($section['route']);
                                $links[] = ['url' => $url, 'title' => $section['description']];
                            } else {
                                $links[] = ['url' => make_url($section['route']), 'title' => $section['description']];
                            }
                        }
                    }
                    break;
            }

            return $links;
        }

        public function getAllHeaders()
        {
            return $this->_headers;
        }

        public function setFullscreen($value)
        {
            $this->_is_fullscreen = $value;
        }

        public function isFullscreen()
        {
            return $this->_is_fullscreen;
        }

    }
