<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Exception;
    use b2db\Insertion;
    use b2db\Join;
    use b2db\Query;
    use b2db\Update;
    use pachno\core\entities\Article;
    use pachno\core\entities\Project;
    use pachno\core\entities\Scope;
    use pachno\core\framework;

    /**
     * @method static Articles getTable() Retrieves an instance of this table
     * @method Article selectById(integer $id) Retrieves an article
     * @method Article[] select(Query $id, $join = 'all') Retrieves articles
     * @method Article selectOne(Query $id, $join = 'all') Retrieves articles
     *
     * @Table(name="articles")
     * @Entity(class="\pachno\core\entities\Article")
     */
    class Articles extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'articles';

        const ID = 'articles.id';

        const NAME = 'articles.name';

        const CONTENT = 'articles.content';

        const IS_PUBLISHED = 'articles.is_published';

        const DATE = 'articles.date';

        const AUTHOR = 'articles.author';

        const SCOPE = 'articles.scope';

        public function _setupIndexes()
        {
            $this->_addIndex('name_scope', [self::NAME, self::SCOPE]);
        }

        public function getAllArticles()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.redirect_article_id', 0);
            $query->addOrderBy(self::NAME);

            return $this->select($query);
        }

        /**
         * @param Project|null $project
         * @param null $filter
         *
         * @return Article[]
         */
        public function getManualSidebarArticles($is_category, Project $project = null): array
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.is_category', $is_category);
            $query->where('articles.parent_article_id', 0);
            $query->where('articles.redirect_article_id', 0);

            if ($project instanceof Project) {
                $query->where('articles.project_id', $project->getID());
            } else {
                $query->where('articles.project_id', 0);
            }

            $query->where('articles.name', 'Main Page', Criterion::NOT_EQUALS);
            $query->addOrderBy(self::NAME, 'asc');

            $articles = $this->select($query);
            $exclude_ids = ArticleCategoryLinks::getTable()->getArticlesIdsWithCategory(array_keys($articles));
            $articles = array_diff_key($articles, array_flip($exclude_ids));

            return $articles;
        }

        /**
         * @param null $filter
         * @param Project|null $project
         * @param Article|null $current_article
         *
         * @return Article[]
         */
        public function findArticles($filter, Project $project = null, Article $current_article = null, $limit = null): array
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.redirect_article_id', 0);

            if ($project instanceof Project) {
                $query->where('articles.project_id', $project->getID());
            } else {
                $query->where('articles.project_id', 0);
            }

            $crit = new Criteria();
            $crit->where('articles.name', '%' . strtolower($filter) . '%', Criterion::LIKE);
            $crit->or('articles.name', '%' . strtolower($filter), Criterion::LIKE);
            $crit->or('articles.name', strtolower($filter) . '%', Criterion::LIKE);
            $query->where($crit);

            if ($current_article instanceof Article) {
                $query->where('articles.id', $current_article->getID(), Criterion::NOT_EQUALS);
            }

            $query->addOrderBy(self::NAME, 'asc');

            if ($limit !== null) {
                $query->setLimit($limit);
            }

            return $this->select($query);
        }

        public function getArticleParentCounts($article_ids)
        {
            if (!count($article_ids)) {
                return [];
            }

            $article_counts = [];
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $query->where('articles.parent_article_id', $article_ids, Criterion::IN);
            $query->where('articles.redirect_article_id', 0);
            $query->addSelectionColumn('articles.id', 'num_articles', Query::DB_COUNT);
            $query->addSelectionColumn('articles.parent_article_id');
            $query->addGroupBy('articles.parent_article_id');

            $res = $this->rawSelect($query);
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $article_counts[$row['parent_article_id']] = $row['num_articles'];
                }
            }

            return $article_counts;
        }

        /**
         * @param Project|null $project
         *
         * @return Article[]
         */
        public function getArticles(Project $project = null): array
        {
            $query = $this->getQuery();
            if ($project instanceof Project && $project->getScope() instanceof Scope) {
                $query->where(self::SCOPE, $project->getScope()->getID());
                $query->where('articles.project_id', $project->getID());
            } else {
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                if ($project instanceof Project) {
                    $query->where('articles.project_id', $project->getID());
                } else {
                    $query->where('articles.project_id', 0);
                }
            }
            $query->where('articles.redirect_article_id', 0);
            $query->addOrderBy(self::DATE, 'desc');
            $articles = [];

            foreach ($this->select($query) as $article) {
                if ($article->hasAccess()) {
                    $articles[$article->getID()] = $article;
                }
            }

            return $articles;
        }

        /**
         * @param Project|null $project
         * @param Scope|null $scope
         * @param bool $check_access
         *
         * @return Article[]
         * @throws Exception
         */
        public function getLegacyArticles(Project $project = null, Scope $scope = null, $check_access = true): array
        {
            $query = $this->getQuery();
            if ($project instanceof Project && $project->getScope() instanceof Scope) {
                $query->where(self::SCOPE, $project->getScope()->getID());
            } elseif ($scope instanceof Scope) {
                $query->where(self::SCOPE, $scope->getID());
            }
            $query->where('articles.article_type', Article::TYPE_WIKI);

            if ($project instanceof Project) {
                $project_key_normalized = str_replace(['-'], [''], $project->getKey());
                $project_key_normalized = ucfirst($project_key_normalized);

                $criteria = new Criteria();
                $criteria->where(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criterion::LIKE);
                $criteria->or(self::NAME, "Category:" . $project_key_normalized . ":%", Criterion::LIKE);
                $criteria->or(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::LIKE);
                $criteria->or(self::NAME, $project_key_normalized . ":%", Criterion::LIKE);
                $query->where($criteria);
            } else {
                $query->where('articles.project_id', 0);
            }

            $query->addOrderBy(self::DATE, 'desc');

            $articles = $this->select($query);
            if ($check_access) {
                foreach ($articles as $id => $article) {
                    if (!$article->hasAccess())
                        unset($articles[$id]);
                }
            }

            return $articles;
        }

        public function doesArticleExist($name): bool
        {
            return $this->getArticleByName($name) instanceof Article;
        }

        /**
         * @param $parent_id
         * @param bool $is_category
         *
         * @return Article[]
         */
        public function getArticlesByParentId($parent_id, $is_category = false)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.is_category', $is_category);
            $query->where('articles.parent_article_id', $parent_id);
            $query->where('articles.redirect_article_id', 0);

            return $this->select($query);
        }

        /**
         * @param $parent_id
         * @param int $new_parent_id
         * @return Article[]
         * @throws Exception
         */
        public function updateParentArticleId($parent_id, $new_parent_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.parent_article_id', $parent_id);

            $update = new Update();
            $update->update('articles.parent_article_id', $new_parent_id);

            $this->rawUpdate($update, $query);
        }

        /**
         * @param $parent_id
         * @param bool $is_category
         *
         * @return int
         */
        public function countArticlesByParentId($parent_id, $is_category = false)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.is_category', $is_category);
            $query->where('articles.parent_article_id', $parent_id);
            $query->where('articles.redirect_article_id', 0);

            return $this->count($query);
        }

        /**
         * @param $project_id
         *
         * @return Article[]
         */
        public function getByProjectId($project_id)
        {
            $query = $this->getQuery();
            $query->where('articles.project_id', $project_id);

            return $this->select($query);
        }

        /**
         * @param $scope_id
         *
         * @return Article[]
         */
        public function getByScopeId($scope_id)
        {
            $query = $this->getQuery();
            $query->where('articles.scope', $scope_id);
            $query->where('articles.project_id', 0);

            return $this->select($query);
        }

        /**
         * @param $name
         * @param Project|integer|null $project
         *
         * @param bool $is_manual_name
         *
         * @param int $parent_id
         *
         * @param null $scope_id
         *
         * @return Article
         */
        public function getArticleByName($name, $project = null, $is_manual_name = false, $parent_id = null, $scope_id = null): ?Article
        {
            $project_id = ($project instanceof Project) ? $project->getId() : $project;

            $query = $this->getQuery();
            if ($is_manual_name) {
                $criteria = new Criteria();
                $criteria->where('articles.manual_name', $name, Criterion::LIKE);

                if ($project instanceof Project) {
                    $criteria->or('articles.manual_name', ucfirst($project->getKey()) . ":" . $name, Criterion::LIKE);
                }

                $query->where($criteria);

                if ($parent_id !== null) {
                    $query->where('articles.parent_article_id', $parent_id);
                }
            } else {
                $query->where(self::NAME, $name, Criterion::LIKE);
            }

            if ($scope_id !== null) {
                $query->where('articles.scope', $scope_id);
            }

            if ($project_id !== null) {
                $query->where('articles.project_id', $project_id);
            } else {
                $query->where('articles.project_id', 0);
            }
            $query->where('articles.redirect_article_id', 0);

            return $this->selectOne($query, 'none');
        }

        /**
         * @param null $project
         * @return Article
         *
         * @throws \Exception
         */
        public function getOrCreateMainPage($project = null): Article
        {
            $article = $this->getArticleByName('Main Page', $project);
            if (!$article instanceof Article) {
                $article = framework\Context::getModule('publish')->createMainPageArticle($project);
            }

            return $article;
        }

        public function deleteArticleByName($name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->setLimit(1);
            $row = $this->rawDelete($query);

            return $row;
        }

        public function doesNameConflictExist($name, $id, $project_id = null, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;

            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::ID, $id, Criterion::NOT_EQUALS);
            if ($project_id) {
                $query->where('articles.project_id', $project_id);
            } else {
                $query->where('articles.project_id', 0);
            }
            $query->where(self::SCOPE, $scope);

            return (bool)$this->count($query);
        }

        public function findArticlesContaining($content, $project = null, $limit = 5, $offset = 0)
        {
            $query = $this->getQuery();
            if ($project instanceof Project) {
                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where(self::NAME, "category:" . $project->getKey() . "%", Criterion::LIKE);
                $criteria->where('articles.redirect_article_id', 0);
                $query->where($criteria);

                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where(self::NAME, $project->getKey() . "%", Criterion::LIKE);
                $criteria->where('articles.redirect_article_id', 0);
                $query->or($criteria);

                $criteria = new Criteria();
                $criteria->where(self::CONTENT, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::NAME, $project->getKey() . "%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where('articles.redirect_article_id', 0);
                $query->or($criteria);
            } else {
                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where('articles.redirect_article_id', 0);
                $query->where($criteria);

                $criteria = new Criteria();
                $criteria->where(self::CONTENT, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where('articles.redirect_article_id', 0);
                $query->or($criteria);
            }

            $resultcount = $this->count($query);

            if ($resultcount) {
                $query->setLimit($limit);

                if ($offset) {
                    $query->setOffset($offset);
                }

                return [$resultcount, $this->select($query)];
            } else {
                return [$resultcount, []];
            }
        }

//        public function save($name, $content, $published, $author, $id = null, $scope = null)
//        {
//            $scope = ($scope !== null) ? $scope : framework\Context::getScope()->getID();
//            if ($id == null) {
//                $insertion = new Insertion();
//                $insertion->add(self::NAME, $name);
//                $insertion->add(self::CONTENT, $content);
//                $insertion->add(self::IS_PUBLISHED, (bool)$published);
//                $insertion->add(self::AUTHOR, $author);
//                $insertion->add(self::DATE, NOW);
//                $insertion->add(self::SCOPE, $scope);
//                $res = $this->rawInsert($insertion);
//
//                return $res->getInsertID();
//            } else {
//                $update = new Update();
//                $update->add(self::NAME, $name);
//                $update->add(self::CONTENT, $content);
//                $update->add(self::IS_PUBLISHED, (bool)$published);
//                $update->add(self::AUTHOR, $author);
//                $update->add(self::DATE, NOW);
//                $res = $this->rawUpdateById($update, $id);
//
//                return $res;
//            }
//        }

        /**
         * @param $article_ids
         * @return Article[]
         */
        public function getByArticleIds($article_ids)
        {
            if (!$article_ids)
                return [];

            $query = $this->getQuery();
            $query->where('articles.id', $article_ids, Criterion::IN);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getDeadEndArticles(Project $project = null)
        {
            $names = ArticleLinks::getTable()->getUniqueArticleNames();

            $query = $this->getQuery();
            if ($project instanceof Project) {
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::LIKE);
            } else {
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }
            $query->where(self::NAME, $names, Criterion::NOT_IN);
            $query->where('articles.redirect_article_id', 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getAllByLinksToArticleName($article_name)
        {
            $names_res = ArticleLinks::getTable()->getLinkingArticles($article_name);
            if (empty($names_res))
                return [];

            $names = [];
            while ($row = $names_res->getNextRow()) {
                $names[] = $row[ArticleLinks::ARTICLE_NAME];
            }

            $query = $this->getQuery();
            $query->where(self::NAME, $names, Criterion::IN);
            $query->where('articles.redirect_article_id', 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getUnlinkedArticles(Project $project = null)
        {
            $names = ArticleLinks::getTable()->getUniqueLinkedArticleNames();

            $query = $this->getQuery();
            if ($project instanceof Project) {
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::LIKE);
            } else {
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }
            $query->where(self::NAME, $names, Criterion::NOT_IN);
            $query->where('articles.redirect_article_id', 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getUncategorizedArticles(Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof Project) {
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::LIKE);
            } else {
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }
            $query->where(self::NAME, "Category:%", Criterion::NOT_LIKE);
            $query->where('articles.redirect_article_id', 0);
            $query->where(self::CONTENT, '%[Category:%', Criterion::NOT_LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getUncategorizedCategories(Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof Project) {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criterion::LIKE);
            } else {
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }
            $query->where('articles.redirect_article_id', 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getAllArticlesSpecial(Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof Project) {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::LIKE);
            } else {
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }
            $query->where('articles.redirect_article_id', 0);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getAllCategories(Project $project = null)
        {
            return $this->_getAllInNamespace('Category', $project);
        }

        protected function _getAllInNamespace($namespace, Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof Project) {
                $query->where(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . ":%", Criterion::LIKE);
            } else {
                $query->where(self::NAME, "{$namespace}:%", Criterion::LIKE);
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getAllTemplates(Project $project = null)
        {
            return $this->_getAllInNamespace('Template', $project);
        }

        public function fixArticleTypes()
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add('articles.article_type', Article::TYPE_WIKI);

            $query->where('articles.article_type', 0);

            $this->rawUpdate($update, $query);
        }

        public function removeEmptyRedirects()
        {
            $query = $this->getQuery();
            $query->where('articles.redirect_slug', '', Criterion::NOT_EQUALS);
            $query->where('articles.redirect_slug', null, Criterion::IS_NOT_NULL);
            $query->where('articles.redirect_article_id', 0);

            $this->rawDelete($query);
        }

    }
