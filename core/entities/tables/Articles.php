<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Exception;
    use b2db\Insertion;
    use b2db\Query;
    use b2db\Update;
    use pachno\core\entities\Article;
    use pachno\core\entities\Project;
    use pachno\core\entities\Scope;
    use pachno\core\framework;

    /**
     * @static @method Articles getTable() Retrieves an instance of this table
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
            $query->addOrderBy(self::NAME);

            return $this->select($query);
        }

        /**
         * @param Project|null $project
         * @param null $filter
         *
         * @return Article[]
         */
        public function getManualSidebarArticles(Project $project = null, $filter = null): array
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.is_category', false);
            $query->where('articles.parent_article_id', 0);

            if ($project instanceof Project) {
                $query->where('articles.project_id', $project->getID());
            } else {
                $query->where('articles.project_id', 0);
            }

            if ($filter !== null) {
                $query->where('articles.name', '%' . strtolower($filter) . '%', Criterion::LIKE);
            }

            $query->addOrderBy(self::NAME, 'asc');

            return $this->select($query);
        }

        public function getManualSidebarCategories(Project $project = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.article_type', Article::TYPE_MANUAL);
            $query->where('articles.parent_article_id', 0);
            if ($project instanceof Project) {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::LIKE);
            } else {
                foreach (Projects::getTable()->getAllIncludingDeleted() as $project) {
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", Criterion::NOT_LIKE);
                }
            }

            $query->addOrderBy(self::NAME, 'asc');

            $articles = $this->select($query);
            foreach ($articles as $i => $article) {
                if (!$article->hasAccess())
                    unset($articles[$i]);
            }

            return $articles;
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
                $criteria->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criterion::LIKE);
                $criteria->or(self::NAME, "Category:" . $project_key_normalized . "%", Criterion::LIKE);
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
         * @param $name
         * @param Project|integer|null $project
         *
         * @return Article
         */
        public function getArticleByName($name, $project = null): ?Article
        {
            if (mb_substr($name, 0, 9) == 'Category:') {
                $name = mb_substr($name, 9);
                $is_category = true;
            } else {
                $is_category = false;
            }

            $colon_pos = mb_strpos($name, ':');
            if ($colon_pos !== 0) {
                $project_key = mb_strtolower(mb_substr($name, 0, $colon_pos));
                $project = Project::getByKey($project_key);
            }

            if ($project instanceof Project) {
                $article_name = mb_substr($name, $colon_pos + 1);
            } else {
                if (framework\Context::isProjectContext()) {
                    $project = framework\Context::getCurrentProject();
                }
                $article_name = $name;
            }
            $project_id = ($project instanceof Project) ? $project->getId() : $project;

            $query = $this->getQuery();
            $query->where(self::NAME, $article_name, Criterion::LIKE);
            $query->where('articles.is_category', $is_category);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($project_id !== null) {
                $query->where('articles.project_id', $project_id);
            } else {
                $query->where('articles.project_id', 0);
            }
            $article = $this->selectOne($query, 'none');

            if (!$article instanceof Article) {
                $query = $this->getQuery();
                $query->where(self::NAME, $article_name, Criterion::LIKE);
                $query->where('articles.is_category', $is_category);
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->where('articles.project_id', 0);
                $article = $this->selectOne($query);
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

        public function doesNameConflictExist($name, $id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;

            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::ID, $id, Criterion::NOT_EQUALS);
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
                $query->where($criteria);

                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where(self::NAME, $project->getKey() . "%", Criterion::LIKE);
                $query->or($criteria);

                $criteria = new Criteria();
                $criteria->where(self::CONTENT, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::NAME, $project->getKey() . "%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->or($criteria);
            } else {
                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->where($criteria);

                $criteria = new Criteria();
                $criteria->where(self::CONTENT, "%{$content}%", Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
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

        public function save($name, $content, $published, $author, $id = null, $scope = null)
        {
            $scope = ($scope !== null) ? $scope : framework\Context::getScope()->getID();
            if ($id == null) {
                $insertion = new Insertion();
                $insertion->add(self::NAME, $name);
                $insertion->add(self::CONTENT, $content);
                $insertion->add(self::IS_PUBLISHED, (bool)$published);
                $insertion->add(self::AUTHOR, $author);
                $insertion->add(self::DATE, NOW);
                $insertion->add(self::SCOPE, $scope);
                $res = $this->rawInsert($insertion);

                return $res->getInsertID();
            } else {
                $update = new Update();
                $update->add(self::NAME, $name);
                $update->add(self::CONTENT, $content);
                $update->add(self::IS_PUBLISHED, (bool)$published);
                $update->add(self::AUTHOR, $author);
                $update->add(self::DATE, NOW);
                $res = $this->rawUpdateById($update, $id);

                return $res;
            }
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
            $query->where(self::CONTENT, '#REDIRECT%', Criterion::NOT_LIKE);
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
            $query->where(self::CONTENT, '#REDIRECT%', Criterion::NOT_LIKE);
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
            $query->where(self::CONTENT, '#REDIRECT%', Criterion::NOT_LIKE);
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
            $query->where(self::CONTENT, '#REDIRECT%', Criterion::NOT_LIKE);
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

    }
