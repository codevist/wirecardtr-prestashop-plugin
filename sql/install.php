<?php
/**
 * 2018 Wirecard Ödeme ve Elektronik Para Hizmetleri A.Ş.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 *  @author    Codevist <info@codevist.com>
 *  @copyright 2018 Wirecard Ödeme ve Elektronik Para Hizmetleri A.Ş.
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
 
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'wirecard_payment` (
    `order_id` int(10) unsigned NOT NULL,
    `customer_id` int(10) unsigned NOT NULL,
    `wirecard_id` varchar(64) NULL,
    `amount` decimal(10,4) NOT NULL,
    `amount_paid` decimal(10,4) NOT NULL,
    `installment` int(2) unsigned NOT NULL DEFAULT 1,
    `cardholdername` varchar(60) NULL,
    `cardnumber` varchar(25) NULL,
    `cardexpdate` varchar(8) NULL,
    `createddate` datetime NOT NULL,
    `ipaddress` varchar(16) NULL,
    `status_code` tinyint(1) DEFAULT 1,
    `result_code` varchar(60) NULL,
    `result_message` varchar(256) NULL,
    `mode` varchar(16) NULL,
    `shared_payment_url` varchar(256) NULL,
    KEY `order_id` (`order_id`),
    KEY `customer_id` (`customer_id`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
