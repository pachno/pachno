<?php

    namespace pachno\core\modules\profile;

    use pachno\core\entities;
    use pachno\core\entities\tables;
    use pachno\core\framework;

    /**
     * Class Components
     *
     * @property entities\User $user
     * @property entities\Issue[] $issues
     *
     * @package pachno\core\modules\user
     */
    class Components extends framework\ActionComponent
    {

        public function componentUsercard()
        {
            $this->issues = $this->user->getIssues(5);
        }

    }
