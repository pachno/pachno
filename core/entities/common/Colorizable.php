<?php

    namespace pachno\core\entities\common;

    use pachno\core\entities\Datatype;
    use pachno\core\framework\Context;

    /**
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Colorizable extends Datatype
    {

        public function setColor($color)
        {
            $this->setItemdata($color);
        }

        public function setItemdata($itemdata)
        {
            $this->_itemdata = (substr($itemdata, 0, 1) == '#' ? '' : '#') . $itemdata;
        }

        public function toJSON($detailed = true)
        {
            return [
                'id' => $this->getID(),
                'name' => $this->getName(),
                'key' => $this->getKey(),
                'itemdata' => $this->getItemdata(),
                'itemtype' => $this->_itemtype,
                'builtin' => $this->isBuiltin(),
                'sort_order' => $this->getOrder(),
                'color' => $this->getColor(),
                'text_color' => $this->getTextColor()
            ];
        }

        /**
         * Return the item color
         *
         * @return string
         */
        public function getColor()
        {
            $itemdata = $this->_itemdata;
            if (strlen($itemdata) == 4) {
                $i = str_split($itemdata);

                return ($i[0] . $i[1] . $i[1] . $i[2] . $i[2] . $i[3] . $i[3]);
            } else {
                return $itemdata;
            }
        }

        public function getTextColor()
        {
            if (!Context::isCLI()) {
                Context::loadLibrary('common');
            }

            $rgb = pachno_hex_to_rgb($this->_itemdata);

            if (!$rgb) return '#333333';

            return 0.299 * $rgb['r'] + 0.587 * $rgb['g'] + 0.114 * $rgb['b'] > 170 ? '#333333' : '#FFFFFF';
        }

    }
