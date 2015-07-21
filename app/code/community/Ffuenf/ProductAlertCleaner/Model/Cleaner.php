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

class Ffuenf_ProductAlertCleaner_Model_Cleaner
{
    /**
     * Clean old quote entries.
     * This method will be called via a Magento crontab task.
     *
     * @return null|array<String>
     */
    public function clean()
    {
        if (!Mage::helper('ffuenf_productalertcleaner')->isExtensionActive()) {
            return;
        }
        $dependentExtension = Mage::helper('core')->isModuleEnabled('Amasty_Xnotif');
        $report = array();
        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write'); /* @var $writeConnection Varien_Db_Adapter_Pdo_Mysql */
        $tableName = Mage::getSingleton('core/resource')->getTableName('product_alert_stock');
        $tableName = $writeConnection->quoteIdentifier($tableName, true);
        
        $olderThan = intval(Mage::getStoreConfig('productalertcleaner/general/clean_alerts_older_than'));
        $olderThan = max($olderThan, 1);

        // delete where older than 1 Year
        // DELETE FROM `product_alert_stock` WHERE `add_date` < NOW() - INTERVAL 365 DAY;
        $startTime = time();
        $sqlOld = sprintf('DELETE FROM %s WHERE add_date < DATE_SUB(Now(), INTERVAL %s DAY)',
            $tableName,
            $olderThan
        );
        $stmt = $writeConnection->query($sqlOld);
        $report['alerts']['old']['count'] = $stmt->rowCount();
        $report['alerts']['old']['duration'] = time() - $startTime;
        Mage::log('[PRODUCTALERTCLEANER] Cleaning productalerts where older than ' . $olderThan . ' days (duration: ' . $report['alerts']['old']['duration'] . ', row count: ' . $report['alerts']['old']['count'] . ')');

        // delete where emails have been send
        // DELETE FROM `product_alert_stock` WHERE `product_alert_stock`.`send_count` > 0;
        $startTime = time();
        $sqlSendCount = sprintf('DELETE FROM %s WHERE send_count > 0',
            $tableName,
            $olderThan
        );
        $stmt = $writeConnection->query($sqlSendCount);
        $report['alerts']['send_count']['count'] = $stmt->rowCount();
        $report['alerts']['send_count']['duration'] = time() - $startTime;
        Mage::log('[PRODUCTALERTCLEANER] Cleaning productalerts where emails already have been send (duration: ' . $report['alerts']['send_count']['duration'] . ', row count: ' . $report['alerts']['send_count']['count'] . ')');

        // only run if dependent extension is installed
        if ($dependentExtension) {
            // delete where not valid emails
            // DELETE FROM `product_alert_stock` WHERE `email` NOT REGEXP '^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$';
            $startTime = time();
            $regexp = "'^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'";
            $sqlInvalidEmails = sprintf("DELETE FROM %s WHERE email NOT REGEXP %s",
                $tableName,
                $regexp
            );
            $stmt = $writeConnection->query($sqlInvalidEmails);
            $report['alerts']['invalid_emails']['count'] = $stmt->rowCount();
            $report['alerts']['invalid_emails']['duration'] = time() - $startTime;
            Mage::log('[PRODUCTALERTCLEANER] Cleaning productalerts where invalid email addresses (duration: ' . $report['alerts']['invalid_emails']['duration'] . ', row count: ' . $report['alerts']['invalid_emails']['count'] . ')');

            // delete duplicates
            // DELETE e1 FROM `product_alert_stock` e1, `product_alert_stock` e2 WHERE e1.alert_stock_id > e2.alert_stock_id AND e1.email = e2.email AND e1.product_id = e2.product_id;
            $startTime = time();
            $sqlDuplicates = sprintf('DELETE e1 FROM %s e1, %s e2 WHERE e1.alert_stock_id > e2.alert_stock_id AND e1.email = e2.email AND e1.product_id = e2.product_id',
                $tableName,
                $tableName
            );
            $stmt = $writeConnection->query($sqlDuplicates);
            $report['alerts']['duplicates']['count'] = $stmt->rowCount();
            $report['alerts']['duplicates']['duration'] = time() - $startTime;
            Mage::log('[PRODUCTALERTCLEANER] Cleaning duplicate productalerts (duration: ' . $report['alerts']['duplicates']['duration'] . ', row count: ' . $report['alerts']['duplicates']['count'] . ')');
        }

        return $report;
    }
}