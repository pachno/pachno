<?php

    namespace pachno\core\entities;

    /**
     * @method static Tag getByKeyish($key)
     * @Table(name="\pachno\core\entities\tables\ListTypes")
     */
    class Tag extends common\Colorizable
    {

        const ITEMTYPE = Datatype::TAG;

        protected static $_items = null;

        protected $_itemtype = Datatype::TAG;

    }
