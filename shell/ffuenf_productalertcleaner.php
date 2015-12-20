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

require_once 'abstract.php';

class Ffuenf_Shell_ProductalertCleaner extends Mage_Shell_Abstract
{
    /**
     * Run script
     */
    public function run()
    {
        echo "Start Ffuenf_ProductAlertCleaner\r\n";
        $run = Mage::getModel("ffuenf_productalertcleaner/cleaner")->clean();
        print_r($run);
        echo "End Ffuenf_ProductAlertCleaner\r\n";
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f ffuenf_productalertcleaner.php -- [options]

  help                          This help

USAGE;
    }
}

//Run Ffuenf ProductalertCleaner Script
$shell = new Ffuenf_Shell_ProductalertCleaner();
$shell->run();