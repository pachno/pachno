<?php

    namespace pachno\core\modules\auth_ldap;

    use pachno\core\framework\ActionComponent;

    /**
     * action components for the ldap_authentication module
     */
    class Components extends ActionComponent
    {

        public function componentSettings()
        {
            if (!extension_loaded('ldap')) {
                $this->noldap = true;
            } else {
                $this->noldap = false;
            }
        }

    }
