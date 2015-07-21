<?php

/**
 * Ffuenf_ProductAlertCleaner extension.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category   Ffuenf
 *
 * @author     Achim Rosenhagen <a.rosenhagen@ffuenf.de>
 * @copyright  Copyright (c) 2015 ffuenf (http://www.ffuenf.de)
 * @license    http://opensource.org/licenses/mit-license.php MIT License
 */

class Ffuenf_ProductAlertCleaner_Helper_Data extends Ffuenf_ProductAlertCleaner_Helper_Core
{

    /**
     * config paths.
     */
    const CONFIG_EXTENSION_ACTIVE = 'productalertcleaner/general/enable';

    /**
     * Variable for if the extension is active.
     *
     * @var bool
     */
    protected $_bExtensionActive;

    /**
     * Check to see if the extension is active.
     *
     * @return bool
     */
    public function isExtensionActive()
    {
        if ($this->_bExtensionActive === null) {
            $this->_bExtensionActive = $this->getStoreFlag(self::CONFIG_EXTENSION_ACTIVE, '_bExtensionActive');
        }
        return $this->_bExtensionActive;
    }
}