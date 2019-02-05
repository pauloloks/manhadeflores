<?php
/**
* 2007-2016 PrestaShop
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
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'djtalbrazilianregister`';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'djtalbrazilianregister` (
    `id_djtalbrazilianregister` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `cpf` VARCHAR(11) DEFAULT NULL,
    `cnpj` VARCHAR(14) DEFAULT NULL,
    `passport` TEXT DEFAULT NULL,
    `rg` TEXT DEFAULT NULL,
    `ie` TEXT DEFAULT NULL,
    `sr` TEXT DEFAULT NULL,
    `comp` TEXT DEFAULT NULL,
    PRIMARY KEY  (`id_djtalbrazilianregister`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
    if (Db::getInstance()->execute($query) == false)
        return false;
