<?php

    namespace pachno\core\modules\livelink;

    use pachno\core\framework\Context;

    abstract class BaseConnector
    {

        protected function getI18n()
        {
            return Context::getI18n();
        }

        abstract public function getName();

        abstract public function getLogo();

        abstract public function getLogoStyle();

        abstract public function getProjectTemplateDescription();

        abstract public function doesSupportImportIssues();

        abstract public function doesSupportLinkIssues();

        abstract public function isConfigured();

    }