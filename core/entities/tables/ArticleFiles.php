<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework,
        pachno\core\entities\tables\ScopedTable,
        pachno\core\entities\traits\FileLink;
    use pachno\core\entities\tables\Articles;

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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::UID, \pachno\core\entities\tables\Users::getTable(), \pachno\core\entities\tables\Users::ID);
            parent::addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
            parent::addForeignKeyColumn(self::FILE_ID, \pachno\core\entities\tables\Files::getTable(), \pachno\core\entities\tables\Files::ID);
            parent::addInteger(self::ATTACHED_AT, 10);
        }

        public function addByArticleIDandFileID($article_id, $file_id, $insert = true)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($this->count($query) == 0)
            {
                if (! $insert) return true;

                $insertion = new Insertion();
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $insertion->add(self::ATTACHED_AT, NOW);
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

            $ret_arr = array();

            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    try
                    {
                        $file = new \pachno\core\entities\File($row->get(\pachno\core\entities\tables\Files::ID), $row);
                        $file->setUploadedAt($row->get(self::ATTACHED_AT));
                        $ret_arr[$row->get(\pachno\core\entities\tables\Files::ID)] = $file;
                    }
                    catch (\Exception $e)
                    {
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
            if ($res = $this->rawSelectOne($query))
            {
                $this->rawDelete($query);
            }
            return $res;
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

            $article_ids = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $a_id = $row->get(self::ARTICLE_ID);
                    $article_ids[$a_id] = $a_id;
                }
            }
            return $article_ids;
        }

    }
