<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\QueryColumnSort;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\Milestone;
    use pachno\core\framework;

    /**
     * Milestones table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Milestones getTable() Retrieves an instance of this table
     * @method Milestone selectById(integer $id) Retrieves a milestone
     *
     * @Table(name="milestones")
     * @Entity(class="\pachno\core\entities\Milestone")
     */
    class Milestones extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'milestones';

        const ID = 'milestones.id';

        const SCOPE = 'milestones.scope';

        const NAME = 'milestones.name';

        const PROJECT = 'milestones.project';

        const DESCRIPTION = 'milestones.description';

        const MILESTONE_TYPE = 'milestones.itemtype';

        const REACHED = 'milestones.reacheddate';

        const STARTING = 'milestones.startingdate';

        const SCHEDULED = 'milestones.scheduleddate';

        const PERCENTAGE_TYPE = 'milestones.percentage_type';

        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function doesIDExist($userid)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $userid);

            return $this->count($query);
        }

        public function setReached($milestone_id)
        {
            $update = new Update();
            $update->add(self::REACHED, NOW);
            $this->rawUpdateById($update, $milestone_id);
        }

        public function clearReached($milestone_id)
        {
            $update = new Update();
            $update->add(self::REACHED, null);
            $this->rawUpdateById($update, $milestone_id);
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return [];

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, Criterion::IN);

            return $this->select($query);
        }

        /**
         * @return Milestone[]
         */
        public function selectAll()
        {
            $query = $this->getQuery();

            $query->join(Projects::getTable(), Projects::ID, self::PROJECT);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(Projects::NAME, QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        protected function migrateData(Table $old_table)
        {
            $update = new Update();
            $update->add('milestones.visible_issues', true);
            $update->add('milestones.visible_roadmap', true);

            $this->rawUpdate($update);
        }

    }
