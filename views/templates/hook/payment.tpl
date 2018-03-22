{*
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
*}

<div class="row">
    <div class="col-xs-12 col-md-12">
        <p class="payment_module" id="wirecard_payment_button">
            <a href="{$link->getModuleLink('wirecard', 'redirect', array(), true)|escape:'htmlall':'UTF-8'}" title="{l s='Pay with my payment module' mod='wirecard'}">
                <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cc.gif" alt="{l s='Pay with my payment module' mod='wirecard'}" width="64" style="margin:10px 15px 10px 10px;"/>
                {l s='Pay With Credit Card' mod='wirecard'}
            </a>
        </p>
    </div>
</div>
 