<?php

    namespace pachno\core\entities\tables;

    use b2db\Core;
    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Query;
    use pachno\core\entities\ArticleRevision;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * @Table(name="articlehistory")
     * @Entity(class="\pachno\core\entities\ArticleRevision")
     */
    class ArticleHistory extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'articlehistory';

        public const ID = 'articlehistory.id';

        public const ARTICLE_NAME = 'articlehistory.article_name';

        public const OLD_CONTENT = 'articlehistory.old_content';

        public const NEW_CONTENT = 'articlehistory.new_content';

        public const REASON = 'articlehistory.reason';

        public const REVISION = 'articlehistory.revision';

        public const DATE = 'articlehistory.date';

        public const AUTHOR = 'articlehistory.author';

        public const SCOPE = 'articlehistory.scope';

        public function deleteHistoryByArticle($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        /**
         * @param $article_id
         * @return ArticleRevision
         */
        public function getLatestByArticleId($article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function addArticleHistory($article_name, $old_content, $new_content, $user_id, $reason = null)
        {
            if (!Core::isTransactionActive()) $transaction = Core::startTransaction();
            $insertion = new Insertion();
            $insertion->add(self::ARTICLE_NAME, $article_name);
            $insertion->add(self::AUTHOR, $user_id);
            $revision_number = $this->_getNextRevisionNumberForArticle($article_name);
            $insertion->add(self::REVISION, $revision_number);

            if (!($revision_number == 1 && $old_content == $new_content)) {
                $insertion->add(self::OLD_CONTENT, $old_content);
            } else {
                $insertion->add(self::OLD_CONTENT, '');
            }
            $insertion->add(self::NEW_CONTENT, $new_content);

            if ($reason !== null) {
                $insertion->add(self::REASON, $reason);
            }

            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::DATE, NOW);

            $res = $this->rawInsert($insertion);
            if (isset($transaction)) $transaction->commitAndEnd();

            return $revision_number;
        }

        protected function _getNextRevisionNumberForArticle($article_name)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::REVISION, 'next_revision', Query::DB_MAX, '', '+1');
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $row = $this->rawSelectOne($query);

            return ($row->get('next_revision')) ? $row->get('next_revision') : 1;
        }

        public function getHistoryByArticleName($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::REVISION, 'desc');

            $res = $this->rawSelect($query);

            return $res;
        }

        public function getUserIDsByArticleName($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addSelectionColumn(self::AUTHOR);

            $res = $this->rawSelect($query);
            $uids = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $a_id = $row[self::AUTHOR];
                    if ($a_id > 0)
                        $uids[$a_id] = $a_id;
                }
            }

            return $uids;
        }

        public function getRevisionContentFromArticleName($article_name, $from_revision, $to_revision = null)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($to_revision !== null) {
                $criteria = new Criteria();
                $criteria->or(self::REVISION, $to_revision);
                $criteria->where(self::REVISION, $from_revision);
                $query->where($criteria);
            } else {
                $query->where(self::REVISION, $from_revision);
            }

            $res = $this->rawSelect($query);

            if ($res) {
                $revisions = [];
                while ($row = $res->getNextRow()) {
                    $author = ($row->get(self::AUTHOR)) ? new User($row->get(self::AUTHOR)) : null;
                    $revisions[$row->get(self::REVISION)] = ['old_content' => $row->get(self::OLD_CONTENT), 'new_content' => $row->get(self::NEW_CONTENT), 'date' => $row->get(self::DATE), 'author' => $author];
                }

                return ($to_revision !== null) ? $revisions : $revisions[$from_revision];
            } else {
                return null;
            }
        }

        public function removeArticleRevisionsSince($article_name, $revision)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::REVISION, $revision, Criterion::GREATER_THAN);
            $res = $this->rawDelete($query);
        }

        /**
         * Returns all article revisions. Entries are sorted based on date, with
         * newest entry at beginning.
         *
         * @return ArticleRevision[]
         *   Array with all article revisions.
         */
        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::DATE, 'desc');

            return $this->select($query);
        }

        /**
         * Returns all article history entries based on passed-in user. Entries
         * are sorted based on date, with newest entry at beginning.
         *
         * @param User $user
         *   User for which to fetch article history.
         *
         * @return ArticleRevision[]
         *   Array with all article revisions produced by specified user.
         */
        public function getByUser($user)
        {
            $query = $this->getQuery();
            $query->where(self::AUTHOR, $user->getID());
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::DATE, 'desc');

            return $this->select($query);
        }

        /**
         * Retrieves all contributions based on author username, and based on
         * current user's article permissions.
         *
         * WARNING: Since article revision is tied to an article via its name,
         * in case of article renames it is not possible to reliably determine
         * the article related to revision. Therefore all revisions where
         * corresponding article cannot be found are not included in the
         * contribution list, since we can't properly check permissions for
         * them. In addition, we would not be able to point user to the article
         * at hand either.
         *
         * @param string $author_username
         *   Author username for which to fetch all contributions. If set to ""
         *   (empty string), fetches all contributions created via fixtures
         *   during installation. If set to null, fetches contributions for all
         *   authors.
         *
         * @return ArticleRevision[]
         *   All contributions by the requested author, or empty array if user
         *   has no permissions to such contributions or specified author is
         *   invalid/does not exist.
         */
        public function getByAuthorUsernameAndCurrentUserAccess($author_username)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            if ($author_username === "") {
                $query->where(self::AUTHOR, 0);
            } elseif ($author_username !== null) {
                $author = Users::getTable()->getByUsername($author_username);

                if ($author === null) {
                    return [];
                }

                $query->where(self::AUTHOR, $author->getID());
            }

            $query->addOrderBy(self::DATE, 'desc');

            $history = $this->select($query);

            $result = [];

            foreach ($history as $revision) {
                $article = $revision->getArticle();

                // Ignore revisions where article cannot be located anymore (due
                // ot renames or removal).
                if ($article !== null && $revision->getArticle()->hasAccess()) {
                    $result[] = $revision;
                }
            }

            return $result;
        }

        /**
         * Returns all article history entries coming from fixtures (author ID
         * 0).
         *
         * @return ArticleRevision[]
         *   Array with all article revisions produced by specified user.
         */
        public function getFromFixtures()
        {
            $query = $this->getQuery();
            $query->where(self::AUTHOR, 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::DATE, 'desc');

            return $this->select($query);
        }

        /**
         * Returns an array with user IDs of all users that have contributed to
         * the project or global wiki.
         *
         * @param Project $project
         *   Project for which to obtain list of contributors. If it is necessry
         *   to obtain contributors for global namespace, use null.
         *
         * @return array
         *   Array with user IDs of all contributors to the requested project or
         *   global wiki.
         */
        public function getContributorIDsByProject($project)
        {
            // All user IDs will get stored here.
            $result = [];

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addSelectionColumn(self::AUTHOR);
            $query->setIsDistinct();

            // If we need to look-up global wiki contributions, then we must
            // fetch list of all projects and exclude them from search.
            if ($project === null) {
                $projects_table = Projects::getTable();

                $project_query = $projects_table->getQuery();
                $project_query->where(Projects::SCOPE, framework\Context::getScope()->getID());
                $project_query->addSelectionColumn(Projects::KEY);

                $project_res = $projects_table->rawSelect($project_query);

                if ($project_res) {
                    while ($project_row = $project_res->getNextRow()) {
                        $query->where(self::ARTICLE_NAME, "{$project_row[Projects::KEY]}:%", Criterion::NOT_LIKE);
                    }
                }
            } else {
                $query->where(self::ARTICLE_NAME, "{$project->getKey()}:%", Criterion::LIKE);
            }

            $res = $this->rawSelect($query);

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $result[] = $row[self::AUTHOR];
                }
            }

            return $result;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::ARTICLE_NAME, 255);
            parent::addText(self::OLD_CONTENT, false);
            parent::addText(self::NEW_CONTENT, false);
            parent::addVarchar(self::REASON, 255);
            parent::addInteger(self::DATE, 10);
            parent::addInteger(self::REVISION, 10);
            parent::addForeignKeyColumn(self::AUTHOR, Users::getTable(), Users::ID);
        }
    }
