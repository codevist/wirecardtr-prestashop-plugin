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
class WirecardRedirectModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public function __construct()
	{
		parent::__construct();
		$this->context = ContextCore::getContext();
	}

	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		$this->addCSS('/views/css/front.css');
		parent::initContent();
	}

	public function postProcess()
	{
		$error_message = false;
		$cart = New Cart((int) Context::getContext()->cart->id);
		$cc_form_key = $this->module->createSecret($cart->secure_key);
		$total_cart = $cart->getOrderTotal(true, Cart::BOTH);

		$tl_currency = (int) Currency::getIdByIsoCode('TRY');
		if (!$tl_currency)
			die($this->l('TRY currency not found'));

		if ($this->context->cookie->id_currency != $tl_currency) {
			$this->context->cookie->id_currency = $tl_currency;
			Tools::redirect($this->context->link->getModuleLink('wirecard', 'redirect'));
		}
		
		if (Tools::getValue('cc_form_key') && Tools::getValue('cc_form_key') == $this->module->createSecret($cart->secure_key) && !isset($_POST['MPAY']))
		{
			$record = $this->module->postToWirecard();
			$this->module->saveRecord($record);
			if($record['shared_payment_url'] != 'null') // Ortak ödemeye yönlen 
			{	
				Tools::redirect($record['shared_payment_url']);
				die;
			}
					
			if($record['status_code'] == 0 ) {//Başarılı işlem			
				$secure_key = Context::getContext()->customer->secure_key;
				$module_name = $this->module->displayName;
				$currency_id = (int) Context::getContext()->currency->id;
				$cart_id = Context::getContext()->cart->id;
				$payment_status = Configuration::get('PS_OS_PAYMENT');
				$total_cart = $cart->getOrderTotal(true, Cart::BOTH);
			
				$this->module->validateOrder($cart_id, $payment_status, $total_cart, $module_name, $error_message, array(), $currency_id, false, $secure_key);

				$order_id = Order::getOrderByCartId((int) $cart_id);
				if ($order_id) {
					$record['order_id'] = $order_id;
					$this->module->saveRecord($record);
					$module_id = $this->module->id;
					Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $module_id . '&id_order=' . $order_id . '&key=' . $secure_key);
				} else {
					$this->errors[] = $this->module->l('An error occured. Please contact the merchant to have more informations');
					return $this->setTemplate('error.tpl');
				}
			}
			else //Başarısız işlem
			{
				$error_message = $record['result_message'];
				$payment_status = Configuration::get('PS_OS_PAYMENT');
			}
		}
		elseif (isset($_POST['MPAY'])) { //Ortak ödemeden gelirse. 
			$record = $this->module->getRecordByOrderId($_POST['MPAY']);
			$record['status_code'] = $_POST['StatusCode'];
			$record['result_code'] = $_POST['ResultCode'];
			$record['result_message'] =$_POST['ResultMessage'];		
			$this->module->saveRecord($record);	
			$payment_status = Configuration::get('PS_OS_PAYMENT');

			if($record['status_code'] == 0 ) {//Başarılı işlem			
				$secure_key = Context::getContext()->customer->secure_key;	
				$module_name = $this->module->displayName;				
				$currency_id = (int) Context::getContext()->currency->id;
				$cart_id = Context::getContext()->cart->id;
				$order_id = Order::getOrderByCartId((int) $cart_id);
				$record['order_id'] = $order_id;
				$module_id = $this->module->id;

				$this->module->validateOrder($cart_id, $payment_status, $total_cart, $module_name, $error_message, array(), $currency_id, false, $secure_key);

				Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $module_id . '&id_order=' . $order_id . '&key=' . $secure_key);	
				exit;
				$error_message = false;
			}
			else { //Başarısız işlem7		
				$error_message = 'Ödeme başarısız oldu: Kart Bankası İşlem Cevabı: ('. $record['result_code'] . ') ' . $record['result_message'];
			}
		}
		// $wirecard_rates = WirecardConfig::calculatePrices($total_cart, $this->module->getRates());
		if (Tools::getValue('action') == 'error') {
			return $this->displayError('An error occurred while trying to redirect the customer');
		} else {
			$this->context->smarty->assign(array(
				'isInstallment' => Configuration::get('WIRECARD_INSTALLMENT'),
				'cc_form_key' => $cc_form_key,
				'error_message' => $error_message,
				'cart_id' => Context::getContext()->cart->id,
				'secure_key' => Context::getContext()->customer->secure_key,
			));
			if(Configuration::get('WIRECARD_MODE') == 'form')
			{
				return $this->setTemplate('redirect.tpl');
			}
			else
			{
				return $this->setTemplate('redirectShared.tpl');		
			}			
		}			
	}

	protected function displayError($message, $description = false)
	{
		$this->context->smarty->assign('path', '
			<a href="' . $this->context->link->getPageLink('order', null, null, 'step=3') . '">' . $this->module->l('Payment') . '</a>
			<span class="navigation-pipe">&gt;</span>' . $this->module->l('Error'));

		array_push($this->errors, $this->module->l($message), $description);

		return $this->setTemplate('error.tpl');
	}
}