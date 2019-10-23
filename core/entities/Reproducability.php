<?php

    namespace pachno\core\entities;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Reproducability extends Datatype
    {

        const ITEMTYPE = Datatype::REPRODUCABILITY;

        protected static $_items = null;

        protected $_itemtype = Datatype::REPRODUCABILITY;

        public static function loadFixtures(Scope $scope)
        {
            $reproducabilities = [];
            $reproducabilities["Can't reproduce"] = '';
            $reproducabilities['Rarely'] = '';
            $reproducabilities['Often'] = '';
            $reproducabilities['Always'] = '';

            foreach ($reproducabilities as $name => $itemdata) {
                $reproducability = new Reproducability();
                $reproducability->setName($name);
                $reproducability->setItemdata($itemdata);
                $reproducability->setScope($scope);
                $reproducability->save();
            }
        }

    }
