<?php

    namespace pachno\core\modules\publish;

    use pachno\core\entities\Article;
    use pachno\core\entities\Permission;
    use pachno\core\entities\Project;
    use pachno\core\entities\tables\ArticleFiles;
    use pachno\core\entities\tables\Articles;
    use pachno\core\entities\tables\Links;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\tables\UserArticles;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\cli\Command;
    use pachno\core\framework\CoreModule;
    use pachno\core\framework\Event;
    use pachno\core\framework\Request;
    use pachno\core\helpers\TextParser;

    /**
     * The wiki class
     *
     * @package pachno
     * @subpackage publish
     *
     * @Table(name="\pachno\core\entities\tables\Modules")
     */
    class Publish extends CoreModule
    {

        const VERSION = '2.0';

        protected $_longname = 'Wiki';

        protected $_description = 'Enables Wiki-functionality';

        protected $_module_config_title = 'Wiki';

        protected $_module_config_description = 'Set up the Wiki module from this section';

        protected $_has_config_settings = false;

        /**
         * @var Article
         */
        protected $_current_article;

        public static function getArticleLink($article_name, $project = null, $mode = 'show', $legacy_name = false)
        {
            $article = Articles::getTable()->getArticleByName($article_name, $project, $legacy_name);
            if (!$article instanceof Article && $project instanceof Project) {
                $article = Articles::getTable()->getArticleByName($project->getKey() . ':' . $article_name, $project, $legacy_name);
            }
            if (!$article instanceof Article) {
                $article = new Article();
                $article->setName($article_name);
                $article->setProject($project);
            }

            return $article->getLink($mode);
        }

        public function setCurrentArticle(Article $article)
        {
            $this->_current_article = $article;
        }

        public function getCurrentArticle(): ?Article
        {
            return $this->_current_article;
        }

        public function postConfigSettings(Request $request)
        {
            if ($request->hasParameter('import_articles')) {
                $cc = 0;
                foreach ($request['import_article'] as $article_name => $import) {
                    $cc++;
                    Articles::getTable()->deleteArticleByName(urldecode($article_name));
                    $content = file_get_contents(PACHNO_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $article_name);
                    Article::createNew(urldecode($article_name), $content, null, ['overwrite' => true, 'noauthor' => true]);
                }
                framework\Context::setMessage('module_message', framework\Context::getI18n()->__('%number_of_articles articles imported successfully', ['%number_of_articles' => $cc]));
            } else {
                $settings = ['allow_camelcase_links', 'menu_title', 'hide_wiki_links', 'free_edit', 'require_change_reason'];
                foreach ($settings as $setting) {
                    if ($request->hasParameter($setting)) {
                        $this->saveSetting($setting, $request->getParameter($setting));
                    }
                }
            }
        }

        public function stripExclamationMark($matches, $parser)
        {
            return mb_substr($matches[0], 1);
        }

        /**
         * Helper function for obtaining article link during parsing of
         * an Article.
         *
         * @param array $matches Result of regular expression matching. First element should be the article name.
         * @param TextParser $parser Parser used for processing the originating article.
         *
         * @return string Fully HTML-encoded link (i.e. <a> tag). If article does not exist, tag will be assigned class "missing_wiki_page".
         */
        public function getArticleLinkTag($matches, $parser)
        {
            $article_link = $matches[0];
            $parser->addInternalLinkOccurrence($article_link);
            $article_name = self::getSpacedName($matches[0]);

            if (!framework\Context::isCLI()) {
                framework\Context::loadLibrary('ui');
                $options = [];

                // Assign CSS class to article if it does not exist.
                if (Articles::getTable()->getArticleByName($matches[0]) === null) {
                    $options["class"] = "missing_wiki_page";
                }

                return link_tag(make_url('publish_article', ['article_name' => $matches[0]]), $article_name, $options);
            } else {
                return $matches[0];
            }
        }

        public static function getSpacedName($camelcased)
        {
            return preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $camelcased);
        }

        public function getLatestArticles(Project $project = null)
        {
            return Articles::getTable()->getArticles($project);
        }

        public function getMenuItems($target_id = 0)
        {
            return Links::getTable()->getLinks('wiki', $target_id);
        }

        public function listen_projectLinks(Event $event)
        {
            framework\ActionComponent::includeComponent('publish/projectlinks', ['project' => $event->getSubject()]);
        }

        protected function _getPermissionslist()
        {
            return [];
        }

        public function getMenuTitle($project_context = null)
        {
            $project_context = ($project_context !== null) ? $project_context : framework\Context::isProjectContext();
            $i18n = framework\Context::getI18n();
            if (($menu_title = $this->getSetting('menu_title')) !== null) {
                switch ($menu_title) {
                    case 5:
                        return ($project_context) ? $i18n->__('Project archive') : $i18n->__('Archive');
                    case 3:
                        return ($project_context) ? $i18n->__('Project documentation') : $i18n->__('Documentation');
                    case 4:
                        return ($project_context) ? $i18n->__('Project documents') : $i18n->__('Documents');
                    case 2:
                        return ($project_context) ? $i18n->__('Project help') : $i18n->__('Help');
                }
            }

            return $i18n->__('Documentation');
        }

        public function listen_fileHasAccess(Event $event)
        {
            $article_ids = ArticleFiles::getTable()->getArticlesByFileID($event->getSubject()->getID());

            foreach ($article_ids as $article_id) {
                $article = new Article($article_id);
                if ($article->hasAccess()) {
                    $event->setProcessed();
                    $event->setReturnValue(true);
                    break;
                }
            }
        }

        /**
         * Header "Publish" page names
         *
         * @Listener(module="core", identifier="project/templates/projectheader::pagename")
         *
         * @param Event $event
         */
        public function dashboardProjectHeaderPagename(Event $event)
        {
            switch (framework\Context::getRouting()->getCurrentRoute()->getModuleName()) {
                case 'publish':
                    $event->setReturnValue(framework\Context::getI18n()->__('Documentation'));
                    $event->setProcessed(true);
                    break;
            }
        }

        /**
         * Header selected tab listener
         *
         * @Listener(module="core", identifier="header_menu::selectedTab")
         *
         * @param Event $event
         */
        public function headerMenuSelectedTab(Event $event)
        {
            if (framework\Context::getRouting()->getCurrentRoute()->getModuleName() == 'publish') {
                $event->setReturnValue('publish');
                $event->setProcessed();
            }
        }

        /**
         * Header wiki menu and search dropdown / list
         *
         * @Listener(module="core", identifier="header_menu_entries")
         *
         * @param Event $event
         */
        public function listen_HeaderMenuLink(Event $event)
        {
            framework\ActionComponent::includeComponent('publish/headermenulink');
        }

        /**
         * Header wiki menu and search dropdown / list
         *
         * @Listener(module="core", identifier="project_header_sections")
         *
         * @param Event $event
         */
        public function listen_ProjectHeaderSections(Event $event)
        {
            if (framework\Context::getRouting()->getCurrentRoute()->getModuleName() == 'publish') {
                framework\ActionComponent::includeComponent('publish/headeractions');
            }
        }

        /**
         * Header wiki menu and search dropdown / list
         *
         * @Listener(module="core", identifier="templates/header::projectmenulinks")
         *
         * @param Event $event
         */
        public function listen_MenustripLinks(Event $event)
        {
            $article = Articles::getTable()->getOrCreateMainPage($event->getSubject());

            if ($event->getSubject() instanceof Project) {
                $project_url = framework\Context::getRouting()->generate('publish_project_article', ['project_key' => $event->getSubject()->getKey(), 'article_id' => $article->getId(), 'article_name' => 'Main Page']);
            } else {
                $project_url = framework\Context::getRouting()->generate('publish_article', ['article_id' => $article->getId(), 'article_name' => 'Main Page']);
            }

            $wiki_url = ($event->getSubject() instanceof Project && $event->getSubject()->hasWikiURL()) ? $event->getSubject()->getWikiURL() : null;
            $top_level_articles = Articles::getTable()->getManualSidebarArticles(false, $article->getProject());
            $top_level_categories = Articles::getTable()->getManualSidebarArticles(true, $article->getProject());
            $overview_article = $article;
            usort($top_level_articles, '\pachno\core\entities\Article::sortArticleChildren');
            usort($top_level_categories, '\pachno\core\entities\Article::sortArticleChildren');
            framework\ActionComponent::includeComponent('publish/menustriplinks', ['project_url' => $project_url, 'project' => $event->getSubject(), 'wiki_url' => $wiki_url, 'top_level_articles' => $top_level_articles, 'top_level_categories' => $top_level_categories, 'overview_article' => $overview_article]);
        }

        /**
         * Listen to header menu strip
         *
         * @Listener(module="core", identifier="header_menu_strip")
         *
         * @param Event $event
         */
        public function listenerMainMenustrip(Event $event)
        {
            $route = $event->getSubject();

            if (!$route instanceof framework\routing\Route)
                return;

            if ($route->getModuleName() == 'publish') {
                $component = framework\Action::returnComponentHTML('publish/mainmenustrip');
                $event->setReturnValue($component);
                $event->setProcessed();
            }
        }

        /**
         * @param null $project
         * @param null $scope
         *
         * @return Article
         */
        public function createMainPageArticle($project = null, $scope = null): Article
        {
            $fixtures_path = PACHNO_CORE_PATH . 'modules' . DS . 'publish' . DS . 'fixtures' . DS;
            if ($project instanceof Project) {
                $data = file_get_contents($fixtures_path . 'project.json');
                $content = str_replace('%projectname', $project->getName(), $data);
            } else {
                $content = file_get_contents($fixtures_path . 'main.json');
            }

            return Article::createNew("Main Page", $content, $scope, ['noauthor' => true], $project);
        }

        public function listen_createNewProject(Event $event)
        {
            $this->createMainPageArticle($event->getSubject());

            framework\Context::setPermission(Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION, 'project_' . $event->getSubject()->getID(), "publish", framework\Context::getUser()->getID(), 0, 0);
            framework\Context::setPermission(Permission::PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION, 'project_' . $event->getSubject()->getID(), "publish", framework\Context::getUser()->getID(), 0, 0);
        }

        public function getTabKey()
        {
            return (framework\Context::isProjectContext()) ? parent::getTabKey() : 'wiki';
        }

        public function canUserReadArticle(Article $article)
        {
            return $this->_checkArticlePermissions($article, Permission::PERMISSION_PROJECT_ACCESS_DOCUMENTATION);
        }

        protected function _checkArticlePermissions(Article $article, $permission_name)
        {
            $user = framework\Context::getUser();
            $target_id = ($article->getProject() instanceof Project) ? $article->getProject()->getID() : 0;

            $allowed = $user->hasPermission($permission_name, $target_id);
            if ($target_id) {
                $allowed = $allowed ?? $user->hasPermission($permission_name, 0);
            }

            return $allowed ?? false;
        }

        public function canUserEditArticle(Article $article)
        {
            if ($article->getAuthorID() == framework\Context::getUser()->getID()) {
                return true;
            }

            return $this->_checkArticlePermissions($article, Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION);
        }

        public function listen_quicksearchDropdownFirstItems(Event $event)
        {
            $searchterm = $event->getSubject();
            framework\ActionComponent::includeComponent('publish/quicksearch_dropdown_firstitems', ['searchterm' => $searchterm]);
        }

        public function listen_quicksearchDropdownFoundItems(Event $event)
        {
            $searchterm = $event->getSubject();
            list ($resultcount, $articles) = Article::findArticlesByContentAndProject($searchterm, framework\Context::getCurrentProject());
            framework\ActionComponent::includeComponent('publish/quicksearch_dropdown_founditems', ['searchterm' => $searchterm, 'articles' => $articles, 'resultcount' => $resultcount]);
        }

        /**
         * Returns an array of articles ids which are "starred" by this user
         *
         * @return array
         */
        public function User__getStarredArticles(Event $event)
        {
            $user = $event->getSubject();
            $this->User__populateStarredArticles($user);
            $event->setProcessed();
            $event->setReturnValue($user->_retrieve('publish', 'starredarticles'));

            return;
        }

        /**
         * Populate the array of starred articles
         */
        protected function User__populateStarredArticles(User $user)
        {
            if ($user->_isset('publish', 'starredarticles') === null) {
                $articles = UserArticles::getTable()->getUserStarredArticles($user->getID());
                $user->_store('publish', 'starredarticles', $articles);
            }
        }

        /**
         * Returns whether or not an article is starred
         *
         * @return boolean
         */
        public function User__isArticleStarred(Event $event)
        {
            $user = $event->getSubject();
            $arguments = $event->getParameters();
            $article_id = $arguments[0];
            if ($user->_isset('publish', 'starredarticles')) {
                $articles = $user->getStarredArticles();
                $event->setProcessed();
                $event->setReturnValue(array_key_exists($article_id, $articles));

                return;
            } else {
                $event->setProcessed();
                $event->setReturnValue(UserArticles::getTable()->hasStarredArticle($user->getID(), $article_id));

                return;
            }
        }

        /**
         * Adds an article to the list of articles "starred" by this user
         *
         * @return boolean
         */
        public function User__addStarredArticle(Event $event)
        {
            $user = $event->getSubject();
            $arguments = $event->getParameters();
            $article_id = $arguments[0];
            if ($user->isLoggedIn() && !$user->isGuest()) {
                if (UserArticles::getTable()->hasStarredArticle($user->getID(), $article_id)) {
                    $event->setProcessed();
                    $event->setReturnValue(true);

                    return;
                }

                UserArticles::getTable()->addStarredArticle($user->getID(), $article_id);
                if ($user->_isset('publish', 'starredarticles')) {
                    $article = Articles::getTable()->selectById($article_id);
                    $articles = $user->_retrieve('publish', 'starredarticles');
                    $articles[$article->getID()] = $article;
                    $user->_store('publish', 'starredarticles', $articles);
                }
                $event->setProcessed();
                $event->setReturnValue(true);

                return;
            }

            $event->setProcessed();
            $event->setReturnValue(false);

            return;
        }

        /**
         * Removes an article from the list of flagged articles
         *
         * @param Event $event
         */
        public function User__removeStarredArticle(Event $event)
        {
            $user = $event->getSubject();
            $arguments = $event->getParameters();
            $article_id = $arguments[0];
            UserArticles::getTable()->removeStarredArticle($user->getID(), $article_id);
            if (isset($user->_starredarticles)) {
                $articles = $user->_retrieve('publish', 'starredarticles');
                unset($articles[$article_id]);
                $user->_store('publish', 'starredarticles', $articles);
            }
            $event->setProcessed();
            $event->setReturnValue(true);
        }

        /**
         * Removes an article from the list of flagged articles
         *
         * @param Event $event
         */
        public function Files__getUnattachedFiles(Event $event)
        {
            $event->setProcessed();
            $event->addToReturnList(ArticleFiles::getTable()->getLinkedFileIds());
        }

        public function getFontAwesomeIcon()
        {
            return 'newspaper';
        }

        public function getFontAwesomeIconStyle()
        {
            return 'fas';
        }

        public function getFontAwesomeColor()
        {
            return '#555';
        }

        protected function _initialize()
        {
            if ($this->isEnabled() && $this->getSetting('allow_camelcase_links')) {
                TextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', [$this, 'getArticleLinkTag']);
                TextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', [$this, 'stripExclamationMark']);
            }
        }

        protected function _addAvailablePermissions()
        {
        }

        protected function _addListeners()
        {
            if (!framework\Context::isInstallmode() && $this->isWikiTabsEnabled()) {
                Event::listen('core', 'project_overview_item_links', [$this, 'listen_projectLinks']);
            }
            Event::listen('core', 'pachno\core\entities\Project::_postSave', [$this, 'listen_createNewProject']);
            Event::listen('core', 'pachno\core\entities\File::hasAccess', [$this, 'listen_fileHasAccess']);
            Event::listen('core', 'pachno\core\entities\User::__getStarredArticles', [$this, 'User__getStarredArticles']);
            Event::listen('core', 'pachno\core\entities\User::__isArticleStarred', [$this, 'User__isArticleStarred']);
            Event::listen('core', 'pachno\core\entities\User::__addStarredArticle', [$this, 'User__addStarredArticle']);
            Event::listen('core', 'pachno\core\entities\User::__removeStarredArticle', [$this, 'User__removeStarredArticle']);
            Event::listen('core', 'pachno\core\entities\\tables\Files::getUnattachedFiles', [$this, 'Files__getUnattachedFiles']);
            Event::listen('core', 'upload', [$this, 'listen_upload']);
            Event::listen('core', 'quicksearch_dropdown_firstitems', [$this, 'listen_quicksearchDropdownFirstItems']);
            Event::listen('core', 'quicksearch_dropdown_founditems', [$this, 'listen_quicksearchDropdownFoundItems']);
        }

        public function isWikiTabsEnabled()
        {
            return (bool)($this->getSetting('hide_wiki_links') != 1);
        }

        protected function _install($scope)
        {
            $admin_group_id = framework\Settings::getAdminGroup()->getID();
            framework\Context::setPermission(Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION, 0, 'core', 0, $admin_group_id, 0, $scope);
            framework\Context::setPermission(Permission::PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION, 0, 'core', 0, $admin_group_id, 0, $scope);
        }

        protected function _loadFixtures($scope)
        {
            $admin_group_id = framework\Settings::getAdminGroup()->getID();
            $this->loadFixturesArticles($scope);

            framework\Context::setPermission(Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION, 0, 'core', 0, $admin_group_id, 0, $scope);
            framework\Context::setPermission(Permission::PERMISSION_MANAGE_PROJECT_MODERATE_DOCUMENTATION, 0, 'core', 0, $admin_group_id, 0, $scope);
        }

        public function loadFixturesArticles($scope, $overwrite = true)
        {
            if (framework\Context::isCLI())
                Command::cli_echo("Loading default articles\n");
            $this->loadArticles(null, $overwrite, $scope);
            if (framework\Context::isCLI())
                Command::cli_echo("... done\n");
        }

        /**
         * @param Project $project
         * @param bool $overwrite
         * @param null $scope
         */
        public function loadArticles($project = null, $overwrite = true, $scope = null)
        {
            $scope = $scope ?? framework\Context::getScope()->getID();
            $this->createMainPageArticle(null, $scope);

//            $_path_handle = opendir($fixtures_path);
//            while ($original_article_name = readdir($_path_handle)) {
//                if (mb_strpos($original_article_name, '.') === false) {
//                    $imported = false;
//                    if (framework\Context::isCLI()) {
//                        Command::cli_echo('Saving ' . urldecode($original_article_name) . "\n");
//                    }
//                    if ($overwrite) {
//                        Articles::getTable()->deleteArticleByName(urldecode($original_article_name));
//                    }
//                    if (Articles::getTable()->getArticleByName(urldecode($original_article_name)) === null) {
//                        $content = file_get_contents($fixtures_path . $original_article_name);
//                        Article::createNew(urldecode($original_article_name), $content, $scope, ['overwrite' => $overwrite, 'noauthor' => true]);
//                        $imported = true;
//                    }
//                    Event::createNew('publish', 'fixture_article_loaded', urldecode($original_article_name), ['imported' => $imported])->trigger();
//                }
//            }
        }

    }
