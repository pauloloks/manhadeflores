<?php
/**
 * 2007-2017 PrestaShop
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
 *  @copyright 2017 DJTAL
 *  @version   1.0.0
 *  @link      http://www.djtal.com.br/
 *  @license
 */

include 'API30/Environment.php';
include 'API30/Merchant.php';
include 'API30/Ecommerce/Environment.php';
include 'API30/Ecommerce/Payment.php';
include 'API30/Ecommerce/Sale.php';
include 'API30/Ecommerce/Customer.php';
include 'API30/Ecommerce/CreditCard.php';
include 'API30/Ecommerce/DebitCard.php';
include 'API30/Ecommerce/Boleto.php';
include 'API30/Ecommerce/CieloEcommerce.php';
include 'API30/Ecommerce/CieloSerializable.php';
include 'API30/Ecommerce/Address.php';

include 'API30/Ecommerce/Request/AbstractSaleRequest.php';
include 'API30/Ecommerce/Request/CreateSaleRequest.php';
include 'API30/Ecommerce/Request/UpdateSaleRequest.php';
include 'API30/Ecommerce/Request/QuerySaleRequest.php';


include 'API30/Ecommerce/Request/CieloError.php';
include 'API30/Ecommerce/Request/CieloRequestException.php';
