<?php

    namespace pachno\core\modules\debugger;

    use pachno\core\framework\CoreModule;

    class Debugger extends CoreModule
    {
        protected $variables = [];

        public function watch($key, $variable)
        {
            $this->variables[$key] = $variable;
        }

        public function getWatchedVariables()
        {
            return $this->variables;
        }
    }
