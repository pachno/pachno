<?php

    namespace pachno\core\entities;

    use pachno\core\framework;

    /**
     * @method static Priority getByKeyish($key)
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Priority extends Datatype
    {

        public const ITEMTYPE = Datatype::PRIORITY;

        public const CRITICAL = 1;

        public const HIGH = 2;

        public const NORMAL = 3;

        public const LOW = 4;

        public const TRIVIAL = 5;

        protected static $_items = null;

        protected $_itemtype = Datatype::PRIORITY;

        protected $_abbreviation = null;
        
        protected $json;

        public static function loadFixtures(Scope $scope)
        {
            $priorities = [];
            $priorities['Critical'] = self::CRITICAL;
            $priorities['High'] = self::HIGH;
            $priorities['Normal'] = self::NORMAL;
            $priorities['Low'] = self::LOW;
            $priorities['Trivial'] = self::TRIVIAL;

            foreach ($priorities as $name => $itemdata) {
                $priority = new Priority();
                $priority->setName($name);
                $priority->setItemdata($itemdata);
                $priority->setScope($scope);
                $priority->save();
            }
        }

        public static function getAvailableValues()
        {
            return [
                self::CRITICAL => 'exclamation',
                self::HIGH => 'angle-up',
                self::NORMAL => 'minus',
                self::LOW => 'angle-down',
                self::TRIVIAL => 'angle-double-down'
            ];
        }

        public function getValue()
        {
            return $this->_itemdata;
        }

        public function getAbbreviation()
        {
            if ($this->_abbreviation === null) {
                $this->_abbreviation = mb_substr(framework\Context::getI18n()->__($this->getName()), 0, 1);
            }

            return $this->_abbreviation;
        }
    
        public function toJSON($detailed = true)
        {
            if (!is_array($this->json)) {
                $this->json = parent::toJSON($detailed);
            }
        
            return $this->json;
        }
    
    }
