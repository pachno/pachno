<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use pachno\core\entities\SavedSearch;
    use pachno\core\framework;

    /**
     * @Table(name="savedsearches")
     * @Entity(class="\pachno\core\entities\SavedSearch")
     */
    class SavedSearches extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'savedsearches';

        public const ID = 'savedsearches.id';

        public const SCOPE = 'savedsearches.scope';

        public const NAME = 'savedsearches.name';

        public const DESCRIPTION = 'savedsearches.description';

        public const GROUPBY = 'savedsearches.groupby';

        public const GROUPORDER = 'savedsearches.grouporder';

        public const ISSUES_PER_PAGE = 'savedsearches.issues_per_page';

        public const TEMPLATE_NAME = 'savedsearches.templatename';

        public const TEMPLATE_PARAMETER = 'savedsearches.templateparameter';

        public const APPLIES_TO_PROJECT = 'savedsearches.applies_to_project';

        public const IS_PUBLIC = 'savedsearches.is_public';

        public const USER_ID = 'savedsearches.uid';

        /**
         * @param $user_id
         * @param int $project_id
         *
         * @return SavedSearch[][]
         */
        public function getAllSavedSearchesByUserIDAndPossiblyProjectID($user_id, $project_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::USER_ID, $user_id);
            $criteria->or(self::USER_ID, 0);
            $query->and($criteria);

            if ($project_id !== 0) {
                $query->where(self::APPLIES_TO_PROJECT, $project_id);
            } else {
                $query->where(self::APPLIES_TO_PROJECT, 0);
            }

            $retarr = ['user' => [], 'public' => []];

            if ($res = $this->select($query, 'none')) {
                foreach ($res as $id => $search) {
                    if ($search->getUserID() == 0 && !$search->isPublic()) continue;

                    $retarr[($search->getUserID() != 0) ? 'user' : 'public'][$id] = $search;
                }
            }

            return $retarr;
        }

    }
