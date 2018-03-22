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

if (!defined('_PS_VERSION_')) {
	exit;
}

class Wirecard extends PaymentModule
{
	protected $config_form = false;
	public function __construct()
	{
		$this->name = 'wirecard';
		$this->tab = 'payments_gateways';
		$this->version = 1.1;
		$this->author = 'Wirecard adına Codevist BT';
		$this->need_instance = 1;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Wirecard Kredi Kartı İle Ödeme');
		$this->description = $this->l('Kredi kartı ile peşin ve taksitli ödeme');
	}

	public function install()
	{
		if (extension_loaded('curl') == false) {
			$this->_errors[] = $this->l('Bu modülün çalışabilmesi için Curl eklentisinin sunucuya yüklü olması gerekiyor.');
			return false;
		}
		include(dirname(__FILE__) . '/sql/install.php');

		Configuration::updateValue('WIRECARD_ENABLED', 'on');
		Configuration::updateValue('WIRECARD_USER_CODE', false);
		Configuration::updateValue('WIRECARD_PIN', false);
		Configuration::updateValue('WIRECARD_INSTALLMENT', 'on');
		Configuration::updateValue('WIRECARD_MODE', 'shared3d');

		return parent::install() &&
				$this->registerHook('header') &&
				$this->registerHook('backOfficeHeader') &&
				$this->registerHook('payment') &&
				$this->registerHook('paymentReturn') &&
				$this->registerHook('actionOrderDetail') &&
				$this->registerHook('actionPaymentConfirmation') &&
				$this->registerHook('displayAdminOrderContentOrder') &&
				$this->registerHook('displayPayment') &&
				$this->registerHook('displayPaymentReturn') &&
				$this->registerHook('productfooter');
	}

	public function uninstall()
	{
		Configuration::deleteByName('WIRECARD_ENABLED');
		Configuration::deleteByName('WIRECARD_USER_CODE');
		Configuration::deleteByName('WIRECARD_PIN');
		Configuration::deleteByName('WIRECARD_INSTALLMENT');
		Configuration::deleteByName('WIRECARD_MODE');
		return parent::uninstall();
	}

	public function getContent()
	{
		if (((bool) Tools::isSubmit('submitWirecardModule')) == true) {
			$this->postProcess();
		}

	

		$this->context->smarty->assign(array(
			'module_dir' => $this->_path,
			'wirecard_setting_form' => $this->WirecardRenderForm(),
			'wirecard_try_currency_enabled' => (int) Currency::getIdByIsoCode('TRY'),
			'wirecard_registered' => Configuration::get('WIRECARD_REGISTERED'),
		));

		return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
	}

	protected function WirecardRenderForm()
	{
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitWirecardModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
				. '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Ayarlar'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-key"></i>',
						'name' => 'WIRECARD_USER_CODE',
						'label' => $this->l('Wirecard User Code'),
						'desc' => $this->l('Wirecard tarafından atanan üye iş yeri numarası'),
					),
					array(
						'col' => 4,
						'type' => 'text',
						'prefix' => '<i class="icon icon-key"></i>',
						'name' => 'WIRECARD_PIN',
						'label' => $this->l('Wirecard Pin'),
						'desc' => $this->l('Wirecard tarafından atanan pin'),
					),
					array(
						'col' => 4,
						'type' => 'radio',
						'prefix' => '<i class="icon icon-table"></i>',
						'name' => 'WIRECARD_INSTALLMENT',
						'label' => $this->l('Taksit'),
						'desc' => $this->l('Taksitli ödemeye izin verecek misiniz ? (Sadece Form ile Direkt Ödeme yönteminde çalışmaktadır.'),
						'values' => array(
							array(
								'id' => 'on',
								'value' => 'on',
								'label' => $this->l('Aktif')
							),
							array(
								'id' => 'off',
								'value' => 'off',
								'label' => $this->l('Aktif Değil')
							),
						)
					),
					array(
						'type' => 'radio',
						'name' => 'WIRECARD_MODE',
						'is_bool' => false,
						'label' => $this->l('Ödeme Yöntemi'),
						'desc' => $this->l('Ödeme Yönteminiz?'),
						'values' => array(
							array(
								'id' => 'shared3d',
								'value' => 'shared3d',
								'label' => $this->l('3D ile Ortak Ödeme Sayfası')//3D ile Ortak Ödeme Sayfası
							),
							array(
								'id' => 'shared',
								'value' => 'shared',
								'label' => $this->l('Ortak Ödeme Sayfası') //Ortak Ödeme Sayfası
							),
							array(
								'id' => 'form',
								'value' => 'form',
								'label' => $this->l('Form ile Direkt Ödeme')//Form ile Direkt Ödeme
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Kaydet'),
				),
			),
		);
	}

	protected function getConfigFormValues()
	{
		return array(
			'WIRECARD_ENABLED' => Configuration::get('WIRECARD_ENABLED'),
			'WIRECARD_USER_CODE' => Configuration::get('WIRECARD_USER_CODE'),
			'WIRECARD_PIN' => Configuration::get('WIRECARD_PIN'),			
			'WIRECARD_INSTALLMENT' => Configuration::get('WIRECARD_INSTALLMENT'),
			'WIRECARD_MODE' => Configuration::get('WIRECARD_MODE'),			
		);
	}

	protected function postProcess()
	{
		$form_values = $this->getConfigFormValues();
		foreach (array_keys($form_values) as $key) {
			Configuration::updateValue($key, Tools::getValue($key));
		}
	}

	public function hookBackOfficeHeader()
	{
		if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
			$this->context->controller->addJS($this->_path . 'views/js/back.js');
			$this->context->controller->addCSS($this->_path . 'views/css/back.css');
		}
	}

	public function hookHeader()
	{
		$this->context->controller->addJS($this->_path . '/views/js/front.js');
		$this->context->controller->addCSS($this->_path . '/views/css/front.css');
	}

	public function hookPayment()
	{
		$this->smarty->assign('module_dir', $this->_path);
		return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
	}

	public function hookPaymentReturn($params)
	{

		if ($this->active == false)
			return;

		$order = $params['objOrder'];

		if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR'))
			$this->smarty->assign('status', 'ok');

		$this->smarty->assign(array(
			'id_order' => $order->id,
			'reference' => $order->reference,
			'params' => $params,
			'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
		));

		return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
	}

	public function hookActionOrderDetail($params)
	{
		$order_id = $params['order']->id;
		if (!$record = $this->getRecordByOrderId($order_id))
			return;
		$this->smarty->assign('record', $record);
		echo $this->display(__FILE__, 'views/templates/hook/customer_order_detail.tpl');
		return;
	}

	public function hookActionPaymentConfirmation()
	{
		return;
	}

	public function hookDisplayAdminOrderContentOrder()
	{
		$order_id = Tools::getValue('order_id');
		if (!$record = $this->getRecordByOrderId($order_id))
			return;
		$this->smarty->assign('record', $record);
		return $this->display(__FILE__, 'views/templates/hook/admin_order_detail.tpl');
	}

	public function hookDisplayPayment()
	{
		return $this->hookPayment();
	}

	public function hookDisplayPaymentReturn($params)
	{
		return $this->hookPaymentReturn($params);
	}

	/**
	 * Generates secure key
	 */
	public function createSecret($key)
	{
		return sha1('Wirecard' . $key);
	}

	function postToWirecard()
	{
		include_once(dirname(__FILE__) . '/class/restHttpCaller.php');
		include_once(dirname(__FILE__) . '/class/CCProxySaleRequest.php');
		include_once(dirname(__FILE__) . '/class/WDTicketPaymentFormRequest.php');
		include_once(dirname(__FILE__) . '/class/BaseModel.php');
		include_once(dirname(__FILE__) . '/class/helper.php');
	
		global $cart, $link;
		$prices = 0;
		$ins =(int)(Tools::getValue('wirecard-installment-count') == is_null ? 0 : Tools::getValue('wirecard-installment-count'));	 
	
		$wirecard_usercode = Configuration::get("WIRECARD_USER_CODE");
		$wirecard_pin = Configuration::get("WIRECARD_PIN");
		$wirecard_mode = Configuration::get("WIRECARD_MODE");
		$expire_date = explode('/', Tools::getValue('cc_expiry'));
		$customer = new Customer($cart->id_customer);

		$total_cart = $cart->getOrderTotal(true, Cart::BOTH);
		$order_sum = $cart->getSummaryDetails();

		 $amount = 	$total_cart;
		 $installment = $ins;
		 $order_id =  $cart->id ;

		 $record = array(
			'order_id' => $order_id,
			'customer_id' => $cart->id_customer,
			'wirecard_id' => $order_id,
			'amount' => $amount,
			'amount_paid' => $amount,
			'installment' => $installment,
			'cardholdername' => Tools::getValue('cc_name'),
			'cardexpdate' => str_replace(' ', '', $expire_date[0]) . str_replace(' ', '', $expire_date[1]),
			'cardnumber' => str_replace(' ', '', Tools::getValue('cc_number')), //Maskeli yazılacak
			'createddate' =>date("Y-m-d h:i:s"), 
			'ipaddress' =>  helper::get_client_ip(),
			'status_code' => 1, //default başarısız
			'result_code' => '', 
			'result_message' => '',
			'mode' =>  $wirecard_mode,
			'shared_payment_url' => 'null'
		);
		
		if ($wirecard_mode == 'form')
			{			
				$request = new CCProxySaleRequest();
				$request->ServiceType = "CCProxy";
				$request->OperationType = "Sale";
				$request->Token= new Token();
				$request->Token->UserCode=Configuration::get("WIRECARD_USER_CODE");
				$request->Token->Pin=Configuration::get("WIRECARD_PIN");
				$request->MPAY = $order_id;
				$request->IPAddress = helper::get_client_ip();  
				$request->PaymentContent = "Odeme"; //Ürünisimleri
				$request->InstallmentCount = $installment;
				$request->Description = "";
				$request->ExtraParam = "";
				$request->CreditCardInfo= new CreditCardInfo();
				$request->CreditCardInfo->CreditCardNo= str_replace(' ', '', Tools::getValue('cc_number'));
				$request->CreditCardInfo->OwnerName= Tools::getValue('cc_name');
				$request->CreditCardInfo->ExpireYear=str_replace(' ', '', $expire_date[1]);
				$request->CreditCardInfo->ExpireMonth = str_replace(' ', '', $expire_date[0]);
				$request->CreditCardInfo->Cvv= Tools::getValue('cc_cvc');
				$request->CreditCardInfo->Price=$amount * 100;  // 1 TL için 100 Gönderilmeli.		

				try {				
					$response = CCProxySaleRequest::execute($request); 					
				} catch (Exception $e) {
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}

				$sxml = new SimpleXMLElement( $response);

				$record['status_code'] = $sxml->Item[0]['Value'];
				$record['result_code'] = $sxml->Item[1]['Value'];
				$record['result_message'] = helper::turkishreplace( $sxml->Item[2]['Value']);
				$record['wirecard_id'] =   $sxml->Item[3]['Value'];
				$record['cardnumber'] =    $sxml->Item[5]['Value'];
				$record['amount_paid'] = (string) $response['StatusCode'] == "0" ? $amount : 0;
			
				return $record;

			}
			elseif ($wirecard_mode =='shared3d') //shared 3d ortak ödeme sayfası 3d 
			{	
				$request = new WDTicketPaymentFormRequest();
				$request->ServiceType = "WDTicket";
				$request->OperationType = "Sale3DSURLProxy";
				$request->Token= new Token();
				$request->Token->UserCode=Configuration::get("WIRECARD_USER_CODE");
				$request->Token->Pin=Configuration::get("WIRECARD_PIN");
				$request->MPAY = $order_id;
				$request->IPAddress = helper::get_client_ip();  
				$request->PaymentContent = "Odeme"; //Ürünisimleri
				$request->PaymentTypeId = "1";
				$request->Description = "";
				$request->ExtraParam = "";
				$request->ErrorURL = $this->context->link->getModuleLink('wirecard', 'redirect');
				$request->SuccessURL =$this->context->link->getModuleLink('wirecard', 'redirect');
				$request->Price = $amount * 100;  // 1 TL için 100 Gönderilmeli.

				try {		
					$response = WDTicketPaymentFormRequest::Execute($request); 
				
				} catch (Exception $e) {	
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement( $response);
			
				$record['status_code'] = $sxml->Item[0]['Value'];
				$record['result_code'] = $sxml->Item[1]['Value'];
				$record['result_message'] = helper::turkishreplace( $sxml->Item[2]['Value']);
				$record['wirecard_id'] =   $sxml->Item[3]['Value'];
				$record['shared_payment_url'] =$sxml->Item[3]['Value'];
				return $record;

			}
			else 
			{ //shared
				$request = new WDTicketPaymentFormRequest();
				$request->ServiceType = "WDTicket";
				$request->OperationType = "SaleURLProxy";
				$request->Token= new Token();
				$request->Token->UserCode=Configuration::get("WIRECARD_USER_CODE");
				$request->Token->Pin=Configuration::get("WIRECARD_PIN");
				$request->MPAY = $order_id;
				$request->PaymentContent = "Odeme"; //Ürünisimleri
				$request->PaymentTypeId = "1";
				$request->Description = "";
				$request->ExtraParam = "";
				$request->ErrorURL = $this->context->link->getModuleLink('wirecard', 'redirect');
				$request->SuccessURL =$this->context->link->getModuleLink('wirecard', 'redirect');
				$request->Price = $amount * 100;  // 1 TL için 100 Gönderilmeli.

				try {				
					$response = WDTicketPaymentFormRequest::Execute($request); 
				
				} catch (Exception $e) {
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement( $response);
			
				$record['status_code'] = $sxml->Item[0]['Value'];
				$record['result_code'] = $sxml->Item[1]['Value'];
				$record['result_message'] = helper::turkishreplace( $sxml->Item[2]['Value']);
				$record['wirecard_id'] =   $sxml->Item[3]['Value'];
				$record['shared_payment_url'] =$sxml->Item[3]['Value'];

				return $record;
			}
	}

	private function addRecord($record)
	{
		Db::getInstance()->insert( 'wirecard_payment', $record);
	}

	private function updateRecordByOrderId($record)
	{
		Db::getInstance()
				->update( 'wirecard_payment', $record, 'order_id = ' . (int) $record['order_id'], 1);
	}

	public function saveRecord($record)
	{
		if (isset($record['order_id'])
				AND $record['order_id']
				AND $this->getRecordByOrderId($record['order_id']))
			return $this->updateRecordByOrderId($record);

		return $this->addRecord($record);
	}

	public function getRecordByOrderId($order_id)
	{
		return Db::getInstance()
						->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wirecard_payment` '
								. 'WHERE `order_id` = ' . (int) $order_id);
	}
}
