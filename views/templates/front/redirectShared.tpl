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

<script>

    {*    {if	$id_currency_cookie != $currency_default}
    setCurrency({$currency_default});
    alert('Seçtiğiniz para birimi bu ödeme yönteminde kullanılamıyor. Kurunuz {$curname->name} olarak değiştrildi.')
    {/if}*}
</script>   
<link rel="stylesheet" href="{$base_dir_ssl}/modules/wirecard/views/css/form.css" />

{capture name=path}{'Wirecard - Kredi Kartı ile Güvenli Ödeme'}{/capture}
<section>
    <div class="row" align="center">
        <div class="col-xs-12 col-sm-12">
            <img src="{$base_dir_ssl}/modules/wirecard/views/img/wirecard_logo.png"/>
            <h2>{'Wirecard - Kredi Kartı ile Güvenli Ödeme'}</h2>
                    {'Bu form, kredi kartınızdan yüksek güvenlikli SSL şifrelemesi ile güvenli ödeme yapmanızı sağlar.'}<br/>
                    {'3D Secure yöntemi ile bankanıza yönlendirilip SMS şifrenizi girmeniz istenebilir.'}
        </div>
       
    </div>
    {if $error_message}
        <div class="row">
            <div class="alert alert-danger" id="errDiv">
                {'Bankanız ödemeyi onaylamadı ve şu cevabı verdi:'} <br/> 
                <b>{$error_message}</b><br/>
                {'Lütfen formu gözden geçirip yeniden deneyin.'}
            </div>
        </div>
    {/if}
    <hr/>
</section>
<form novalidate autocomplete="on" method="POST" id="cc_form">
<div align="center">
    <div class="row">
        <div class="col-xs-12 col-sm-12">     
            <input type="hidden" name="cc_form_key" value="{$cc_form_key}"/>
            <br>  
            <button type="submit" id="cc_form_submit" class="btn btn-lg btn-primary">{'Ödemeyi Tamamla'}</button>
        </div>
    </div>
</form> 
<br>
<hr>
<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{'Diğer Ödeme Yöntemleri'}</a>
</div>
