<?php

    namespace pachno\core\entities;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Severity extends Datatype
    {

        public const ITEMTYPE = Datatype::SEVERITY;

        protected static $_items = null;

        protected $_itemtype = Datatype::SEVERITY;
        
        protected $json;

        public static function loadFixtures(Scope $scope)
        {
            $severities = [];
            $severities['Low'] = '';
            $severities['Normal'] = '';
            $severities['Critical'] = '';

            foreach ($severities as $name => $itemdata) {
                $severity = new Severity();
                $severity->setName($name);
                $severity->setItemdata($itemdata);
                $severity->setScope($scope);
                $severity->save();
            }
        }
    
        public function toJSON($detailed = true)
        {
            if (!is_array($this->json)) {
                $this->json = parent::toJSON($detailed);
            }
        
            return $this->json;
        }
    
    }
