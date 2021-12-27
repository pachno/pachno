<?php

    namespace pachno\core\framework;

    use DateTimeZone;
    use Exception;
    use pachno\core\entities\Group;
    use pachno\core\entities\Scope;
    use pachno\core\entities\Setting;
    use pachno\core\entities\tables;
    use pachno\core\entities\User;
    use pachno\core\entities\Userstate;

    /**
     * Settings class
     *
     * @package pachno
     * @subpackage core
     */
    final class Settings
    {

        public const ACCESS_READ = 1;

        public const ACCESS_FULL = 2;

        public const CONFIGURATION_SECTION_WORKFLOW = 1;

        public const CONFIGURATION_SECTION_WORKFLOW_SCHEMES = 9;

        public const CONFIGURATION_SECTION_USERS = 2;

        public const CONFIGURATION_SECTION_TEAMS = 22;

        public const CONFIGURATION_SECTION_CLIENTS = 23;

        public const CONFIGURATION_SECTION_UPLOADS = 3;

        public const CONFIGURATION_SECTION_MAILING = 'mailing';

        public const CONFIGURATION_SECTION_ISSUEFIELDS = 4;

        public const CONFIGURATION_SECTION_PERMISSIONS = 5;

        public const CONFIGURATION_SECTION_ISSUETYPES = 6;

        public const CONFIGURATION_SECTION_ISSUETYPE_SCHEMES = 8;

        public const CONFIGURATION_SECTION_ROLES = 7;

        public const CONFIGURATION_SECTION_PROJECTS = 10;

        public const CONFIGURATION_SECTION_SETTINGS = 12;

        public const CONFIGURATION_SECTION_SCOPES = 14;

        public const CONFIGURATION_SECTION_MODULES = 15;

        public const CONFIGURATION_SECTION_IMPORT = 16;

        public const CONFIGURATION_SECTION_AUTHENTICATION = 17;

        public const CONFIGURATION_SECTION_THEMES = 18;

        public const APPEARANCE_HEADER_THEME = 0;

        public const APPEARANCE_HEADER_CUSTOM = 1;

        public const APPEARANCE_FAVICON_THEME = 0;

        public const APPEARANCE_FAVICON_CUSTOM = 1;

        public const INFOBOX_PREFIX = 'hide_infobox_';

        public const TOGGLE_PREFIX = 'toggle_';

        public const SYNTAX_MW = 1;

        public const SYNTAX_MD = 2;

        public const SYNTAX_PT = 3;

        public const SYNTAX_EDITOR_JS = 4;

        public const LOGIN_REQUIRED_NONE = 0;

        public const LOGIN_REQUIRED_WRITE = 1;

        public const LOGIN_REQUIRED_READ = 2;

        public const SETTING_ADMIN_GROUP = 'admingroup';

        public const SETTING_ALLOW_REGISTRATION = 'allowreg';

        public const SETTING_ALLOW_USER_THEMES = 'userthemes';

        public const SETTING_AWAYSTATE = 'awaystate';

        public const SETTING_DEFAULT_CHARSET = 'charset';

        public const SETTING_DEFAULT_COMMENT_SYNTAX = 'comment_syntax';

        public const SETTING_DEFAULT_ISSUE_SYNTAX = 'issue_syntax';

        public const SETTING_DEFAULT_LANGUAGE = 'language';

        public const SETTING_DEFAULT_USER_IS_GUEST = 'defaultisguest';

        public const SETTING_DEFAULT_USER_ID = 'defaultuserid';

        public const SETTING_MULTI_TEAM_WORKFLOW_SCHEME = 'multi_team_workflow_scheme';

        public const SETTING_BALANCED_WORKFLOW_SCHEME = 'balanced_workflow_scheme';

        public const SETTING_SIMPLE_WORKFLOW_SCHEME = 'simple_workflow_scheme';

        public const SETTING_FULL_RANGE_ISSUETYPE_SCHEME = 'full_range_issuetype_scheme';

        public const SETTING_BALANCED_ISSUETYPE_SCHEME = 'balanced_issuetype_scheme';

        public const SETTING_BALANCED_AGILE_ISSUETYPE_SCHEME = 'balanced_agile_issuetype_scheme';

        public const SETTING_SIMPLE_ISSUETYPE_SCHEME = 'simple_issuetype_scheme';

        public const SETTING_ENABLE_UPLOADS = 'enable_uploads';

        public const SETTING_ENABLE_GRAVATARS = 'enable_gravatars';

        public const SETTING_FAVICON_TYPE = 'icon_fav';

        public const SETTING_FAVICON_ID = 'icon_fav_id';

        public const SETTING_GUEST_GROUP = 'guestgroup';

        public const SETTING_HEADER_ICON_TYPE = 'icon_header';

        public const SETTING_HEADER_ICON_ID = 'icon_header_id';

        public const SETTING_HEADER_LINK = 'header_link';

        public const SETTING_NOTIFICATION_POLL_INTERVAL = 'notificationpollinterval';

        public const SETTING_OFFLINESTATE = 'offlinestate';

        public const SETTING_ONLINESTATE = 'onlinestate';

        public const SETTING_REGISTRATION_DOMAIN_WHITELIST = 'limit_registration';

        public const SETTING_REQUIRE_LOGIN = 'requirelogin';

        public const SETTING_ELEVATED_LOGIN_DISABLED = 'elevatedlogindisabled';

        public const SETTING_RETURN_FROM_LOGIN = 'returnfromlogin';

        public const SETTING_RETURN_FROM_LOGOUT = 'returnfromlogout';

        public const SETTING_SALT = 'salt';

        public const SETTING_SERVER_TIMEZONE = 'server_timezone';

        public const SETTING_SITE_NAME = 'b2_name';

        public const SETTING_SITE_NAME_HTML = 'pachno_header_name_html';

        public const SETTING_THEME_NAME = 'theme_name';

        public const SETTING_UPLOAD_EXTENSIONS_LIST = 'upload_extensions_list';

        public const SETTING_UPLOAD_LOCAL_PATH = 'upload_localpath';

        public const SETTING_UPLOAD_MAX_FILE_SIZE = 'upload_max_file_size';

        public const SETTING_UPLOAD_RESTRICTION_MODE = 'upload_restriction_mode';

        public const SETTING_UPLOAD_STORAGE = 'upload_storage';

        public const SETTING_UPLOAD_ALLOW_IMAGE_CACHING = 'upload_allow_image_caching';

        public const SETTING_UPLOAD_DELIVERY_USE_XSEND = 'upload_delivery_use_xsend';

        public const SETTING_ISSUETYPE_BUG_REPORT = 'issuetype_bug_report';

        public const SETTING_ISSUETYPE_FEATURE_REQUEST = 'issuetype_feature_request';

        public const SETTING_ISSUETYPE_ENHANCEMENT = 'issuetype_enhancement';

        public const SETTING_ISSUETYPE_TASK = 'issuetype_task';

        public const SETTING_ISSUETYPE_USER_STORY = 'issuetype_user_story';

        public const SETTING_ISSUETYPE_EPIC = 'issuetype_epic';

        public const SETTING_ISSUETYPE_IDEA = 'issuetype_idea';

        public const SETTING_USER_COMMENT_ORDER = 'comment_order';

        public const SETTING_USER_DISPLAYNAME_FORMAT = 'user_displayname_format';

        public const SETTING_USER_GROUP = 'defaultgroup';

        public const SETTING_USER_TIMEZONE = 'timezone';

        public const SETTING_USER_KEYBOARD_NAVIGATION = 'keyboard_navigation';

        public const SETTING_USER_LANGUAGE = 'language';

        public const SETTING_USER_ACTIVATION_KEY = 'activation_key';

        public const SETTING_USER_NOTIFICATION_TIMEOUT = 'notifications_timeout';

        public const SETTING_USER_DESKTOP_NOTIFICATIONS_NEW_TAB = 'desktop_notifications_new_tab';

        public const SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ISSUES = 'subscribe_posted_updated_commented_issues';

        public const SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ARTICLES = 'subscribe_created_updated_commented_articles';

        public const SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS = 'subscribe_new_issues_project';

        public const SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY = 'subscribe_new_issues_project_category';

        public const SETTINGS_USER_SUBSCRIBE_NEW_ARTICLES_MY_PROJECTS = 'subscribe_new_articles_project';

        public const SETTINGS_USER_SUBSCRIBE_ASSIGNED_ISSUES = 'subscribe_assigned_issues';

        public const SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS = 'notify_new_issues_my_projects';

        public const SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY = 'notify_new_issues_my_projects_category';

        public const SETTINGS_USER_NOTIFY_NEW_ARTICLES_MY_PROJECTS = 'notify_new_articles_my_projects';

        public const SETTINGS_USER_NOTIFY_ITEM_ONCE = 'notify_issue_once';

        public const SETTINGS_USER_NOTIFY_SUBSCRIBED_ISSUES = 'notify_subscribed_issues';

        public const SETTINGS_USER_NOTIFY_SUBSCRIBED_ARTICLES = 'notify_subscribed_articles';

        public const SETTINGS_USER_NOTIFY_SUBSCRIBED_DISCUSSIONS = 'notify_subscribed_discussion';

        public const SETTINGS_USER_NOTIFY_UPDATED_SELF = 'notify_updated_self';

        public const SETTINGS_USER_NOTIFY_MENTIONED = 'notify_mentioned';

        public const SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS = 'notify_grouped_notifications';

        public const SETTINGS_USER_NOTIFY_ONLY_IN_BOX_WHEN_ACTIVE = 'notify_only_in_box_when_active';

        public const SETTING_AUTH_BACKEND = 'auth_backend';

        public const SETTING_MAINTENANCE_MODE = 'offline';

        public const SETTING_MAINTENANCE_MESSAGE = 'offline_msg';

        public const SETTING_ICONSET = 'iconset';

        public const SETTING_ENABLE_SCOPES = 'enable_scopes';

        public const USER_RSS_KEY = 'rsskey';

        public const USER_DISPLAYNAME_FORMAT_REALNAME = 1;

        public const USER_DISPLAYNAME_FORMAT_BUDDY = 0;

        protected static $_ver_mj = 1;

        protected static $_ver_mn = 0;

        protected static $_ver_rev = 3;

        protected static $_ver_name = "Amethyst";

        /**
         * @var Scope
         */
        protected static $_defaultscope;

        /**
         * @var Setting[][]|Setting[][][]
         */
        protected static $_settings;

        /**
         * @var DateTimeZone
         */
        protected static $_timezone;

        protected static $_loadedsettings = [];

        protected static $_core_workflow;

        protected static $_verified_theme = false;

        protected static $_core_workflowscheme;

        protected static $_core_issuetypescheme;

        /**
         * @var AuthenticationBackend
         */
        protected static $_authentication_backend;

        public static function forceSettingsReload()
        {
            self::$_settings = null;
        }

        public static function deleteModuleSettings($module_name, $scope)
        {
            if ($scope == Context::getScope()->getID()) {
                if (array_key_exists($module_name, self::$_settings)) {
                    unset(self::$_settings[$module_name]);
                }
            }
            tables\Settings::getTable()->deleteModuleSettings($module_name, $scope);
        }

        public static function copyDefaultScopeSetting($name, $module = 'core')
        {
            $setting = self::_loadSetting($name, $module, self::getDefaultScopeID());
            self::saveSetting($name, $setting[0], $module, Context::getScope()->getID());
        }

        /**
         * @param $name
         * @param string $module
         * @param int $scope
         *
         * @return Setting[]
         */
        private static function _loadSetting($name, $module = 'core', $scope = 0)
        {
            return tables\Settings::getTable()->getSettingForAllUsers($name, $module, $scope);
        }

        /**
         * Returns the default scope
         *
         * @return Scope
         */
        public static function getDefaultScopeID()
        {
            if (self::$_defaultscope === null) {
                self::$_defaultscope = tables\ScopeHostnames::getTable()->getScopeIDForHostname('*');
            }

            return self::$_defaultscope;
        }

        /**
         * Save a setting
         *
         * @param string $name The settings key / name of the setting to store
         * @param mixed $value The value to store
         * @param string $module The name / key of the module storing the setting
         * @param int|Scope $scope A scope id (or 0 to apply to all scopes)
         * @param int $uid A user id to save settings for
         *
         * @throws Exception
         */
        public static function saveSetting($name, $value, $module = 'core', $scope = 0, $uid = 0)
        {
            if ($scope == 0 && $name != 'defaultscope' && $module == 'core' && $uid == 0) {
                if (($scope = Context::getScope()) instanceof Scope) {
                    $scope = $scope->getID();
                } else {
                    throw new Exception('No scope loaded, cannot autoload it');
                }
            }

            $is_current_scope = $scope != 0 && ((!Context::getScope() instanceof Scope) || $scope == Context::getScope()->getID());

            if ($is_current_scope) {
                $setting = self::get($name, $module, $scope, $uid, true);
            } else {
                $setting = tables\Settings::getTable()->getSetting($name, $module, $uid, $scope);
            }

            if (!$setting instanceof Setting) {
                $setting = new Setting();
                $setting->setModuleKey($module);
                $setting->setName($name);
                $setting->setUserId($uid);
                $setting->setScope($scope);

                if ($is_current_scope) {
                    self::$_settings[$module][$name][$uid] = $setting;
                }
            }
            if (!$setting->getID() || $setting->getValue() != $value) {
                $setting->setValue($value);
                $setting->save();
            }
        }

        public static function getUpgradeStatus()
        {
            $version_info = explode(',', file_get_contents(PACHNO_PATH . 'installed'));
            $current_version = $version_info[0];
            $upgrade_available = ($current_version != self::getVersion(false));

            return [$current_version, $upgrade_available];
        }

        public static function getVersion($with_codename = false, $with_revision = true)
        {
            $version_string = self::$_ver_mj . '.' . self::$_ver_mn;
            if ($with_revision) $version_string .= (is_numeric(self::$_ver_rev)) ? '.' . self::$_ver_rev : self::$_ver_rev;
            if ($with_codename) $version_string .= ' ("' . self::$_ver_name . '")';

            return $version_string;
        }

        public static function getVersionName()
        {
            return self::$_ver_name;
        }

        public static function hasUserSetting($user_id, $name, $module = 'core', $scope = 0)
        {
            return self::getUserSetting($user_id, $name, $module, $scope) !== null;
        }

        public static function getUserSetting($user_id, $name, $module = 'core', $scope = 0)
        {
            return self::get($name, $module, $scope, $user_id);
        }

        public static function get($name, $module = 'core', $scope = null, $uid = 0, $return_object = false)
        {
            if (!Context::isReadySetup() && !Context::getScope() instanceof Scope) {
                return null;
            }
            if ($scope instanceof Scope) {
                $scope = $scope->getID();
            }
            if (!Context::getScope() instanceof Scope) {
                throw new Exception('Pachno is not installed correctly');
            }
            if ($scope != Context::getScope()->getID() && $scope !== null) {
                $setting = self::_loadSetting($name, $module, $scope);

                return (isset($setting[$uid]) && $setting[$uid] instanceof Setting) ? $setting[$uid]->getValue() : null;
            }
            if (self::$_settings === null) {
                self::loadSettings();
            }
            if ($uid > 0 && !array_key_exists($uid, self::$_loadedsettings)) {
                self::loadSettings($uid);
            }
            if (!is_array(self::$_settings) || !array_key_exists($module, self::$_settings)) {
                return null;
            }
            if (!array_key_exists($name, self::$_settings[$module])) {
                return null;
            }
            if ($uid !== 0 && array_key_exists($uid, self::$_settings[$module][$name])) {
                if ($return_object) {
                    return self::$_settings[$module][$name][$uid];
                } else {
                    return (self::$_settings[$module][$name][$uid] instanceof Setting) ? self::$_settings[$module][$name][$uid]->getValue() : null;
                }
            } else {
                if (!array_key_exists($uid, self::$_settings[$module][$name])) {
                    return null;
                }

                if ($return_object) {
                    return self::$_settings[$module][$name][$uid];
                } else {
                    return (self::$_settings[$module][$name][$uid] instanceof Setting) ? self::$_settings[$module][$name][$uid]->getValue() : null;
                }
            }
        }

        public static function loadSettings($uid = 0)
        {
            Logging::log("Loading settings");
            if (self::$_settings === null || ($uid > 0 && !array_key_exists($uid, self::$_loadedsettings))) {
                Logging::log('Loading settings');
                if (self::$_settings === null)
                    self::$_settings = [];

                Logging::log('Settings not cached or install mode enabled. Retrieving from database');
                $settings = tables\Settings::getTable()->getSettingsForScope(Context::getScope()->getID(), $uid);
                foreach ($settings as $setting) {
                    self::$_settings[$setting->getModuleKey()][$setting->getName()][$setting->getUserId()] = $setting;
                }

                if (!count($settings) && !Context::isInstallmode() && $uid == 0) {
                    Logging::log('Settings could not be retrieved from the database!', 'main', Logging::LEVEL_FATAL);
                    throw new SettingsException('Could not retrieve settings from database');
                }

                self::$_loadedsettings[$uid] = true;
                self::$_timezone = new DateTimeZone(self::getServerTimezoneIdentifier());
                Logging::log('Retrieved');
            }

            Logging::log("...done");
        }

        public static function getServerTimezoneIdentifier()
        {
            $timezone = self::get(self::SETTING_SERVER_TIMEZONE);

            if (is_numeric($timezone) || $timezone == null) {
                $timezone = date_default_timezone_get();
            }

            if (!$timezone) {
                throw new exceptions\ConfigurationException('No timezone specified, not even in php configuration.<br>For more information on how to fix this, see <a href="http://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone">php.net &raquo; Runtime configuration &raquo; date.timezone</a>');
            }

            return $timezone;
        }

        public static function saveUserSetting($user_id, $name, $value, $module = 'core', $scope = 0)
        {
            return self::saveSetting($name, $value, $module, $scope, $user_id);
        }

        public static function deleteUserSetting($user_id, $setting, $module = 'core', $scope = 0)
        {
            return self::deleteSetting($setting, $module, $scope, $user_id);
        }

        public static function deleteSetting($name, $module = 'core', $scope = null, $uid = null)
        {
            $scope = ($scope === null) ? Context::getScope()->getID() : $scope;
            $uid = ($uid === null) ? Context::getUser()->getID() : $uid;

            $query = tables\Settings::getTable()->getQuery();
            $query->where(tables\Settings::NAME, $name);
            $query->where(tables\Settings::MODULE, $module);
            $query->where(tables\Settings::SCOPE, $scope);
            $query->where(tables\Settings::USER_ID, $uid);

            tables\Settings::getTable()->rawDelete($query);
            unset(self::$_settings[$module][$name][$uid]);
        }

        public static function getMajorVer()
        {
            return self::$_ver_mj;
        }

        public static function getMinorVer()
        {
            return self::$_ver_mn;
        }

        public static function getRevision()
        {
            return self::$_ver_rev;
        }

        /**
         * Return the default admin group
         *
         * @return Group
         */
        public static function getAdminGroup()
        {
            return Group::getB2DBTable()->selectByID((int)self::get(self::SETTING_ADMIN_GROUP));
        }

        public static function getUserDisplaynameFormat()
        {
            $format = self::get(self::SETTING_USER_DISPLAYNAME_FORMAT);
            if (!is_numeric($format))
                $format = self::USER_DISPLAYNAME_FORMAT_BUDDY;

            return (int)$format;
        }

        public static function isGravatarsEnabled()
        {
            return (bool)self::get(self::SETTING_ENABLE_GRAVATARS);
        }

        public static function getHTMLLanguage()
        {
            $lang = explode('_', self::getLanguage());

            return $lang[0];
        }

        public static function getLanguage()
        {
            return self::get(self::SETTING_DEFAULT_LANGUAGE);
        }

        public static function getCharset()
        {
            return self::get(self::SETTING_DEFAULT_CHARSET);
        }

        public static function getHeaderIconURL()
        {
            return (self::isUsingCustomHeaderIcon()) ? Context::getRouting()->generate('showfile', ['id' => self::getHeaderIconID()]) : '/logo_192.png';
        }

        public static function isUsingCustomHeaderIcon()
        {
            return self::get(self::SETTING_HEADER_ICON_TYPE);
        }

        public static function getHeaderIconID()
        {
            return self::get(self::SETTING_HEADER_ICON_ID);
        }

        public static function getHeaderLink()
        {
            return self::get(self::SETTING_HEADER_LINK);
        }

        public static function getFaviconURL()
        {
            return (self::isUsingCustomFavicon()) ? Context::getRouting()->generate('showfile', ['id' => self::getFaviconID()]) : '/favicon_inverted.png';
        }

        public static function isUsingCustomFavicon()
        {
            return self::get(self::SETTING_FAVICON_TYPE);
        }

        public static function getFaviconID()
        {
            return self::get(self::SETTING_FAVICON_ID);
        }

        public static function getSiteHeaderName()
        {
            try {
                if (!Context::isReadySetup()) return 'Pachno';
                $name = self::get(self::SETTING_SITE_NAME);
                if (!self::isHeaderHtmlFormattingAllowed()) $name = htmlspecialchars($name, ENT_COMPAT, Context::getI18n()->getCharset());

                return trim($name);
            } catch (Exception $e) {
                return 'Pachno';
            }
        }

        public static function isHeaderHtmlFormattingAllowed()
        {
            return (bool)self::get(self::SETTING_SITE_NAME_HTML);
        }

        public static function getThemeName()
        {
            $themename = self::get(self::SETTING_THEME_NAME);
            if (!self::$_verified_theme) {
                if (!file_exists(PACHNO_PATH . 'themes' . DS . $themename . DS . 'theme.php')) {
                    self::saveSetting(self::SETTING_THEME_NAME, 'oxygen');
                    $themename = 'oxygen';
                }
                self::$_verified_theme = true;
            }

            return $themename;
        }

        public static function getIconsetName()
        {
            return self::get(self::SETTING_ICONSET);
        }

        public static function setIconsetName($iconset)
        {
            self::loadSettings();
            self::$_settings['core'][self::SETTING_ICONSET][0] = $iconset;
        }

        public static function isUserThemesEnabled()
        {
            return (bool)self::get(self::SETTING_ALLOW_USER_THEMES);
        }

        public static function isCommentTrailClean()
        {
            return false;
        }

        public static function isLoginRequired()
        {
            return (int) self::get(self::SETTING_REQUIRE_LOGIN);
        }

        public static function isElevatedLoginRequired()
        {
            return !(bool)self::get(self::SETTING_ELEVATED_LOGIN_DISABLED);
        }

        public static function isDefaultUserGuest()
        {
            return true;
        }

        /**
         * Return the default user
         *
         * @return User
         */
        public static function getDefaultUser()
        {
            try {
                return tables\Users::getTable()->selectByID(self::getDefaultUserID());
            } catch (Exception $e) {
                return null;
            }
        }

        public static function getDefaultUserID()
        {
            return (int) self::get(self::SETTING_DEFAULT_USER_ID);
        }

        public static function allowRegistration()
        {
            return self::isRegistrationAllowed();
        }

        public static function isRegistrationAllowed()
        {
            return self::isRegistrationEnabled();
        }

        public static function isRegistrationEnabled()
        {
            return (bool)self::get(self::SETTING_ALLOW_REGISTRATION);
        }

        public static function getRegistrationDomainWhitelist()
        {
            return trim(self::get(self::SETTING_REGISTRATION_DOMAIN_WHITELIST));
        }

        public static function hasRegistrationDomainWhitelist()
        {
            return (trim(self::get(self::SETTING_REGISTRATION_DOMAIN_WHITELIST)) !== '');
        }

        public static function getDefaultGroupIDs()
        {
            return [self::get(self::SETTING_ADMIN_GROUP), self::get(self::SETTING_GUEST_GROUP), self::get(self::SETTING_USER_GROUP)];
        }

        /**
         * Return the default user group
         *
         * @return Group
         */
        public static function getDefaultGroup()
        {
            try {
                return Group::getB2DBTable()->selectByID(self::get(self::SETTING_USER_GROUP));
            } catch (Exception $e) {
                return null;
            }
        }

        public static function getLoginReturnRoute()
        {
            return self::get(self::SETTING_RETURN_FROM_LOGIN);
        }

        public static function getLogoutReturnRoute()
        {
            return self::get(self::SETTING_RETURN_FROM_LOGOUT);
        }

        public static function isMaintenanceModeEnabled()
        {
            return (bool) self::get(self::SETTING_MAINTENANCE_MODE);
        }

        public static function hasMaintenanceMessage()
        {
            if (self::get(self::SETTING_MAINTENANCE_MESSAGE) == '') {
                return false;
            }

            return true;
        }

        public static function getMaintenanceMessage()
        {
            return self::get(self::SETTING_MAINTENANCE_MESSAGE);
        }

        /**
         * Return the "online" userstate object
         * @return Userstate
         */
        public static function getOnlineState()
        {
            try {
                return Userstate::getB2DBTable()->selectByID(self::get(self::SETTING_ONLINESTATE));
            } catch (Exception $e) {
                return null;
            }
        }

        /**
         * Return the "offline" userstate object
         * @return Userstate
         */
        public static function getOfflineState()
        {
            try {
                return Userstate::getB2DBTable()->selectByID(self::get(self::SETTING_OFFLINESTATE));
            } catch (Exception $e) {
                return null;
            }
        }

        /**
         * Return the "away" userstate object
         * @return Userstate
         */
        public static function getAwayState()
        {
            try {
                return Userstate::getB2DBTable()->selectByID(self::get(self::SETTING_AWAYSTATE));
            } catch (Exception $e) {
                return null;
            }
        }

        public static function getURLhost()
        {
            return Context::getScope()->getCurrentHostname();
        }

        public static function getGMToffset()
        {
            return self::get(self::SETTING_SERVER_TIMEZONE);
        }

        /**
         * @return DateTimeZone
         */
        public static function getServerTimezone()
        {
            return self::$_timezone;
        }

        public static function isUploadsEnabled()
        {
            return (bool)(Context::getScope()->isUploadsEnabled() && self::get(self::SETTING_ENABLE_UPLOADS));
        }

        public static function isUploadsImageCachingEnabled()
        {
            $caching = self::get(self::SETTING_UPLOAD_ALLOW_IMAGE_CACHING);

            return (($caching == null) ? false : (bool)$caching);
        }

        public static function isUploadsDeliveryUseXsend()
        {
            $useXsend = self::get(self::SETTING_UPLOAD_DELIVERY_USE_XSEND);

            return (($useXsend == null) ? false : (bool)$useXsend);
        }

        public static function getUploadsEffectiveMaxSize($bytes = false)
        {
            $ini_min = min((int)ini_get('upload_max_filesize'), (int)ini_get('post_max_size')) * ($bytes ? 1024 * 1024 : 1);

            return (0 == self::getUploadsMaxSize($bytes)) ? $ini_min : min($ini_min, self::getUploadsMaxSize($bytes));
        }

        public static function getUploadsMaxSize($bytes = false)
        {
            return ($bytes) ? (int)(self::get(self::SETTING_UPLOAD_MAX_FILE_SIZE) * 1024 * 1024) : (int)self::get(self::SETTING_UPLOAD_MAX_FILE_SIZE);
        }

        public static function getUploadsRestrictionMode()
        {
            return self::get(self::SETTING_UPLOAD_RESTRICTION_MODE);
        }

        public static function getUploadsExtensionsList()
        {
            $extensions = (string)self::get(self::SETTING_UPLOAD_EXTENSIONS_LIST);
            $delimiter = ' ';

            switch (true) {
                case (mb_strpos($extensions, ',') !== false):
                    $delimiter = ',';
                    break;
                case (mb_strpos($extensions, ';') !== false):
                    $delimiter = ';';
                    break;
            }

            return explode($delimiter, $extensions);
        }

        public static function getUploadStorage()
        {
            return self::get(self::SETTING_UPLOAD_STORAGE, 'core', self::getDefaultScopeID());
        }

        public static function getUploadsLocalpath()
        {
            $path = self::get(self::SETTING_UPLOAD_LOCAL_PATH, 'core', self::getDefaultScopeID());

            return (substr($path, -1, 1) == DS) ? $path : $path . DS;
        }

        public static function isInfoBoxVisible($key)
        {
            return !(bool)self::get(self::INFOBOX_PREFIX . $key, 'core', Context::getScope()->getID(), Context::getUser()->getID());
        }

        public static function hideInfoBox($key)
        {
            self::saveSetting(self::INFOBOX_PREFIX . $key, 1, 'core', Context::getScope()->getID(), Context::getUser()->getID());
        }

        public static function showInfoBox($key)
        {
            self::deleteSetting(self::INFOBOX_PREFIX . $key);
        }

        public static function setToggle($toggle, $state)
        {
            self::saveSetting(self::TOGGLE_PREFIX . $toggle, $state, 'core', Context::getScope()->getID(), Context::getUser()->getID());
        }

        public static function getToggle($toggle)
        {
            return (bool)self::get(self::TOGGLE_PREFIX . $toggle, 'core', Context::getScope()->getID(), Context::getUser()->getID());
        }

        public static function isPermissive()
        {
            return false;
        }

        public static function getAll()
        {
            return self::$_settings;
        }

        public static function getDefaultSyntaxHighlightingLanguage()
        {
            return 'html';
        }

        /**
         * @return AuthenticationBackend
         * @throws Exception
         *
         */
        public static function getAuthenticationBackend()
        {
            if (self::$_authentication_backend === null) {
                if (self::isUsingExternalAuthenticationBackend()) {
                    self::$_authentication_backend = Context::getModule(self::getAuthenticationBackendIdentifier())->getAuthenticationBackend();
                } else {
                    self::$_authentication_backend = new AuthenticationBackend();
                }
            }

            return self::$_authentication_backend;
        }

        /**
         * Whether or not the authentication backend is external
         *
         * @return boolean
         */
        public static function isUsingExternalAuthenticationBackend()
        {
            return (self::getAuthenticationBackendIdentifier() !== null && self::getAuthenticationBackendIdentifier() !== 'default');
        }

        public static function getAuthenticationBackendIdentifier()
        {
            return self::get(self::SETTING_AUTH_BACKEND);
        }

        /**
         * Get associated syntax class for a given syntax value
         *
         * @param integer $syntax
         *
         * @return string
         */
        public static function getSyntaxClass($syntax)
        {
            switch ($syntax) {
                case self::SYNTAX_EDITOR_JS:
                    return 'editor-js';
                case self::SYNTAX_MW:
                    return 'mw';
                case self::SYNTAX_PT:
                    return 'pt';
                case self::SYNTAX_MD:
                default:
                    return 'md';
            }
        }

        /**
         * Return syntax value for a given syntax shorthand
         *
         * @param string $syntax
         *
         * @return integer
         */
        public static function getSyntaxValue($syntax)
        {
            switch ($syntax) {
                case 'mw':
                    return self::SYNTAX_MW;
                case 'pt':
                    return self::SYNTAX_PT;
                case 'md':
                default:
                    return self::SYNTAX_MD;
            }
        }

        public static function getSubscriptionsSettings()
        {
            $i18n = Context::getI18n();
            $subscriptions_settings = [
                self::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ISSUES => $i18n->__('Issues posted by me'),
                self::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ARTICLES => $i18n->__('Articles / pages written by me'),
                self::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS => $i18n->__('New issues in my project(s)'),
                self::SETTINGS_USER_SUBSCRIBE_NEW_ARTICLES_MY_PROJECTS => $i18n->__('New articles / pages in my project(s)'),
                self::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY => $i18n->__('New issues in selected categories'),
                self::SETTINGS_USER_SUBSCRIBE_ASSIGNED_ISSUES => $i18n->__('Issues I am involved in'),
            ];

            return $subscriptions_settings;
        }

        public static function getNotificationSettings()
        {
            $i18n = Context::getI18n();
            $notification_settings = [
                self::SETTINGS_USER_NOTIFY_SUBSCRIBED_ISSUES => $i18n->__('Notify when there are updates to my subscribed issues'),
                self::SETTINGS_USER_NOTIFY_SUBSCRIBED_ARTICLES => $i18n->__('Notify when there are updates to my subscribed articles'),
                self::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS => $i18n->__('Notify when new issues are created in my project(s)'),
                self::SETTINGS_USER_NOTIFY_NEW_ARTICLES_MY_PROJECTS => $i18n->__('Notify when new articles are created in my project(s)'),
                self::SETTINGS_USER_NOTIFY_UPDATED_SELF => $i18n->__('Notify also when I am the one making the changes'),
                self::SETTINGS_USER_NOTIFY_MENTIONED => $i18n->__('Notify when I am mentioned in issue or article or their comment'),
                self::SETTINGS_USER_NOTIFY_ITEM_ONCE => $i18n->__('Only notify once per issue or article until I view the issue or article in my browser'),
                self::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY => $i18n->__('Notify when issues are created in selected categories'),
                self::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS => $i18n->__('Group similar notifications together if they are related'),
            ];

            return $notification_settings;
        }

        /**
         * File access listener
         *
         * @param Event $event
         */
        public static function listen_pachno_core_entities_File_hasAccess(Event $event)
        {
            $file = $event->getSubject();
            if ($file->getID() == self::getHeaderIconID() || $file->getID() == self::getFaviconID()) {
                $event->setReturnValue(true);
                $event->setProcessed();
            }
        }

        public static function getConfigSectionHeader(I18n $i18n, $section)
        {
            switch ($section) {
                case 'general':
                    return $i18n->__('System settings');
                case 'security':
                    return $i18n->__('Security and permissions');
                case 'users':
                    return $i18n->__('Users and collections');
                case 'projects':
                    return $i18n->__('Projects');
                case 'issues_workflow':
                    return $i18n->__('Issues and workflow');
                case self::CONFIGURATION_SECTION_MODULES:
                    return $i18n->__('Module settings');
            }

            return $i18n->__('Settings');
        }

        /**
         * Return associated configuration sections
         *
         * @param I18n $i18n The translation object
         *
         * @return array
         */
        public static function getConfigSections($i18n)
        {
            $config_sections = [
                'general' => [],
                'security' => [],
                'users' => [],
                'projects' => [],
                'issues_workflow' => [],
                //self::CONFIGURATION_SECTION_MODULES => []
            ];

            if (Context::getScope()->isDefault()) {
                $config_sections['general'][self::CONFIGURATION_SECTION_SCOPES] = ['route' => 'configure_scopes', 'description' => $i18n->__('Scopes'), 'fa_style' => 'fas', 'fa_icon' => 'clone', 'details' => $i18n->__('Scopes are self-contained Pachno environments. Configure them here.')];
            }

            $config_sections['general'][self::CONFIGURATION_SECTION_SETTINGS] = ['route' => 'configure_settings', 'description' => $i18n->__('Settings'), 'fa_style' => 'fas', 'fa_icon' => 'cog', 'details' => $i18n->__('Every setting in Pachno can be adjusted in this section.')];

            if (Context::getScope()->isUploadsEnabled()) {
                $config_sections['general'][self::CONFIGURATION_SECTION_UPLOADS] = ['route' => 'configure_files', 'description' => $i18n->__('Uploads and attachments'), 'fa_style' => 'fas', 'fa_icon' => 'upload', 'details' => $i18n->__('All settings related to file uploads are controlled from this section.')];
            }
            $config_sections['general'][self::CONFIGURATION_SECTION_MAILING] = ['route' => ['configure_module', ['config_module' => 'mailing']], 'description' => Context::geti18n()->__(Context::getModule('mailing')->getConfigTitle()), 'icon' => Context::getModule('mailing')->getName(), 'details' => Context::geti18n()->__(Context::getModule('mailing')->getConfigDescription()), 'module' => Context::getModule('mailing')->getName(), 'fa_style' => Context::getModule('mailing')->getFontAwesomeStyle(), 'fa_icon' => Context::getModule('mailing')->getFontAwesomeIcon()];

            $config_sections['general'][self::CONFIGURATION_SECTION_MODULES] = ['route' => 'configure_modules', 'description' => $i18n->__('Manage modules'), 'fa_style' => 'fas', 'fa_icon' => 'puzzle-piece', 'details' => $i18n->__('Manage Pachno extensions from this section. New modules are installed from here.'), 'module' => 'core'];

            $config_sections['security'][self::CONFIGURATION_SECTION_AUTHENTICATION] = ['route' => 'configure_authentication', 'disabled' => true, 'description' => $i18n->__('Authentication'), 'fa_style' => 'fas', 'fa_icon' => 'lock', 'details' => $i18n->__('Configure the authentication method in this section')];
            $config_sections['security'][self::CONFIGURATION_SECTION_ROLES] = ['route' => 'configure_roles', 'description' => $i18n->__('Roles'), 'fa_style' => 'fas', 'fa_icon' => 'user-tie', 'details' => $i18n->__('Configure roles in this section')];

            $config_sections['projects'][self::CONFIGURATION_SECTION_PROJECTS] = ['route' => 'configure_projects', 'description' => $i18n->__('Projects'), 'fa_style' => 'fas', 'fa_icon' => 'code', 'details' => $i18n->__('Set up all projects in this configuration section.')];
            //$config_sections['projects'][self::CONFIGURATION_SECTION_IMPORT] = ['route' => 'import_home', 'disabled' => true, 'description' => $i18n->__('Import data'), 'fa_style' => 'fas', 'fa_icon' => 'download', 'details' => $i18n->__('Import data from CSV files and other sources.')];
            if (tables\IssuetypeSchemes::getTable()->getNumberOfSchemesInCurrentScope() > 1) {
                $config_sections['issues_workflow'][self::CONFIGURATION_SECTION_ISSUETYPES] = ['route' => 'configure_issuetypes', 'fa_style' => 'fas', 'fa_icon' => 'copy', 'description' => $i18n->__('Issue types'), 'details' => $i18n->__('Manage issue types and configure issue fields for each issue type here')];
                $config_sections['issues_workflow'][self::CONFIGURATION_SECTION_ISSUETYPE_SCHEMES] = ['route' => 'configure_issuetypes_schemes', 'fa_style' => 'fas', 'fa_icon' => 'copy', 'description' => $i18n->__('Issue type schemes'), 'details' => $i18n->__('Manage issue types and configure issue fields for each issue type here')];
            } else {
                $config_sections['issues_workflow'][self::CONFIGURATION_SECTION_ISSUETYPES] = ['route' => 'configure_issuetypes_schemes', 'fa_style' => 'fas', 'fa_icon' => 'copy', 'description' => $i18n->__('Issue types'), 'details' => $i18n->__('Manage issue types and configure issue fields for each issue type here')];
            }
            $config_sections['issues_workflow'][self::CONFIGURATION_SECTION_ISSUEFIELDS] = ['route' => 'configure_issuefields', 'fa_style' => 'fas', 'fa_icon' => 'list', 'description' => $i18n->__('Issue fields'), 'details' => $i18n->__('Status types, resolution types, categories, custom fields, etc. are configurable from this section.')];
            $config_sections['issues_workflow'][self::CONFIGURATION_SECTION_WORKFLOW] = ['route' => 'configure_workflows', 'fa_style' => 'fas', 'fa_icon' => 'share-alt', 'description' => $i18n->__('Workflow'), 'details' => $i18n->__('Set up and edit workflow configuration from this section')];
            $config_sections['issues_workflow'][self::CONFIGURATION_SECTION_WORKFLOW_SCHEMES] = ['route' => 'configure_workflow_schemes', 'fa_style' => 'fas', 'fa_icon' => 'share-alt', 'description' => $i18n->__('Workflow schemes'), 'details' => $i18n->__('Set up and edit workflow configuration from this section')];
            $config_sections['users'][self::CONFIGURATION_SECTION_USERS] = ['route' => 'configure_users', 'description' => $i18n->__('Users and groups'), 'fa_style' => 'fas', 'fa_icon' => 'users', 'details' => $i18n->__('Create, edit and manage users from this section')];
            $config_sections['users'][self::CONFIGURATION_SECTION_TEAMS] = ['route' => 'configure_teams', 'description' => $i18n->__('User teams'), 'fa_style' => 'fas', 'fa_icon' => 'users', 'details' => $i18n->__('Create and manage teams from this section.')];
            $config_sections['users'][self::CONFIGURATION_SECTION_CLIENTS] = ['route' => 'configure_clients', 'description' => $i18n->__('Clients'), 'fa_style' => 'fas', 'fa_icon' => 'users', 'details' => $i18n->__('Create and manage clients from this section.')];

            foreach (Context::getAllModules() as $modules) {
                foreach ($modules as $module) {
                    if ($module->hasConfigSettings() && !$module instanceof CoreModule && $module->isEnabled()) {
                        $module_array = ['route' => ['configure_module', ['config_module' => $module->getName()]], 'description' => Context::geti18n()->__($module->getConfigTitle()), 'icon' => $module->getName(), 'details' => Context::geti18n()->__($module->getConfigDescription()), 'module' => $module->getName()];
                        if ($module->hasFontAwesomeIcon()) {
                            $module_array['fa_icon'] = $module->getFontAwesomeIcon();
                            $module_array['fa_style'] = $module->getFontAwesomeStyle();
                            $module_array['fa_color'] = $module->getFontAwesomeColor();
                        }
                        $config_sections[self::CONFIGURATION_SECTION_MODULES][] = $module_array;
                    }
                }
            }

            return $config_sections;
        }

        public static function getConfigurationAccessLevel()
        {
            return (Context::getUser()->canSaveConfiguration()) ? self::ACCESS_FULL : self::ACCESS_READ;
        }

        public static function isStable(): bool
        {
            return false;
        }

    }
