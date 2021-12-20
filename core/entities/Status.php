<?php

    namespace pachno\core\entities;

    /**
     * @method static Status getByKeyish($key)
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Status extends common\Colorizable
    {

        public const ITEMTYPE = Datatype::STATUS;

        protected static $_items = null;

        protected $_itemtype = Datatype::STATUS;

        public static function loadFixtures(Scope $scope)
        {
            $statuses = [];
            $statuses['New'] = '#FFFFFF';
            $statuses['Investigating'] = '#C2F533';
            $statuses['Confirmed'] = '#FF55AA';
            $statuses['Not a bug'] = '#44FC1D';
            $statuses['Being worked on'] = '#55CC55';
            $statuses['Near completion'] = '#77DD33';
            $statuses['Ready for testing / QA'] = '#5555CC';
            $statuses['Testing / QA'] = '#7777CC';
            $statuses['Closed'] = '#C2F588';
            $statuses['Postponed'] = '#FFAA55';
            $statuses['Done'] = '#77DD33';
            $statuses['Fixed'] = '#55CC55';

            foreach ($statuses as $name => $itemdata) {
                $status = new Status();
                $status->setName($name);
                $status->setItemdata($itemdata);
                $status->setScope($scope);
                $status->save();
            }
        }

        public function canBeDeleted()
        {
            return !$this->hasLinkedWorkflowStep();
        }

        public function hasLinkedWorkflowStep()
        {
            return (bool)tables\WorkflowSteps::getTable()->countByStatusID($this->getID());
        }

    }
