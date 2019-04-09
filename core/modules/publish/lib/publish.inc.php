<?php

    function get_spaced_name($camelcased)
    {
        return \pachno\core\framework\Context::getModule('publish')->getSpacedName($camelcased);
    }
