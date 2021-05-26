<?php

    namespace pachno\core\modules\configuration;

    use Exception;
    use Net_Http_Client;
    use pachno\core\entities;
    use pachno\core\framework;
    use pachno\core\framework\I18n;
    use pachno\core\entities\tables;

    /**
     * Class Components
     *
     * @property ?entities\User[] $users
     * @property ?entities\User $email_user
     * @property ?string $find_by
     * @property ?entities\Client $client
     * @property ?entities\Team $team
     * @property ?entities\Role $role
     * @property ?string $members_url
     * @property ?string $form_url
     *
     * @package pachno\core\modules\configuration
     */
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
            try {
                $client = new Net_Http_Client();
                $client->get('https://pachno.com/addons.json');
                $json_modules = json_decode($client->getBody());
            } catch (Exception $e) {
            }

            $modules = [];
            if (isset($json_modules) && isset($json_modules->featured)) {
                foreach ($json_modules->featured as $key => $module) {
                    if (!framework\Context::isModuleLoaded($module->key))
                        $modules[] = $module;
                }
            }

            $this->modules = $modules;
        }

        public function componentEditClient()
        {
            $this->members_url = $this->getRouting()->generate('configure_client_members', ['client_id' => $this->client->getID()]);
            $this->form_url = ($this->client->getID()) ? $this->getRouting()->generate('configure_client', ['client_id' => $this->client->getID()]) : $this->getRouting()->generate('configure_clients');
        }

        public function componentEditTeam()
        {
            $this->members_url = $this->getRouting()->generate('configure_team_members', ['team_id' => $this->team->getID()]);
            $this->form_url = ($this->team->getID()) ? $this->getRouting()->generate('configure_team', ['team_id' => $this->team->getID()]) : $this->getRouting()->generate('configure_teams');
        }

        public function componentEditRole()
        {
            $this->form_url = ($this->role->getID()) ? $this->getRouting()->generate('configure_role', ['role_id' => $this->role->getID()]) : $this->getRouting()->generate('configure_roles');
        }

        public function componentFindClientMembers()
        {
            $this->users = tables\Users::getTable()->getByDetails($this->find_by, 10, true);

            if (filter_var($this->find_by, FILTER_VALIDATE_EMAIL) == $this->find_by) {
                $email = $this->find_by;
            }

            if (isset($email) && !count($this->users)) {
                $email_user = new entities\User();
                $email_user->setEmail($email);
                $this->email_user = $email_user;
            }
        }

        public function componentFindTeamMembers()
        {
            $this->users = tables\Users::getTable()->getByDetails($this->find_by, 10, true);

            if (filter_var($this->find_by, FILTER_VALIDATE_EMAIL) == $this->find_by) {
                $email = $this->find_by;
            }

            if (isset($email) && !count($this->users)) {
                $email_user = new entities\User();
                $email_user->setEmail($email);
                $this->email_user = $email_user;
            }
        }

        public function componentLanguageSettings()
        {
            $this->languages = I18n::getLanguages();
            $this->timezones = I18n::getTimezones();
        }

        public function componentOffline()
        {

        }

        public function componentSidebar()
        {
            $config_sections = framework\Settings::getConfigSections(framework\Context::getI18n());
            $selected_category = 'general';
            foreach ($config_sections as $key => $sections) {
                foreach ($sections as $section_key => $section) {
                    if ($section_key == $this->selected_section) {
                        $selected_category = $key;
                    }
                }
            }
            $this->selected_category = $selected_category;

            $this->config_sections = $config_sections;
            if ($this->selected_section == framework\Settings::CONFIGURATION_SECTION_MODULES) {
                if (framework\Context::getRouting()->getCurrentRoute()->getName() == 'configure_modules') {
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

        public function componentTransitionStatusBadges()
        {
            $this->statuses = entities\Status::getAll();
        }

        public function componentEditWorkflowScheme()
        {
            $this->issue_types = entities\Issuetype::getAll();
            if (isset($this->clone)) {
                $this->scheme->setName($this->getI18n()->__('Copy of %name', ['%name' => $this->scheme->getName()]));
            }
        }

        public function componentEditWorkflowTransitionPopup()
        {
            $this->workflow = $this->transition->getWorkflow();
        }

        public function componentEditIssueField()
        {
            $this->showitems = false;
            $this->iscustom = false;
            $types = entities\Datatype::getTypes();
            $this->access_level = framework\Settings::getConfigurationAccessLevel(framework\Settings::CONFIGURATION_SECTION_ISSUEFIELDS);

            if (array_key_exists($this->type, $types)) {
                $this->items = call_user_func([$types[$this->type], 'getAll']);
                $this->showitems = true;
            } elseif (!array_key_exists($this->type, entities\DatatypeBase::getAvailableFields(true))) {
                $customtype = entities\CustomDatatype::getByKey($this->type);
                $this->showitems = $customtype->hasCustomOptions();
                $this->iscustom = true;
                if ($this->showitems) {
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
            $this->builtin_fields = array_keys(entities\DatatypeBase::getAvailableFields(true));
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
            switch ($this->mode) {
                case 'datatype':

                    break;
            }
        }

        public function componentPermissionsinfoitem()
        {

        }

        public function componentPermissionsblock()
        {
            if (!is_array($this->permissions_list)) {
                $this->permissions_list = $this->_getPermissionListFromKey($this->permissions_list);
            }
        }

        protected function _getPermissionListFromKey($key, $permissions = null)
        {
            if ($permissions === null) {
                $permissions = framework\Context::getAvailablePermissions();
            }
            foreach ($permissions as $pkey => $permission) {
                if ($pkey == $key) {
                    return (array_key_exists('details', $permission)) ? $permission['details'] : [];
                } elseif (array_key_exists('details', $permission) && count($permission['details']) > 0 && ($plist = $this->_getPermissionListFromKey($key, $permission['details']))) {
                    return $plist;
                }
            }

            return [];
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
            $available_assignees_users = [];
            foreach (framework\Context::getUser()->getTeams() as $team) {
                foreach ($team->getMembers() as $user) {
                    $available_assignees_users[$user->getID()] = $user;
                }
            }
            foreach (framework\Context::getUser()->getFriends() as $user) {
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
