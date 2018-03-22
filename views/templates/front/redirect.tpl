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
<script src="{$base_dir_ssl}/modules/wirecard/views/js/jquery.card.js"></script>
<script src="{$base_dir_ssl}/modules/wirecard/views/js/jquery.payment.min.js"></script>

{capture name=path}{l s='Secure Payment With Credit Card' mod='wirecard'}{/capture}
<section>
    <div class="row" align="center">
        <div class="col-xs-12 col-sm-12">
                    <img src="{$base_dir_ssl}/modules/wirecard/views/img/wirecard_logo.png"/>
            <h2>{l s='Secure Payment With Credit Card' mod='wirecard'}</h2>
                    {l s='This page allows you make a secure credit card payment via an SSL encrypted form' mod='wirecard'}<br/>
                    {l s='You may redirect to the 3D Secure page and use your SMS password.' mod='wirecard'}
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

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <table id="cc_form_table">
                <tr>
                    <td> {l s='Name On Card' mod='wirecard'}
                    <input type="text" id="cc_name" name="cc_name" class="cc_input" placeholder="{l s='Your Name' mod='wirecard'}" value="{if isset($smarty.post.cc_name)}{$smarty.post.cc_name}{/if}"/>
                    </td>               
                <td>
                        {l s='Card Number' mod='wirecard'}
                <input type="text" id="cc_number" name="cc_number" class="cc_input" placeholder="•••• •••• •••• ••••" value="{if isset($smarty.post.cc_number)}{$smarty.post.cc_number}{/if}"/>
                </td>
                </tr>
                <tr>
                <td>
                    {l s='Card Expity Date' mod='wirecard'}
                <input type="text" id="cc_expiry" name="cc_expiry" class="cc_input" placeholder="{l s='MM/YY' mod='wirecard'}" value="{if isset($smarty.post.cc_expiry)}{$smarty.post.cc_expiry}{/if}"/>
                </td>
                
                    <td>
                        {l s='Card CVC' mod='wirecard'}
                <input type="text" id="cc_cvc" name="cc_cvc" class="cc_input" placeholder="•••" value="{if isset($smarty.post.cc_cvc)}{$smarty.post.cc_cvc}{/if}"/>
                </td>

                </tr>
                 {if  {$isInstallment} == 'on'}
                <tr>
                    <td >
                            <h4>Taksit Sayısı  
                            <br>    
                            <select name="wirecard-installment-count">
                            <option value="0">Peşin Ödeme</option>
                            <option value="3">3 Taksit</option>
                            <option value="6">6 Taksit</option>
                            <option value="9">9 Taksit</option></h4>       
                            </select>
                    </td>
                </tr>
                {/if}
            </table>
            <hr/>
            
            <input type="hidden" name="cc_form_key" value="{$cc_form_key}"/>
            <button type="submit" id="cc_form_submit" class="btn btn-lg btn-primary">{l s='Pay Now' mod='wirecard'}</button>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="card-wrapper"></div>
        </div>
    </div>
   
</form> 

<script>
    $('form#cc_form').card({
        // a selector or DOM element for the form where users will
        // be entering their information
        form: 'form#cc_form', // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
		formSelectors: {
			numberInput: 'input#cc_number', // optional — default input[name="number"]
			expiryInput: 'input#cc_expiry', // optional — default input[name="expiry"]
			cvcInput: 'input#cc_cvc', // optional — default input[name="cvc"]
			nameInput: 'input#cc_name' // optional - defaults input[name="name"]
		},
		placeholders: {
		  number: '&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;',
		  cvc: '&bull;&bull;&bull;',
		  expiry: '&bull;&bull;/&bull;&bull;',
		  name: '{l s="Your Name" mod="wirecard"}'
		},
		messages: {
            monthYear: 'mm/yy' // optional - default 'month/year'
        },
        container: '.card-wrapper', // *required*
        width: "100%",
        formatting: true, // optional - default true
        // Default placeholders for rendered fields - optional
        // if true, will log helpful messages for setting up Card
        debug: true // optional - default false
    });


    jQuery(function ($) {
        $('table#cc_form_table').removeClass('error success');
        $('input#cc_number').payment('formatCardNumber');
        $('input#cc_expiry').payment('formatCardExpiry');
        $('input#cc_cvc').payment('formatCardCVC');
        $("#cc_form_submit").attr("disabled", true);

        $('.cc_input').bind('keypress keyup keydown focus', function (e) {
            $(this).removeClass('error');
            $("#cc_form_submit").attr("disabled", true);
            var hasError = false;
            var cardType = $.payment.cardType($('input#cc_number').val());


            if (!$.payment.validateCardNumber($('input#cc_number').val())) {
                $('input#cc_number').addClass('error');
                hasError = 'number';
            }
            if (!$.payment.validateCardExpiry($('input#cc_expiry').payment('cardExpiryVal'))) {
                $('input#cc_expiry').addClass('error');
                hasError = 'expiry';
            }
            if (!$.payment.validateCardCVC($('input#cc_cvc').val(), cardType)) {
                $('input#cc_cvc').addClass('error');
                hasError = 'cvc';
            }
            if ($('input#cc_name').val().length < 3) {
                $('input#cc_name').addClass('error');
                hasError = 'name';
            }

            if (hasError === false) {
//                console.log(hasError);
                $("#cc_form_submit").removeAttr("disabled");
                $("#cc_validation").hide();
            }
            else {
                $("#cc_validation").show();
                $("#cc_form_submit").attr("disabled", true);
                $('table#cc_form_table').addClass('error');
            }
        });
		$('.cc_input').keypress();
    });
</script>
<div align="center">
<br>
<hr>
<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other Payment Methods' mod='wirecard'}</a>
</div>
