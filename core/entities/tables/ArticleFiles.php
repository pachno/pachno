<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use Exception;
    use pachno\core\entities\File;
    use pachno\core\entities\traits\FileLink;
    use pachno\core\framework;

    /**
     * Articles <-> Files table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Articles <-> Files table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="articlefiles")
     */
    class ArticleFiles extends ScopedTable
    {

        use FileLink;

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'articlefiles';

        const ID = 'articlefiles.id';

        const SCOPE = 'articlefiles.scope';

        const UID = 'articlefiles.uid';

        const ATTACHED_AT = 'articlefiles.attached_at';

        const FILE_ID = 'articlefiles.file_id';

        const ARTICLE_ID = 'articlefiles.article_id';

        public function addByArticleIDandFileID($article_id, $file_id, $attached_at = null)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($this->count($query) == 0) {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                if ($attached_at === null) {
                    $insertion->add(self::ATTACHED_AT, NOW);
                } else {
                    $insertion->add(self::ATTACHED_AT, $attached_at);
                }
                $insertion->add(self::ARTICLE_ID, $article_id);
                $insertion->add(self::FILE_ID, $file_id);
                $this->rawInsert($insertion);
            }
        }

        public function getByArticleID($article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            $ret_arr = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    try {
                        $file = new File($row->get(Files::ID), $row);
                        $file->setUploadedAt($row->get(self::ATTACHED_AT));
                        $ret_arr[$row->get(Files::ID)] = $file;
                    } catch (Exception $e) {
                        $this->rawDeleteById($row->get(self::ID));
                    }
                }
            }

            return $ret_arr;
        }

        public function removeByArticleIDandFileID($article_id, $file_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        public function deleteFilesByArticleID($article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        public function getArticlesByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $article_ids = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $a_id = $row->get(self::ARTICLE_ID);
                    $article_ids[$a_id] = $a_id;
                }
            }

            return $article_ids;
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
            parent::addForeignKeyColumn(self::FILE_ID, Files::getTable(), Files::ID);
            parent::addInteger(self::ATTACHED_AT, 10);
        }

    }
