<?php

    namespace pachno\core\entities;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class ActivityType extends Datatype
    {

        const ITEMTYPE = Datatype::ACTIVITYTYPE;

        protected static $_items = null;

        protected $_key = null;

        protected $_itemtype = Datatype::ACTIVITYTYPE;

        public static function loadFixtures(Scope $scope)
        {
            foreach (["Investigation", "Documentation", "Development", "Testing", "Deployment"] as $name) {
                $activitytype = new ActivityType();
                $activitytype->setName($name);
                $activitytype->setItemdata('');
                $activitytype->setScope($scope);
                $activitytype->save();
            }
        }

        public static function getActivityTypeByKeyish($key)
        {
            return self::getByKeyish($key);
        }

    }
