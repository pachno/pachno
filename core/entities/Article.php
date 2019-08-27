<?php

    namespace pachno\core\entities;

    use pachno\core\entities\Comment;
    use pachno\core\entities\Notification;
    use \pachno\core\framework,
        \pachno\core\entities\File,
        \pachno\core\entities\Project,
        \pachno\core\entities\User,
        pachno\core\entities\tables\UserArticles,
        pachno\core\entities\tables\Articles,
        pachno\core\entities\tables\ArticleCategoryLinks,
        pachno\core\entities\tables\ArticleFiles,
        pachno\core\entities\tables\ArticleHistory,
        pachno\core\entities\tables\ArticleLinks;
    use pachno\core\entities\tables;

    /**
     * @Table(name="\pachno\core\entities\tables\Articles")
     */
    class Article extends \pachno\core\entities\common\IdentifiableScoped implements \pachno\core\helpers\Attachable
    {

        const TYPE_WIKI = 1;
        const TYPE_MANUAL = 2;

        /**
         * The article author
         *
         * @var \pachno\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_author = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="string", length=200)
         */
        protected $_manual_name;

        /**
         * When the article was posted
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_date = null;

        /**
         * What type of article this is
         *
         * @var integer
         * @Column(type="integer", length=10, default=2)
         */
        protected $_article_type = self::TYPE_MANUAL;

        /**
         * The old article content, used for history when saving
         *
         * @var string
         */
        protected $_old_content = null;

        /**
         * The article content
         *
         * @var string
         * @Column(type="text")
         */
        protected $_content = null;

        /**
         * The article content syntax
         *
         * @var integer
         * @Column(type="integer", length=3, default=1)
         */
        protected $_content_syntax = framework\Settings::SYNTAX_MW;

        /**
         * Whether the article is published or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_is_published = false;

        /**
         * The parent article, if this article has one
         *
         * @var \pachno\core\entities\Article
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Article")
         */
        protected $_parent_article_id = false;

        /**
         * Child article, if this article has any
         *
         * @var array|\pachno\core\entities\Article
         * @Relates(class="\pachno\core\entities\Article", collection=true, foreign_column="parent_article_id", orderby="name")
         */
        protected $_child_articles = null;

        /**
         * Array of users that are subscribed to this issue
         *
         * @var array
         * @Relates(class="\pachno\core\entities\User", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\UserArticles")
         */
        protected $_subscribers = null;

        /**
         * Related project
         *
         * @var \pachno\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * Whether or not this page is a category page
         *
         * @Column(type="boolean", default=false)
         * @var boolean
         */
        protected $_is_category = false;

        /**
         * A list of articles that links to this article
         *
         * @var array
         */
        protected $_linking_articles = null;

        /**
         * A list of categories this article is in
         *
         * @var array
         */
        protected $_categories = null;

        /**
         * Array of files attached to this article
         *
         * @var array
         */
        protected $_files = null;

        /**
         * A list of subcategories for this category
         *
         * @var array
         */
        protected $_subcategories = null;

        /**
         * A list of page in this category
         *
         * @var array
         */
        protected $_category_articles = null;

        protected $_history = null;
        protected $_category_name = null;
        protected $_namespaces = null;
        protected $_redirect_article = null;

        protected $_new_subscribers = array();

        protected $_parser = null;

        /**
         * Article constructor
         *
         * @param \b2db\Row $row
         */
        public function _construct(\b2db\Row $row, $foreign_key = null)
        {
            $this->_content = str_replace("\r\n", "\n", $this->_content);
            $this->_old_content = $this->_content;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if (!framework\Context::isCLI()) {
                $this->_date = NOW;
                $this->_author = framework\Context::getUser();
            }
        }

        protected function _postDelete()
        {
            \pachno\core\entities\tables\ArticleLinks::getTable()->deleteLinksByArticle($this->getName());
            ArticleCategoryLinks::getTable()->deleteCategoriesByArticle($this->getName());
            \pachno\core\entities\tables\ArticleHistory::getTable()->deleteHistoryByArticle($this->getName());
            \pachno\core\entities\tables\ArticleFiles::getTable()->deleteFilesByArticleID($this->getID());
        }

        public static function findArticlesByContentAndProject($content, $project, $limit = 5, $offset = 0)
        {
            list ($resultcount, $articles) = \pachno\core\entities\tables\Articles::getTable()->findArticlesContaining($content, $project, $limit, $offset);

            if ($resultcount)
            {
                foreach ($articles as $key => $article)
                {
                    if (!$article->hasAccess())
                    {
                        unset($articles[$key]);
                        $resultcount--;
                    }
                }
            }

            return array($resultcount, $articles);
        }

        public static function getByName($article_name)
        {
            return \pachno\core\entities\tables\Articles::getTable()->getArticleByName($article_name);
        }

        public static function doesArticleExist($article_name)
        {
            return \pachno\core\entities\tables\Articles::getTable()->doesArticleExist($article_name);
        }

        public static function deleteByName($article_name)
        {
            \pachno\core\entities\tables\Articles::getTable()->deleteArticleByName($article_name);
            \pachno\core\entities\tables\ArticleLinks::getTable()->deleteLinksByArticle($article_name);
        }

        public static function createNew($name, $content, $scope = null, $options = array(), $project = null)
        {
            $user_id = (framework\Context::getUser() instanceof User) ? framework\Context::getUser()->getID() : 0;

            $article = new Article();
            $article->setName($name);
            $article->setContent($content);
            if ($project !== null) {
                $article->setProject($project->getID());
            }

            if (!isset($options['noauthor']))
                $article->setAuthor($user_id);
            else
                $article->setAuthor(0);

            if ($scope !== null)
                $article->setScope($scope);

            $article->doSave($options);

            return $article->getID();
        }

        public function __toString()
        {
            return $this->_content;
        }

        public function hasContent()
        {
            return (trim($this->_content) != '') ? true : false;
        }

        public function getContent()
        {
            return $this->_content;
        }

        public function getParsedContent($options = array())
        {
            return $this->_parseContent($options);
        }

        public function setContentSyntax($syntax)
        {
            if (!is_numeric($syntax))
                $syntax = framework\Settings::getSyntaxValue($syntax);

            $this->_content_syntax = $syntax;
        }

        public function getContentSyntax()
        {
            return $this->_content_syntax;
        }

        public function setContent($content)
        {
            $this->_content = str_replace("\r\n", "\n", $content);
            if ($this->_content_syntax == framework\Settings::SYNTAX_MW)
            {
                $parser = new \pachno\core\helpers\TextParser($content);
                $parser->doParse();
                $this->_populateCategories($parser->getCategories());
            }
        }

        public function getTitle()
        {
            return $this->getName();
        }

        public function getLastUpdatedDate()
        {
            return $this->getPostedDate();
        }

        protected function _populateLinkingArticles()
        {
            if ($this->_linking_articles === null)
            {
                $this->_linking_articles = \pachno\core\entities\tables\Articles::getTable()->getAllByLinksToArticleName($this->_name);
                foreach ($this->_linking_articles as $k => $article)
                    if (!$article->hasAccess())
                        unset($this->_linking_articles[$k]);
            }
        }

        public function getLinkingArticles()
        {
            $this->_populateLinkingArticles();
            return $this->_linking_articles;
        }

        protected function _populateSubCategories()
        {
            if ($this->_subcategories === null)
            {
                $this->_subcategories = array();
                return;
                if ($res = ArticleCategoryLinks::getTable()->getSubCategories($this->getCategoryName()))
                {
                    while ($row = $res->getNextRow())
                    {
                        try
                        {
                            $article = \pachno\core\entities\tables\Articles::getTable()->getArticleByName($row->get(ArticleCategoryLinks::ARTICLE_NAME));
                            if ($article instanceof Article)
                            {
                                $this->_subcategories[$row->get(ArticleCategoryLinks::ARTICLE_NAME)] = $article;
                            }
                        }
                        catch (\Exception $e)
                        {
                            throw $e;
                        }
                    }
                }
            }
        }

        public function getSubCategories()
        {
            $this->_populateSubCategories();
            return $this->_subcategories;
        }

        protected function _populateCategoryArticles()
        {
            if ($this->_category_articles === null)
            {
                $this->_category_articles = array();
                return;
                if ($res = ArticleCategoryLinks::getTable()->getCategoryArticles($this->getCategoryName()))
                {
                    while ($row = $res->getNextRow())
                    {
                        try
                        {
                            $article = \pachno\core\entities\tables\Articles::getTable()->getArticleByName($row->get(ArticleCategoryLinks::ARTICLE_NAME));
                            if ($article instanceof Article)
                            {
                                $this->_category_articles[$row->get(ArticleCategoryLinks::ARTICLE_NAME)] = $article;
                            }
                        }
                        catch (\Exception $e)
                        {
                            throw $e;
                        }
                    }
                }
            }
        }

        /**
         * @return Article[]
         */
        public function getCategoryArticles(): array
        {
            $this->_populateCategoryArticles();
            return $this->_category_articles;
        }

        protected function _populateCategories($categories = null)
        {
            if ($this->_categories === null || $categories !== null)
            {
                $this->_categories = array();
                if ($categories === null)
                {
                    if ($res = ArticleCategoryLinks::getTable()->getArticleCategories($this->getName()))
                    {
                        while ($row = $res->getNextRow())
                        {
                            $this->_categories[] = $row->get(ArticleCategoryLinks::CATEGORY_NAME);
                        }
                    }
                }
                else
                {
                    foreach ($categories as $category => $occurrences)
                    {
                        $this->_categories[] = $category;
                    }
                }
            }
        }

        public function getCategories()
        {
            $this->_populateCategories();
            return $this->_categories;
        }

        protected function _retrieveLinksAndCategoriesFromContent($options = array())
        {
            $parser = new \pachno\core\helpers\TextParser($this->_content);
            $options['no_code_highlighting'] = true;
            $parser->doParse($options);
            return array($parser->getInternalLinks(), $parser->getCategories());
        }

        public function isCategory()
        {
            return $this->_is_category;
        }

        public function setIsCategory($is_category = true)
        {
            $this->_is_category = $is_category;
        }

        public function getSpacedName()
        {
            return preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $this->getName());
        }

        public function getCategoryName()
        {
            if ($this->_category_name === null)
            {
                $this->_category_name = mb_substr($this->_name, mb_strpos($this->_name, ':') + 1);
            }
            return $this->_category_name;
        }

        protected function _populateHistory()
        {
            if ($this->_history === null)
            {
                $this->_history = array();
                $history = \pachno\core\entities\tables\ArticleHistory::getTable()->getHistoryByArticleName($this->getName());

                if ($history)
                {
                    while ($row = $history->getNextRow())
                    {
                        $author = ($row->get(ArticleHistory::AUTHOR)) ? new \pachno\core\entities\User($row->get(ArticleHistory::AUTHOR)) : null;
                        $this->_history[$row->get(ArticleHistory::REVISION)] = array('old_content' => $row->get(ArticleHistory::OLD_CONTENT), 'new_content' => $row->get(ArticleHistory::NEW_CONTENT), 'change_reason' => $row->get(ArticleHistory::REASON), 'updated' => $row->get(ArticleHistory::DATE), 'author' => $author);
                    }
                }
            }
        }

        public function getHistory()
        {
            $this->_populateHistory();
            return $this->_history;
        }

        public function isRedirect()
        {
            if (mb_substr($this->getContent(), 0, 10) == "#REDIRECT ")
            {
                $content = explode("\n", $this->getContent());
                preg_match('/(\[\[([^\]]*?)\]\])$/im', mb_substr(array_shift($content), 10), $matches);
                if (count($matches) == 3)
                {
                    $this->_redirect_article = $matches[2];
                    return true;
                }
            }

            return false;
        }

        public function getRedirectArticle()
        {
            if (!$this->isRedirect())
                return null;

            if (!$this->_redirect_article instanceof Article)
            {
                $article = \pachno\core\entities\tables\Articles::getTable()->getArticleByName($this->_redirect_article, $this->getProject());
                if ($article instanceof Article)
                    $this->_redirect_article = $article;
            }

            return $this->_redirect_article;
        }

        public function getRedirectArticleName()
        {
            return ($this->_redirect_article instanceof Article) ? $this->_redirect_article->getName() : $this->_redirect_article;
        }

        public function doSave($options = array(), $reason = null)
        {
            if (\pachno\core\entities\tables\Articles::getTable()->doesNameConflictExist($this->_name, $this->_id, framework\Context::getScope()->getID()))
            {
                if (!array_key_exists('overwrite', $options) || !$options['overwrite'])
                {
                    throw new \Exception(framework\Context::getI18n()->__('Another article with this name already exists'));
                }
            }
            $user_id = (framework\Context::getUser() instanceof User) ? framework\Context::getUser()->getID() : 0;

            if (!isset($options['revert']) || !$options['revert'])
            {
                $revision = \pachno\core\entities\tables\ArticleHistory::getTable()->addArticleHistory($this->_name, $this->_old_content, $this->_content, $user_id, $reason);
            }
            else
            {
                $revision = null;
            }

            \pachno\core\entities\tables\ArticleLinks::getTable()->deleteLinksByArticle($this->_name);
            ArticleCategoryLinks::getTable()->deleteCategoriesByArticle($this->_name);

            if ($this->getArticleType() == self::TYPE_MANUAL && isset($options['article_prev_name']) && $this->_name != $options['article_prev_name'])
            {
                $manual_articles = Articles::getTable()->getManualSidebarArticles(framework\Context::getCurrentProject(), $options['article_prev_name']);

                foreach ($manual_articles as $manual_article)
                {
                    $manual_article->setName(str_replace($options['article_prev_name'], $this->_name, $manual_article->getName()));
                    $manual_article->doSave();
                }
            }

            $this->save();

            $this->_old_content = $this->_content;

            if (mb_substr($this->getContent(), 0, 10) == "#REDIRECT ")
            {
                $content = explode("\n", $this->getContent());
                preg_match('/(\[\[([^\]]*?)\]\])$/im', mb_substr(array_shift($content), 10), $matches);
                if (count($matches) == 3)
                {
                    return;
                }
            }
            list ($links, $categories) = $this->_retrieveLinksAndCategoriesFromContent($options);

            foreach ($links as $link => $occurrences)
            {
                \pachno\core\entities\tables\ArticleLinks::getTable()->addArticleLink($this->_name, $link);
            }

            foreach ($categories as $category => $occurrences)
            {
                ArticleCategoryLinks::getTable()->addArticleCategory($this->_name, $category, $this->isCategory());
            }

            $this->_history = null;

            \pachno\core\framework\Event::createNew('core', 'pachno\core\entities\Article::doSave', $this, compact('reason', 'revision', 'user_id'))->trigger();

            return true;
        }

        public function getPostedDate()
        {
            return $this->_date;
        }

        /**
         * Returns the author
         *
         * @return \pachno\core\entities\User
         */
        public function getAuthor()
        {
            return $this->_b2dbLazyLoad('_author');
        }

        /**
         * Return the author id
         *
         * @return integer
         */
        public function getAuthorID()
        {
            $author = $this->getAuthor();
            return ($author instanceof \pachno\core\entities\common\Identifiable) ? $author->getID() : null;
        }

        public function setAuthor($author)
        {
            if (is_object($author))
            {
                $author = $author->getID();
            }
            $this->_author = $author;
        }

        /**
         * Compare to revisions of this article, and return the diff output, as well as revision information
         *
         * @param integer $from_revision
         * @param integer $to_revision
         *
         * @return array
         */
        public function compareRevisions($from_revision, $to_revision)
        {
            $content = \pachno\core\entities\tables\ArticleHistory::getTable()->getRevisionContentFromArticleName($this->getName(), $from_revision, $to_revision);
            $old_content = htmlspecialchars($content[$from_revision]['new_content']);
            $new_content = htmlspecialchars($content[$to_revision]['new_content']);

            $diff = new \pachno\core\helpers\TextDiff();
            $result = $diff->stringDiff($old_content, $new_content);
            $changes = $diff->sequentialChanges($result);
            return array($content, $diff->renderDiff($result));
        }

        public function restoreRevision($revision)
        {
            \pachno\core\entities\tables\ArticleHistory::getTable()->removeArticleRevisionsSince($this->getName(), $revision);
            $content = \pachno\core\entities\tables\ArticleHistory::getTable()->getRevisionContentFromArticleName($this->getName(), $revision);
            $this->setContent($content['new_content']);
            $this->doSave(array('revert' => true));
        }

        public function setRevision($revision = null)
        {
            $content = \pachno\core\entities\tables\ArticleHistory::getTable()->getRevisionContentFromArticleName($this->getName(), $revision);
            if (array_key_exists('new_content', $content))
            {
                $this->setContent($content['new_content']);
                $this->_date = $content['date'];
                $this->_author = $content['author'];
            }
            else
            {
                throw new \Exception('No such revision');
            }
        }

        public function getNamespaces()
        {
            if ($this->_namespaces === null)
            {
                $this->_namespaces = array();
                $namespaces = explode(':', $this->getName());
                if (count($namespaces))
                {
                    array_pop($namespaces);
                    $this->_namespaces = $namespaces;
                }
            }
            return $this->_namespaces;
        }

        public function getCombinedNamespaces()
        {
            $namespaces = $this->getNamespaces();
            if (count($namespaces) > 1)
            {
                $composite_ns = '';
                $return_array = array();
                foreach ($namespaces as $namespace)
                {
                    $composite_ns .= ($composite_ns != '') ? ":{$namespace}" : $namespace;
                    $return_array[] = $composite_ns;
                }
                return $return_array;
            }
            else
            {
                return $namespaces;
            }
        }

        /**
         * Populate the files array
         */
        protected function _populateFiles()
        {
            if ($this->_files === null)
            {
                $this->_files = File::getByArticleID($this->getID());
            }
        }

        /**
         * Return an array with all files attached to this issue
         *
         * @return array
         */
        public function getFiles()
        {
            $this->_populateFiles();
            return $this->_files;
        }

        /**
         * Return an array with all files attached to this issue
         *
         * @return array
         */
        public function countFiles()
        {
            return count($this->getFiles());
        }

        /**
         * Return a file by the filename if it is attached to this issue
         *
         * @param string $filename The original filename to match against
         *
         * @return \pachno\core\entities\File
         */
        public function getFileByFilename($filename)
        {
            foreach ($this->getFiles() as $file_id => $file)
            {
                if (mb_strtolower($filename) == mb_strtolower($file->getRealFilename()) || mb_strtolower($filename) == mb_strtolower($file->getOriginalFilename()))
                {
                    return $file;
                }
            }
            return null;
        }

        /**
         * Attach a file to the issue
         *
         * @param \pachno\core\entities\File $file The file to attach
         */
        public function attachFile(File $file, $file_comment = '', $file_description = '')
        {
            \pachno\core\entities\tables\ArticleFiles::getTable()->addByArticleIDandFileID($this->getID(), $file->getID());
            if ($this->_files !== null)
            {
                $this->_files[$file->getID()] = $file;
            }
        }

        /**
         * Remove a file
         *
         * @param \pachno\core\entities\File $file The file to be removed
         *
         * @return boolean
         */
        public function detachFile(File $file)
        {
            \pachno\core\entities\tables\ArticleFiles::getTable()->removeByArticleIDandFileID($this->getID(), $file->getID());
            if (is_array($this->_files) && array_key_exists($file->getID(), $this->_files))
            {
                unset($this->_files[$file->getID()]);
            }
            $file->delete();
        }

        public function canDelete()
        {
            $namespaces = $this->getNamespaces();

            if (count($namespaces) > 0)
            {
                $key = $namespaces[0];
                $project = Project::getByKey($key);
                if ($project instanceof Project)
                {
                    if ($project->isArchived())
                        return false;
                }
            }

            return framework\Context::getModule('publish')->canUserDeleteArticle($this->getName());
        }

        public function canEdit()
        {
            $namespaces = $this->getNamespaces();

            if (count($namespaces) > 0)
            {
                $key = $namespaces[0];
                $project = Project::getByKey($key);
                if ($project instanceof Project)
                {
                    if ($project->isArchived())
                        return false;
                }
            }

            return framework\Context::getModule('publish')->canUserEditArticle($this->getName());
        }

        public function canRead()
        {
            return framework\Context::getModule('publish')->canUserReadArticle($this->getName());
        }

        /**
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project_id');
        }

        public function setProject($project_id)
        {
            $this->_project_id = $project_id;
        }

        public function getProjectFromName()
        {
            $namespaces = $this->getNamespaces();

            if (count($namespaces) > 0)
            {
                $key = $namespaces[0];
                $project = Project::getByKey(strtolower($key));
                return $project;
            }

            return null;
        }

        public function hasAccess()
        {
            $project = $this->getProject();

            if ($project instanceof Project && $project->isArchived())
                return false;

            return $this->canRead();
        }

        /**
         * Return the article name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the article name
         *
         * @param string $name
         */
        public function setName($name)
        {
//            $this->_name = preg_replace('/[^\p{L}\p{N} :]/u', '', $name);
            $this->_name = $name;
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getManualName()
        {
            return $this->_manual_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setManualName($name)
        {
            $this->_manual_name = $name;
        }

        public function setParentArticle($parent_article)
        {
            $this->_parent_article_id = $parent_article;
        }

        /**
         * Return the parent article (if any)
         *
         * @return \pachno\core\entities\Article
         */
        public function getParentArticle()
        {
            return $this->_b2dbLazyLoad('_parent_article_id');
        }

        public function getParentArticleName()
        {
            $article = $this->getParentArticle();
            return ($article instanceof Article) ? $article->getName() : null;
        }

        public function getChildArticles()
        {
            return $this->_b2dbLazyLoad('_child_articles');
        }

        public function setArticleType($article_type)
        {
            $this->_article_type = $article_type;
        }

        public function getArticleType()
        {
            return $this->_article_type;
        }

        public function getHistoryUserIDs()
        {
            static $uids = null;
            if ($uids === null)
                $uids = \pachno\core\entities\tables\ArticleHistory::getTable()->getUserIDsByArticleName($this->getName());

            return $uids;
        }

        public function getSubscribers()
        {
            $this->_b2dbLazyLoad('_subscribers');
            return $this->_subscribers;
        }

        public function addSubscriber($user_id)
        {
            \pachno\core\entities\tables\UserArticles::getTable()->addStarredArticle($user_id, $this->getID());
            $this->_new_subscribers[] = $user_id;
        }

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                if ($this->_getParser()->hasMentions())
                {
                    foreach ($this->_getParser()->getMentions() as $user)
                    {
                        if ($user->getID() == framework\Context::getUser()->getID()) continue;

                        if (($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_MENTIONED, false)->isOn())) $this->_addNotificationIfNotNotified(Notification::TYPE_ARTICLE_MENTIONED, $user, $this->getAuthor());
                    }
                }
                if ($this->getAuthor() instanceof User) {
                    $this->_addCreateNotifications($this->getAuthor());
                }
            }
            else
            {
                $history = $this->getHistory();
                $history_item = array_shift($history);

                if ($history_item !== null) $this->_addUpdateNotifications($history_item['author']);
            }

            if (framework\Context::getUser() instanceof \pachno\core\entities\User && framework\Context::getUser()->getNotificationSetting(\pachno\core\framework\Settings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ARTICLES, false)->isOn() && !$this->isSubscriber(framework\Context::getUser()))
            {
                $this->addSubscriber(framework\Context::getUser()->getID());
            }
        }

        protected function _addCreateNotifications($updated_by)
        {
            foreach ($this->getRelatedUsers() as $user)
            {
                if ($this->shouldAutomaticallySubscribeUser($user)) $this->addSubscriber($user->getID());

                if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_NEW_ARTICLES_MY_PROJECTS, false)->isOn())
                {
                    $this->_addNotificationIfNotNotified(Notification::TYPE_ARTICLE_CREATED, $user, $updated_by);
                }
            }
        }

        protected function _addUpdateNotifications($updated_by)
        {
            if (!$updated_by instanceof \pachno\core\entities\User) return;

            foreach ($this->getSubscribers() as $user)
            {
                if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_SUBSCRIBED_ARTICLES, false)->isOn() && $this->isSubscriber($user))
                {
                    $this->_addNotificationIfNotNotified(Notification::TYPE_ARTICLE_UPDATED, $user, $updated_by);
                }
            }
        }

        public function shouldAutomaticallySubscribeUser($user)
        {
            if ($this->isSubscriber($user)) return false;

            if (!$user instanceof \pachno\core\entities\User || $user->getNotificationSetting(\pachno\core\framework\Settings::SETTINGS_USER_SUBSCRIBE_NEW_ARTICLES_MY_PROJECTS, null)->getValue() != 1) return false;

            return true;
        }

        public function isSubscriber($user)
        {
            if (!$user instanceof \pachno\core\entities\User) return false;

            $user_id = (string) $user->getID();
            $subscribers = (array) $this->getSubscribers();
            $new_subscribers = (array) $this->_new_subscribers;

            return (bool) in_array($user_id, $new_subscribers) || (bool) array_key_exists($user_id, $subscribers);
        }

        /**
         * Returns an array with everyone related to this project
         *
         * @return array|\pachno\core\entities\User
         */
        public function getRelatedUsers()
        {
            $uids = array();
            $teams = array();

            // Add the author
            $uids[$this->getAuthorID()] = $this->getAuthorID();

            if ($this->getProject() instanceof \pachno\core\entities\Project)
            {
                // Add all users in the team who leads the project, if valid
                // or add the user who leads the project, if valid
                if ($this->getProject()->getLeader() instanceof \pachno\core\entities\Team)
                {
                    $teams[$this->getProject()->getLeader()->getID()] = $this->getProject()->getLeader();
                }
                elseif ($this->getProject()->getLeader() instanceof \pachno\core\entities\User)
                {
                    $uids[$this->getProject()->getLeader()->getID()] = $this->getProject()->getLeader()->getID();
                }

                // Same for QA
                if ($this->getProject()->getQaResponsible() instanceof \pachno\core\entities\Team)
                {
                    $teams[$this->getProject()->getQaResponsible()->getID()] = $this->getProject()->getQaResponsible();
                }
                elseif ($this->getProject()->getQaResponsible() instanceof \pachno\core\entities\User)
                {
                    $uids[$this->getProject()->getQaResponsible()->getID()] = $this->getProject()->getQaResponsible()->getID();
                }

                foreach ($this->getProject()->getAssignedTeams() as $team)
                {
                    $teams[$team->getID()] = $team;
                }
                foreach ($this->getProject()->getAssignedUsers() as $member)
                {
                    $uids[$member->getID()] = $member->getID();
                }
            }

            foreach ($teams as $team)
            {
                foreach ($team->getMembers() as $user)
                {
                    $uids[$user->getID()] = $user->getID();
                }
            }

            if (framework\Context::getUser() and isset($uids[framework\Context::getUser()->getID()])) unset($uids[framework\Context::getUser()->getID()]);
            $users = \pachno\core\entities\tables\Users::getTable()->getByUserIDs($uids);
            return $users;
        }

        protected function _addNotification($type, $user, $updated_by)
        {
            $notification = new Notification();
            $notification->setTarget($this);
            $notification->setNotificationType($type);
            $notification->setTriggeredByUser($updated_by);
            $notification->setUser($user);
            $notification->save();
        }

        protected function _addNotificationIfNotNotified($type, $user, $updated_by)
        {
            if (! $this->shouldUserBeNotified($user, $updated_by)) return;

            $this->_addNotification($type, $user, $updated_by);
        }

        public function shouldUserBeNotified($user, $updated_by) {
            if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_UPDATED_SELF, false)->isOff() && $user->getID() === $updated_by->getID()) return false;

            if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE, false)->isOff()) return true;

            if ($user->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . '_article_' . $this->getID(), false)->isOff())
            {
                $user->setNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . '_article_' . $this->getID(), true);

                return true;
            }

            return false;
        }

        protected function _parseContent($options = array())
        {
            switch ($this->_content_syntax)
            {
                case framework\Settings::SYNTAX_MD:
                    $parser = new \pachno\core\helpers\TextParserMarkdown();
                    $text = $parser->transform($this->_content);
                    break;
                case framework\Settings::SYNTAX_PT:
                    $options = array('plain' => true);
                case framework\Settings::SYNTAX_MW:
                default:
                    $parser = new \pachno\core\helpers\TextParser($this->_content, true, $this->getID());
                    foreach ($options as $option => $value)
                    {
                        $parser->setOption($option, $value);
                    }
                    $text = $parser->getParsedText();
                    break;
            }

            if (isset($parser))
            {
                $this->_parser = $parser;
            }
            return $text;
        }

        /**
         * Returns the associated parser object
         *
         * @return \pachno\core\helpers\ContentParser
         */
        protected function _getParser()
        {
            if (!isset($this->_parser))
            {
                $this->_parseContent();
            }
            return $this->_parser;
        }

        public function hasMentions()
        {
            return $this->_getParser()->hasMentions();
        }

        public function getMentions()
        {
            return $this->_getParser()->getMentions();
        }

        public function getMentionedUsers()
        {
            $users = array();
            if ($this->hasMentions())
            {
                foreach ($this->getMentions() as $user)
                {
                    $users[$user->getID()] = $user;
                }
            }
            foreach (Comment::getComments($this->getID(), Comment::TYPE_ARTICLE) as $comment)
            {
                foreach ($comment->getMentions() as $user)
                {
                    $users[$user->getID()] = $user;
                }
            }

            return $users;
        }

        public function getLink($mode = 'show')
        {
            switch ($mode) {
                case 'show':
                    if ($this->getProject() instanceof Project) {
                        return framework\Context::getRouting()->generate('publish_project_article', ['project_key' => $this->getProject()->getKey(), 'article_id' => (int) $this->getId(), 'article_name' => $this->getName()]);
                    }

                    return framework\Context::getRouting()->generate('publish_article', ['article_id' => (int) $this->getId(), 'article_name' => $this->getName()]);

                case 'history':
                    return framework\Context::getRouting()->generate('publish_article_history', ['article_id' => (int) $this->getId(), 'article_name' => $this->getName()]);

                case 'edit':
                    if ($this->getProject() instanceof Project) {
                        return framework\Context::getRouting()->generate('publish_project_article_edit', ['project_key' => $this->getProject()->getKey(), 'article_id' => (int) $this->getId()]);
                    }

                    return framework\Context::getRouting()->generate('publish_article_edit', ['article_id' => (int) $this->getId()]);
            }

            return '';
        }

    }
