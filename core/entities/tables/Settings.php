<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Query;
    use b2db\Row;
    use b2db\Update;
    use pachno\core\entities\Scope;
    use pachno\core\entities\Setting;
    use pachno\core\framework;
    use RuntimeException;

    /**
     * Settings table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Settings table
     *
     * @method Setting[] select(Query $query, $join = 'all')
     * @method Setting selectOne(Query $query, $join = 'all')
     *
     * @Table(name="settings")
     * @Entity(class="\pachno\core\entities\Setting")
     */
    class Settings extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'settings';

        const ID = 'settings.id';

        const SCOPE = 'settings.scope';

        const NAME = 'settings.name';

        const MODULE = 'settings.module';

        const VALUE = 'settings.value';

        const UPDATED_AT = 'settings.updated_at';

        const UID = 'settings.uid';

        /**
         * @param $scope
         * @param int $uid
         *
         * @return Setting[]
         */
        public function getSettingsForScope($scope, $uid = 0)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $uid);

            $criteria = new Criteria();
            $criteria->where(self::SCOPE, $scope);
            $criteria->or(self::SCOPE, 0);
            $query->and($criteria);

            return $this->select($query, 'none');
        }

        /**
         * @param $name
         * @param $module
         * @param $uid
         * @param $scope
         *
         * @return Setting
         */
        public function getSetting($name, $module, $uid, $scope): ?Setting
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::MODULE, $module);
            $query->where(self::UID, $uid);
            $query->where(self::SCOPE, $scope);

            return $this->selectOne($query, 'none');
        }

        public function preventDuplicate(Setting $setting)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $setting->getName());
            $query->where(self::MODULE, $setting->getModuleKey());
            $query->where(self::UID, $setting->getUserId());
            $query->where(self::SCOPE, $setting->getScope()->getID());

            if ($setting->getID()) {
                $query->where(self::ID, $setting->getID(), Criterion::NOT_EQUALS);
            }

            if ($this->count($query)) {
                throw new RuntimeException('Cannot save duplicate settings object');
            }
        }

        public function deleteModuleSettings($module_name, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::MODULE, $module_name);
            $query->where(self::SCOPE, $scope);
            $this->rawDelete($query);
        }

        public function deleteAllUserModuleSettings($module_name, $scope = null)
        {
            $query = $this->getQuery();
            $query->where(self::MODULE, $module_name);
            $query->where(self::UID, 0, Criterion::GREATER_THAN);
            if ($scope !== null) {
                $query->where(self::SCOPE, $scope);
            }
            $this->rawDelete($query);
        }

        public function loadFixtures(Scope $scope)
        {
            $i18n = framework\Context::getI18n();

            $settings = [];
            $settings[framework\Settings::SETTING_THEME_NAME] = 'oxygen';
            $settings[framework\Settings::SETTING_REQUIRE_LOGIN] = false;
            $settings[framework\Settings::SETTING_DEFAULT_USER_IS_GUEST] = true;
            $settings[framework\Settings::SETTING_ALLOW_REGISTRATION] = true;
            $settings[framework\Settings::SETTING_RETURN_FROM_LOGIN] = 'referer';
            $settings[framework\Settings::SETTING_RETURN_FROM_LOGOUT] = 'home';
            $settings[framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW] = true;
            $settings[framework\Settings::SETTING_ALLOW_USER_THEMES] = false;
            $settings[framework\Settings::SETTING_ENABLE_UPLOADS] = false;
            $settings[framework\Settings::SETTING_ENABLE_GRAVATARS] = true;
            $settings[framework\Settings::SETTING_UPLOAD_RESTRICTION_MODE] = 'blacklist';
            $settings[framework\Settings::SETTING_UPLOAD_EXTENSIONS_LIST] = 'exe,bat,php,asp,jsp';
            $settings[framework\Settings::SETTING_UPLOAD_STORAGE] = 'files';
            $settings[framework\Settings::SETTING_UPLOAD_LOCAL_PATH] = PACHNO_PATH . 'files/';
            $settings[framework\Settings::SETTING_UPLOAD_ALLOW_IMAGE_CACHING] = false;
            $settings[framework\Settings::SETTING_UPLOAD_DELIVERY_USE_XSEND] = false;
            $settings[framework\Settings::SETTING_SITE_NAME] = 'Pachno';
            $settings[framework\Settings::SETTING_ICONSET] = 'oxygen';
            $settings[framework\Settings::SETTING_SERVER_TIMEZONE] = date_default_timezone_get();
            $settings[framework\Settings::SETTING_ELEVATED_LOGIN_DISABLED] = true;

            $scope_id = $scope->getID();
            foreach ($settings as $settings_name => $settings_val) {
                $this->saveSetting($settings_name, 'core', $settings_val, 0, $scope_id);
            }
        }

        public function saveSetting($name, $module, $value, $uid, $scope)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::MODULE, $module);
            $query->where(self::UID, $uid);
            $query->where(self::SCOPE, $scope);
            $res = $this->rawSelectOne($query);

            if ($res instanceof Row) {
                $theID = $res->get(self::ID);
                $query = $this->getQuery();
                $query->where(self::NAME, $name);
                $query->where(self::MODULE, $module);
                $query->where(self::UID, $uid);
                $query->where(self::SCOPE, $scope);
                $query->where(self::ID, $theID, Criterion::NOT_EQUALS);
                $res2 = $this->rawDelete($query);

                $update = new Update();
                $update->add(self::NAME, $name);
                $update->add(self::MODULE, $module);
                $update->add(self::UID, $uid);
                $update->add(self::VALUE, $value);
                $update->add(self::UPDATED_AT, time());
                $this->rawUpdateById($update, $theID);
            } else {
                $insertion = new Insertion();
                $insertion->add(self::NAME, $name);
                $insertion->add(self::MODULE, $module);
                $insertion->add(self::VALUE, $value);
                $insertion->add(self::SCOPE, $scope);
                $insertion->add(self::UID, $uid);
                $insertion->add(self::UPDATED_AT, time());
                $this->rawInsert($insertion);
            }
        }

        public function getFileIds()
        {
            $query = $this->getQuery();
            $file_id_settings = [
                framework\Settings::SETTING_FAVICON_ID,
                framework\Settings::SETTING_HEADER_ICON_ID
            ];
            $query->where(self::NAME, $file_id_settings, Criterion::IN);
            $query->addSelectionColumn(self::VALUE, 'file_id');

            $res = $this->rawSelect($query);
            $file_ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file_ids[$row['file_id']] = $row['file_id'];
                }
            }

            return $file_ids;
        }

        public function migrateSettings()
        {
            $query = $this->getQuery();
            $query->where(self::VALUE, 'The Bug Genie');

            $update = new Update();
            $update->update(self::VALUE, 'Pachno');
            $this->rawUpdate($update, $query);
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope_uid', [self::SCOPE, self::UID]);
        }

    }
