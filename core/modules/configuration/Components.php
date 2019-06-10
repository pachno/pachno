<?php

    namespace pachno\core\modules\configuration;

    use pachno\core\framework,
        pachno\core\entities;

    class Components extends framework\ActionComponent
    {

        public function componentEditIssueType()
        {
            $this->icons = entities\Issuetype::getIcons();
        }

        public function componentModulebox()
        {
            $this->is_default_scope = (isset($this->is_default_scope)) ? $this->is_default_scope : framework\Context::getScope()->isDefault();
        }

        public function componentOnlineModules()
        {
            try
            {
                $client = new \Net_Http_Client();
                $client->get('https://pachno.com/addons.json');
                $json_modules = json_decode($client->getBody());
            }
            catch (\Exception $e) {}

            $modules = array();
            if (isset($json_modules) && isset($json_modules->featured)) {
                foreach ($json_modules->featured as $key => $module) {
                    if (!framework\Context::isModuleLoaded($module->key))
                        $modules[] = $module;
                }
            }

            $this->modules = $modules;
        }

        public function componentOnlineThemes()
        {
            try
            {
                $client = new \Net_Http_Client();
                $client->get('https://pachno.com/themes.json');
                $json_themes = json_decode($client->getBody());
            }
            catch (\Exception $e) {}

            $themes = array();
            $existing_themes = framework\Context::getThemes();
            if (isset($json_themes) && isset($json_themes->featured)) {
                foreach ($json_themes->featured as $key => $theme) {
                    if (!array_key_exists($theme->key, $existing_themes))
                        $themes[] = $theme;
                }
            }

            $this->themes = $themes;
        }

        public function componentTheme()
        {
            $this->enabled = (framework\Settings::getThemeName() == $this->theme['key']);
            $this->is_default_scope = framework\Context::getScope()->isDefault();
        }

        public function componentLanguageSettings()
        {
            $this->languages = framework\I18n::getLanguages();
            $this->timezones = \pachno\core\framework\I18n::getTimezones();
        }

        public function componentOffline()
        {

        }

        public function componentSidebar()
        {
            $config_sections = framework\Settings::getConfigSections(framework\Context::getI18n());
            $breadcrumblinks = array();
            foreach ($config_sections as $key => $sections) {
                foreach ($sections as $section) {
                    if ($key == framework\Settings::CONFIGURATION_SECTION_MODULES) {
                        $url = (is_array($section['route'])) ? make_url($section['route'][0], $section['route'][1]) : make_url($section['route']);
                        $breadcrumblinks[] = array('url' => $url, 'title' => $section['description']);
                    } else {
                        $breadcrumblinks[] = array('url' => make_url($section['route']), 'title' => $section['description']);
                    }
                }
            }
            $this->breadcrumblinks = $breadcrumblinks;

            $this->config_sections = $config_sections;
            if ($this->selected_section == framework\Settings::CONFIGURATION_SECTION_MODULES) {
                if (framework\Context::getRouting()->getCurrentRouteName() == 'configure_modules') {
                    $this->selected_subsection = 'core';
                } else {
                    $this->selected_subsection = framework\Context::getRequest()->getParameter('config_module');
                }
            }
        }

        public function componentEditScope()
        {
            if ($this->scope->getId()) {
                $modules = entities\tables\Modules::getTable()->getModulesForScope($this->scope->getID());
                $this->modules = $modules;
            }
        }

        public function componentEditIssueField()
        {
            $this->items = array();
            $this->showitems = true;
            $this->iscustom = false;
            $types = entities\Datatype::getTypes();

            if (array_key_exists($this->type, $types))
            {
                $this->items = call_user_func(array($types[$this->type], 'getAll'));
            }
            else
            {
                $customtype = entities\CustomDatatype::getByKey($this->type);
                $this->showitems = $customtype->hasCustomOptions();
                $this->iscustom = true;
                if ($this->showitems)
                {
                    $this->items = $customtype->getOptions();
                }
                $this->customtype = $customtype;
            }
        }

        public function componentIssueFieldPermissions()
        {

        }

        public function componentPermissionsPopup()
        {

        }

        public function componentIssueTypeSchemeOptions()
        {
            $this->builtin_fields = entities\Datatype::getAvailableFields(true);
            $this->custom_fields = entities\CustomDatatype::getAll();
            $this->visible_fields = $this->scheme->getVisibleFieldsForIssuetype($this->issue_type);
        }

        public function componentIssueType()
        {
            $this->icons = entities\Issuetype::getIcons();
        }

        public function componentIssuetypescheme()
        {

        }

        public function componentIssueFields_CustomType()
        {

        }

        public function componentPermissionsinfo()
        {
            switch ($this->mode)
            {
                case 'datatype':

                    break;
            }
        }

        public function componentPermissionsinfoitem()
        {

        }

        protected function _getPermissionListFromKey($key, $permissions = null)
        {
            if ($permissions === null)
            {
                $permissions = framework\Context::getAvailablePermissions();
            }
            foreach ($permissions as $pkey => $permission)
            {
                if ($pkey == $key)
                {
                    return (array_key_exists('details', $permission)) ? $permission['details'] : array();
                }
                elseif (array_key_exists('details', $permission) && count($permission['details']) > 0 && ($plist = $this->_getPermissionListFromKey($key, $permission['details'])))
                {
                    return $plist;
                }
            }
            return array();
        }

        public function componentPermissionsblock()
        {
            if (!is_array($this->permissions_list))
            {
                $this->permissions_list = $this->_getPermissionListFromKey($this->permissions_list);
            }
        }

        public function componentPermissionsConfigurator()
        {
            $this->base_id = (isset($this->base_id)) ? $this->base_id : 0;
            $this->user_id = (isset($this->user_id)) ? $this->user_id : 0;
            $this->team_id = (isset($this->team_id)) ? $this->team_id : 0;
            $this->mode = ($this->user_id) ? 'user' : 'team';
        }

        public function componentWorkflowtransitionaction()
        {
            $available_assignees_users = array();
            foreach (framework\Context::getUser()->getTeams() as $team)
            {
                foreach ($team->getMembers() as $user)
                {
                    $available_assignees_users[$user->getID()] = $user;
                }
            }
            foreach (framework\Context::getUser()->getFriends() as $user)
            {
                $available_assignees_users[$user->getID()] = $user;
            }
            $this->available_assignees_teams = entities\Team::getAll();
            $this->available_assignees_users = $available_assignees_users;
        }

        public function componentUserscopes()
        {
            $this->scopes = entities\Scope::getAll();
        }

        public function componentSiteicons()
        {

        }

    }
