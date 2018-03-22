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

<div>
    {l s='We noticed a problem with your order. please contact us and let us solve this error.' mod='bankwire'} 
    <a class="btn btn-large btn-info" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Please use customer support by clicking here' mod='wirecard'}</a>.

    <h3>{l s='An error occurred' mod='wirecard'}:</h3>
    <ul class="alert alert-danger">
        {foreach from=$errors item='error'}
            <li>{$error|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
    </ul>
            <a class="button btn btn-default button-medium" href="{$link->getModuleLink('wirecard', 'redirect')|escape:'html'}">
                <span> <i class="icon-chevron-left left"></i> {l s='Click Here To Try Again' mod='wirecard'}</span>
            </a>.

</div>