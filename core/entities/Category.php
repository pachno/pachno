<?php

    namespace pachno\core\entities;

    use pachno\core\framework;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Category extends common\Colorizable
    {

        public const ITEMTYPE = Datatype::CATEGORY;

        protected $_itemtype = Datatype::CATEGORY;
        
        protected $json;

        public static function loadFixtures(Scope $scope)
        {
            $categories = ['General' => '', 'Security' => '', 'User interface' => ''];
            $categories['General'] = '#FFFFFF';
            $categories['Security'] = '#C2F533';
            $categories['User interface'] = '#55CC55';

            foreach ($categories as $name => $color) {
                $category = new Category();
                $category->setName($name);
                $category->setColor($color);
                $category->setScope($scope);
                $category->save();
            }
        }

        /**
         * Whether or not the current or target user can access the category
         *
         * @param null $target_user
         *
         * @return boolean
         */
        public function hasAccess($target_user = null)
        {
            return true;
        }
        
        public function toJSON($detailed = true)
        {
            if (!is_array($this->json)) {
                $this->json = parent::toJSON($detailed);
            }
            
            return $this->json;
        }
    
    }
