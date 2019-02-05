<?php
/**
* 2007-2015 PrestaShop
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
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    DJTAL
*  @copyright 2015 DJTAL
*  @version   1.0.0
*  @link      http://www.djtal.com.br/
*  @license
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cielo_authorization` (
    `id_cielo_authorization` int(11) NOT NULL AUTO_INCREMENT,
    `id_order` int(11) NULL,
    `id_cart` int(11) NULL,
    `paymentId` VARCHAR(36) NULL,
    `authorizationCode` TEXT NULL,
    `type` VARCHAR(16) NULL,
    `provider` VARCHAR(60) NULL,
    `proofOfSale` VARCHAR(20) NULL,
    `tid` VARCHAR(40) NULL,
    `amount` VARCHAR(15) NULL,
    `capturedAmount` VARCHAR(15) NULL,
    `receivedDate` VARCHAR(30) NULL,
    `capturedDate` VARCHAR(30) NULL,
    `returnCode` VARCHAR(32) NULL,
    `returnMessage` TEXT NULL,
    `status` int(2) NULL,
    `customer_fisc` VARCHAR(14) NULL,
    `num_installment` int(11) DEFAULT \'1\' NOT NULL,
    `secret_key` TEXT NULL,
    PRIMARY KEY  (`id_cielo_authorization`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
    if (Db::getInstance()->execute($query) == false)
        return false;
