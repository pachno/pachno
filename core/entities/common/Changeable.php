<?php

    namespace pachno\core\entities\common;

    /**
     * Changeable item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * Changeable item class
     * 
     * @method boolean revert*() revert*() Reverts a property change
     * @method boolean is*Changed() is*Changed() Checks to see whether a property is changed
     *
     * @package pachno
     * @subpackage main
     */
    abstract class Changeable extends Ownable
    {


        /**
         * List of properties that has been changed somewhere else
         * 
         * @var array
         */
        protected $_unmergeable_items = array();
        
        /**
         * Whether the constructor is done merging changes with the original object
         *  
         * @var boolean
         */
        protected $_merged = false;
        
        /**
         * Whether there was any errors mergin
         * 
         * @var boolean
         */
        protected $_merge_error = false;
        
    }