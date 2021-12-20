<?php

    namespace pachno\core\entities;

    use b2db\Row;
    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\pachno\core\entities\tables\ArticleHistory")
     */
    class ArticleRevision extends IdentifiableScoped
    {
        /**
         * Name of the article.
         *
         * @var string
         * @Column(type="string", length=255)
         */
        protected $_article_name;

        /**
         * Article associated with the revision.
         *
         * @var Article
         */
        protected $_article = null;

        /**
         * Article content prior to revision.
         *
         * @var string
         * @Column(type="text")
         */
        protected $_old_content;

        /**
         * Article content after the revision.
         *
         * @var string
         * @Column(type="text")
         */
        protected $_new_content;

        /**
         * Reason specified by the author for revision change.
         *
         * @var string
         * @Column(type="text", length=255)
         */
        protected $_reason;

        /**
         * Revision number.
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_revision;

        /**
         * Date when the change to article was made.
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_date;

        /**
         * Author associated with the revision.
         *
         * @var User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         *
         */
        protected $_author;

        /**
         * Returns URL pointing to author contributions.
         *
         * @param User $author
         *   Author for which to generate contributions URL. If set to 0,
         *   returns contribution URL for install-time system fixtures. If set
         *   to null, returns contributions URL for all users.
         * @param string $project_namespace
         *   Project namespace for which the URL should be generated.
         *
         * @return string
         *   URL pointing to author contributions. If passed-in $author is
         *   invalid (not a User instance, not 0, and not null), returns null.
         */
        public static function getAuthorContributionsURL($author = null, $project_namespace = "")
        {
            $base_url = make_url('publish_article', ['article_name' => "Special:{$project_namespace}Contributions"]);

            if ($author instanceof User) {
                return "{$base_url}?user={$author->getUsername()}";
            } elseif (is_numeric($author) && $author == 0) {
                return "{$base_url}?user=";
            } elseif ($author === null) {
                return "{$base_url}";
            } else {
                return null;
            }
        }

        /**
         * Article revision constructor.
         *
         * @param Row $row
         */
        public function _construct(Row $row, string $foreign_key = null): void
        {
        }

        public function getName()
        {
            return $this->getArticleName();
        }

        /**
         * Returns article name as stored in the article revision.
         *
         * @return string
         */
        public function getArticleName()
        {
            return $this->_article_name;
        }

        /**
         * Returns old content of the article (prior to change).
         *
         *
         * @return string
         */
        public function getOldContent()
        {
            return $this->_old_content;
        }

        /**
         * Returns new content of the article (at the time of change).
         *
         *
         * @return string
         */
        public function getNewContent()
        {
            return $this->_new_content;
        }

        /**
         * Returns reason for change as specified by revision author.
         *
         *
         * @return string
         */
        public function getReason()
        {
            return $this->_reason;
        }

        /**
         * Returns date of change.
         *
         *
         * @return int
         */
        public function getDate()
        {
            return $this->_date;
        }

        /**
         * Returns author name with username. This method is essentially a light
         * wrapper around User::getNameWithUsername() which covers the case
         * where author ID is 0 (i.e. for initial fixtures).
         *
         * @return string
         *   "none (fixtures)" if author ID was 0, real name and username
         *   combined if author exists, or null if author could not be
         *   retrieved.
         */
        public function getAuthorNameWithUsername()
        {
            $author = $this->getAuthor();

            if ($author instanceof User) {
                return $author->getNameWithUsername();
            } elseif (is_numeric($author) && $author == 0) {
                return "none (fixtures)";
            }

            return null;
        }

        /**
         * Returns revision author.
         *
         * @return User
         */
        public function getAuthor()
        {
            return $this->_b2dbLazyLoad('_author');
        }

        /**
         * Returns article associated with the revision.
         *
         *
         * @return Article
         */
        public function getArticle()
        {
            // Small caching optimisation for fetching the article.
            if ($this->_article === null) {
                $this->_article = Article::getByName($this->getArticleName());
            }

            return $this->_article;
        }

        /**
         * Returns URL for viewing this specific revision.
         *
         *
         * @return string
         */
        public function getRevisionURL()
        {
            return make_url('publish_article_revision', ['article_name' => $this->getArticleName(),
                'revision' => $this->getRevision()]);
        }

        /**
         * Returns revision number.
         *
         *
         * @return int
         */
        public function getRevision()
        {
            return $this->_revision;
        }

        /**
         * Returns URL for viewing the article.
         *
         *
         * @return string
         */
        public function getArticleURL()
        {
            return make_url('publish_article', ['article_name' => $this->getArticleName()]);
        }

        /**
         * Returns URL for viewing revision differences. If this is the first
         * revision, returns null.
         *
         *
         * @return string
         */
        public function getDiffURL()
        {
            if ($this->getRevision() > 1) {
                return make_url('publish_article_diff', ['article_name' => $this->getArticleName(),
                    'from_revision' => $this->getRevision() - 1,
                    'to_revision' => $this->getRevision()]);
            } else {
                return null;
            }
        }

        /**
         * Returns history URL for article associated with the revision.
         *
         *
         * @return string
         */
        public function getHistoryURL()
        {
            return make_url('publish_article_history', ['article_name' => $this->getArticleName()]);
        }
    }
