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
<div class="box">
    <h4>{l s='Credit Card Payment Details' mod='wirecard'} </h4>
    <table class="table">
        <tr>
            <td>{l s='Amount Paid' mod='wirecard'} </td><td> {displayPrice price=$record.amount_paid}</td>
        </tr>
        <tr>
            <td>{l s='CC Number' mod='wirecard'} </td><td> {$record.cc_number}</td>
        </tr>
        <tr>
            <td>{l s='CC Name' mod='wirecard'} </td><td> {$record.cc_name}</td>
        </tr>
        <tr>
            <td>{l s='Installment' mod='wirecard'} </td><td> {$record.installment}</td>
        </tr>
    </table>
</div>
