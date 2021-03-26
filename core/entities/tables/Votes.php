<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use b2db\Query;
    use pachno\core\framework;

    /**
     * Votes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Votes table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="votes")
     */
    class Votes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'votes';

        const ID = 'votes.id';

        const SCOPE = 'votes.scope';

        const TARGET = 'votes.target';

        const VOTE = 'votes.vote';

        const USER_ID = 'votes.uid';

        public function getVoteSumForIssue($issue_id)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::VOTE, 'votes_total', Query::DB_SUM);
            $query->where(self::TARGET, $issue_id);
            $res = $this->rawSelectOne($query, false);

            return ($res) ? $res->get('votes_total') : 0;
        }

        public function getByIssueId($issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::TARGET, $issue_id);
            $res = $this->rawSelect($query, false);

            return $res;
        }

        public function addByUserIdAndIssueId($user_id, $issue_id, $up = true)
        {
            $query = $this->getQuery();
            $query->where(self::TARGET, $issue_id);
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);

            $insertion = new Insertion();
            $insertion->add(self::TARGET, $issue_id);
            $insertion->add(self::USER_ID, $user_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::VOTE, (($up) ? 1 : -1));
            $res = $this->rawInsert($insertion);

            return $res->getInsertID();
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addInteger(self::TARGET, 10);
            parent::addInteger(self::VOTE, 2);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
        }

    }
