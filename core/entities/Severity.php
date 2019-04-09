<?php

    namespace pachno\core\entities;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Severity extends Datatype 
    {

        const ITEMTYPE = Datatype::SEVERITY;

        protected static $_items = null;
        
        protected $_itemtype = Datatype::SEVERITY;

        public static function loadFixtures(Scope $scope)
        {
            $severities = array();
            $severities['Low'] = '';
            $severities['Normal'] = '';
            $severities['Critical'] = '';

            foreach ($severities as $name => $itemdata)
            {
                $severity = new Severity();
                $severity->setName($name);
                $severity->setItemdata($itemdata);
                $severity->setScope($scope);
                $severity->save();
            }
        }
        
    }
