<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	
	require_once './core/html2pdf/vendor/autoload.php';
	use Spipu\Html2Pdf\Html2Pdf;
	
	class CommandesController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'commandes';
		protected $email;
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_ORDERS_.'OrdersModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrdersDetailsModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderCarrierModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderHistoryModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderTaxModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderMessageModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderPaymentModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderStateModels');
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderAddressModels');
			Autoloader::register(_DIR_ORM_CART_.'CartModels');
			Autoloader::register(_DIR_ORM_CART_.'CartDeliveryModels');
			Autoloader::register(_DIR_ORM_CART_.'CartProductModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomersModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'AddressModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'PaymentAccessModels');
			Autoloader::register(_DIR_ORM_.'EmployeeModels');
			
			$this->models = new CommandesModels();
			
			parent::__construct(true);
			
			if(isset($_POST['function'])) {
				$this->{'ajax'.$_POST['function']}($_POST['data']);
			} else{
				$this->content();
			}
		}
		
		public function content ()
		{
			$this->template();
			$this->initPagination();
			$this->initContent();
			$this->postProcess();
			parent::content();
		}
		
		public function template(){
			
			if(!empty($_GET['o'])){
				$objet = $this->BuildArray('OrdersModels', (int)$_GET['o']);
				$objet_order   = $this->getObjetByIdOrder('Orders', $objet['id_order']);
				$objet_product = $this->getObjetByIdOrder('OrdersDetails', $objet['id_order']);
				$objet_tax     = $this->getObjetByIdOrder('OrderTax', $objet['id_order']);
				$objet_carrier = $this->getObjetByIdOrder('OrderCarrier', $objet['id_order']);
				$objet_address = $this->getObjetByIdOrder('OrderAddress', $objet['id_order']);
				$objet_history = $this->getObjetByIdOrder('OrderHistory', $objet['id_order']);
				$objet_message = $this->getObjetByIdOrder('OrderMessage', $objet['id_order']);
				
				$obj_detail = new OrdersDetailsModels();
				$total_ecotax = $obj_detail->getTotalEcotax($objet['id_order']);
				$total_product_delivered = $obj_detail->getTotalProductDelivered($objet['id_order']);
				$total_product_refunded  = $obj_detail->getTotalProductRefunded($objet['id_order']);
			}
			
			$payment = new OrderPaymentModels;
			$state = new OrderStateModels;
			
			$this->assign = array(
				'admin_menu'     => true,
				'datepicker'     => true,
				'title'          => 'Commandes',
				'table'          => $this->table,
				'view'           => $this->view(),
				'css'      	     => 'order',
				'js'             => 'order',
				'list_payment'   => $payment->getPayments(),
				'list_statut'    => $state->getStates(),
				'action'         => $_GET['action'] ?? false,
				'objet'          => !empty($objet['id_order']) ? $objet : false,
				'objet_order'    => $objet_order ?? false,
				'objet_product'  => $objet_product ?? false,
				'objet_tax'      => $objet_tax ?? false,
				'objet_carrier'  => $objet_carrier ?? false,
				'objet_address'  => $objet_address ?? false,
				'objet_history'  => $objet_history ?? false,
				'objet_message'  => $objet_message ?? false,
				'total_ecotax'   => $total_ecotax ?? false,
				'total_product_delivered' => $total_product_delivered ?? false,
				'total_product_refunded'  => $total_product_refunded ?? false,
			);
		}
		
		public function BuildArray($objet, $params = false){
			$objet = new $objet($params);
			return get_object_vars($objet);
		}
		
		public function getObjetByIdOrder($objet, $params){
			$class = $objet.'Models';
			$obj = new $class();
			return $obj->{'get'.$objet.'ByIdOrder'}($params);
		}
		
		public function view($table = false)
		{
			$view = parent::view($table);
			
			if(!empty($_GET['action']) && $_GET['action'] == 'view'){
				$view = 'admin/commandes/AdminCommandesViewsOrder.twig';
			}
			
			return $view;
		}
		
		public function initContent($clause = false){
			
			$nb = $this->models->countOrders($clause);
			$orders = $this->models->getOrders($this->limit, $this->pagination, $clause);
			
			$this->assign['nb_orders'] = $nb;
			$this->assign['orders'] = $orders;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			
			if (!empty($_POST['statut_commandes'])){
				$this->postProcessOrderState();
			}
			
			if(!empty($_POST['message_commandes'])){
				$this->postProcessOrderMessage();
			}
			
			if(!empty($_GET['pdf'])){
				$this->postProcessDocument();
			}
			
			parent::postProcess($params_url);
		}
		
		public function renderPdf($twig_file, $assign, $type){
			ob_start();
			$template = Render::renderTpl('pdf/'.$twig_file);
			echo $template->render($assign);
			$content = ob_get_contents();
			ob_end_clean();
			
			$facture = new Html2Pdf('P', 'A4');
			$facture->writeHTML($content);
			$facture->output($assign[$type]['number'].'.pdf', 'D');
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				
				if($key == 'name_customer'){
					$key = 'c.name';
				}
				
				if(in_array($key, array('date_from','date_to'))) {
					$date = date('Y-m-d', strtotime($value));
					
					$operateur = ($key == 'date_from') ? '>=' : '<=';
					$heure = ($key == 'date_from') ? ' 00:00:00' : ' 23:59:59';
					
					$sql .= ' AND o.date_add '.$operateur.' "'.$date.$heure.'"';
				} else{
					$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
				}
			}
			
			$this->initContent($sql);
		}
		
		public function postProcessOrderState(){
			$order = new OrdersModels((int)$_POST['id_order']);
			$order->current_state = $_POST['id_order_state'];
			$order->upd();
			
			if(!empty($order->id_order)) {
				$this->orderHistoryProcess($order, $order->current_state);
				header('Location: '.$this->table.'&action=view&o='.$order->id_order);
			}
		}
		
		public function postProcessOrderMessage(){
			$order = new OrdersModels((int)$_POST['id_order']);
			
			if(!empty($order->id_order)) {
				$this->orderMessageProcess($order, 'message');
				header('Location: '.$this->table.'&action=view&o='.$order->id_order.'&p='.(int)$_POST['page'].'#echange');
			}
		}
		
		public function postProcessDocument(){
			$assign = array(
				'logo'    => './assets/img/favicon/android-launchericon-192-192.png',
				'societe' => array(
					'name'         => 'Yoky',
					'address'      => '10 rue gabriel vicaire',
					'postcode'     => '01000',
					'city'         => 'BOURG-EN-BRESSE',
					'website'      => 'www.yoky.fr',
					'phone'        => '06 52 81 43 11',
					'email'        => 'comptabilite@yoky.fr',
					'mentions'     => 'RCS : Bourg-en-Bresse - SIREN : 123 456 789 - APE : 1234A - TVA : FR 94 123 456 789',
					'capital'      => 'S.A.S au Capital de 15 000 Euros',
				),
				'data' => $this->assign
			);
			
			switch ($_GET['pdf']){
				case 'invoice' :
					
					$assign['invoice'] = array(
						'number' => 'FC1402',
						'date' => '25/09/2019',
					);
					
					$this->renderPdf('invoice.twig', $assign, 'invoice');
					
					break;
				
				case 'delivery' :
					
					$assign['delivery'] = array(
						'number' => $this->assign['objet']['reference'],
						'date' => $this->assign['objet']['date_add'],
					);
					
					$this->renderPdf('delivery.twig', $assign, 'delivery');
					
					break;
			}
		}
		
		public function insertProcess(){
			
			$order = $this->orderProcess();
			
			if(!empty($order->id_order)){
				$this->orderAddressProcess($order, 'Livraison', 'delivery');
				$this->orderAddressProcess($order, 'Facturation', 'invoice');
				
				$this->orderProductProcess($order);
				$this->orderCarrierProcess($order);
				$this->orderHistoryProcess($order, 1);
				$this->orderTaxProcess($order);
				$this->orderMessageProcess($order, 'order_message');
				
				return true;
			}
			
			return false;
		}
		
		public function orderProcess(){
			if(!empty((int)$_POST['id_cart'])){
				$cart = new CartModels((int)$_POST['id_cart']);
				$cart_product = new CartProductModels();
				
				$order = new OrdersModels();
				$order->reference     = $order->setReference();
				$order->id_cart       = $cart->id_cart;
				$order->id_customer   = $cart->id_customer;
				$order->id_payment    = (int)$_POST['id_payment'];
				$order->current_state = (int)$_POST['current_state'];
				$order->total_product = $this->totalProductExcl($cart);
				$order->total_excl    = $this->totalTaxExcl($order->total_product, $cart->total_shipping_ht);
				$order->total_incl    = $this->totalTaxIncl($order->total_excl, $cart, $cart_product);
				$order->date_add      = $this->date;
				$order->date_upd      = $this->date;
				$order->add();
				
				$cart->used = 1;
				$cart->upd();
				
				return $order;
			}
			
			return false;
		}
		
		public function orderAddressProcess($order, $alias, $id){
			
			$cart = new CartModels($order->id_cart);
			$address = new AddressModels((int)$cart->{'id_address_'.$id});
			
			$obj = new OrderAddressModels();
			
			$obj->id_order = $order->id_order;
			$obj->alias    = $alias;
			$obj->lastname = $address->lastname;
			$obj->voie     = $address->voie;
			$obj->complement_voie = $address->complement_voie;
			$obj->postcode = $address->postcode;
			$obj->ville    = $address->ville;
			$obj->phone    = $address->phone;
			$obj->date_add = $this->date;
			$obj->add();
		}
		
		public function orderProductProcess($order){
	
			$cart_product = new CartProductModels();
			$products = $cart_product->getProductInCart($order->id_cart);
			
			if(isset($products)) {
				foreach ($products as $product) {
					$obj = new OrdersDetailsModels();
					$obj->id_order            = $order->id_order;
					$obj->id_product          = $product->id_product;
					$obj->product_reference   = $product->reference;
					$obj->product_name        = $product->name;
					$obj->product_ean         = $product->ean13;
					$obj->product_quantity    = $product->quantity;
					$obj->quantity_delivered  = 0;
					$obj->quantity_refunded   = 0;
					$obj->product_price_excl  = $product->price_excl;
					$obj->product_price_incl  = $product->price_incl;
					$obj->product_ecotax_excl = $product->ecotax_excl;
					$obj->product_ecotax_incl = $product->ecotax_incl;
					$obj->unit_price_excl     = $product->unit_price_excl;
					$obj->unit_price_incl     = $product->unit_price_incl;
					$obj->product_weight      = $product->weight;
					$obj->date_add            = $this->date;
					$obj->date_upd            = $this->date;
					$obj->add();
				}
			}
		}
		
		public function orderCarrierProcess($order){
			
			$cart = new CartModels($order->id_cart);
			
			$carrier = new OrderCarrierModels();
			$carrier->id_order      = $order->id_order;
			$carrier->id_carrier    = $cart->id_carrier;
			$carrier->weight        = $cart->weight;
			$carrier->shipping_excl = $cart->total_shipping_ht;
			$carrier->shipping_incl = $cart->total_shipping_ht + $cart->taxShippingCart();
			$carrier->date_add      = $this->date;
			$carrier->date_upd      = $this->date;
			$carrier->add();
		}
		
		public function orderHistoryProcess($order, $id){
			$obj = new EmployeeModels();
			$employee = $obj->getEmployeeByToken($_SESSION['employee']['token']);
			
			$history = new OrderHistoryModels();
			$history->id_order       = $order->id_order;
			$history->id_employee    = $employee->id_employees;
			$history->id_order_state = $id;
			$history->date_add       = $this->date;
			$history->add();
		}
		
		public function orderTaxProcess($order){
			
			$cart = new CartModels($order->id_cart);
			$cart_product = new CartProductModels();
			$products = $cart_product->taxProductCartDetails($order->id_cart);
			$tax_shipping = $cart->taxShippingCartDetails();
			
			if(!empty($products)) {
				foreach ($products as $product) {
					$tax = new OrderTaxModels();
					$tax->id_order  = $order->id_order;
					$tax->tax_name  = $product->tax_name;
					$tax->tax_value = $product->tax_value;
					$tax->amount    = $product->amount;
					$tax->date_add  = $this->date;
					$tax->add();
				}
			}
			
			if(isset($tax_shipping)){
				$obj = new OrderTaxModels();
				$id = $obj->searchIdOrderTax($order->id_order, $tax_shipping);
				
				if(!empty($id)){
					$obj = new OrderTaxModels($id);
					$obj->amount   += $tax_shipping->amount;
					$obj->date_add = $this->date;
					$obj->upd();
				} else{
					$obj->id_order  = $order->id_order;
					$obj->tax_name  = $tax_shipping->tax_name;
					$obj->tax_value = $tax_shipping->tax_value;
					$obj->amount    = $tax_shipping->amount;
					$obj->date_add  = $this->date;
					$obj->add();
				}
			}
		}
		
		public function orderMessageProcess($order, $msg){
			
			if(!empty($_POST[$msg])) {
				$obj = new EmployeeModels();
				$employee = $obj->getEmployeeByToken($_SESSION['employee']['token']);
				
				$message = new OrderMessageModels();
				$message->id_order = $order->id_order;
				$message->id_employee = $employee->id_employees;
				$message->private = 0;
				$message->message = $this->cut($this->escape($_POST[$msg]), 0, 255);
				$message->date_add = $this->date;
				$message->date_upd = $this->date;
				$message->add();
			}
		}
		
		public function deleteProcess($id){
			
			if(!empty($id)) {
				$obj = new OrdersModels($id);
				
				if(!empty($obj->id_order)){
					return $obj->delete();
				}
			}
			
			return false;
		}
		
		public function ajaxUpdateTrackingNumber($data){
			$tracking = false;
			$form = $this->unserializeForm($data);
			
			if(!empty($form['id_order'])){
				$carrier = new OrderCarrierModels(false, (int)$form['id_order']);
				$carrier->tracking_number = $this->escape($form['tracking_number']);
				$carrier->upd();
				$tracking = $carrier->tracking_number;
			}
			
			echo json_encode(array('tracking_number' => $tracking));
		}
		
		public function ajaxSearchClient($query){
			$result = $this->models->searchClient($this->escape($query));
			echo json_encode(array('client' => $result));
		}
		
		public function ajaxSearchAddressCustomer($id_customer){
			$address = new AddressModels();
			
			$id_invoice = $address->getAddressByAlias($id_customer, 'Facturation');
			$id_delivery = $address->getAddressByAlias($id_customer, 'Livraison');
			
			$invoice = new AddressModels($id_invoice);
			$delivery = new AddressModels($id_delivery);
			
			echo json_encode(array('address' => array('invoice' => get_object_vars($invoice),
													  'delivery' => get_object_vars($delivery))));
		}
		
		public function ajaxGetAddressCart($data){
			$address = false;
			$action = (string)$data['action'];
			$id_cart = (int)$data['id_cart'];
			
			if(!empty($id_cart)) {
				$cart = new CartModels($id_cart);
				
				if(!empty($cart->{'id_address_'.$action})){
					if($action === 'delivery' && !empty($cart->id_cart_delivery)){
						$address = new CartDeliveryModels((int)$cart->id_cart_delivery);
					} else{
						$address = new AddressModels((int)$cart->{'id_address_'.$action});
					}
				}
			}
			
			echo json_encode(array('address' => $address));
		}
		
		public function ajaxUpdateAddress($data){
			$address = false;
			$cart = false;
			$id_cart = (int)$data['id_cart'];
			$form = $this->unserializeForm($data['form']);
			
			if(!empty($id_cart)) {
				$cart = new CartModels($id_cart);
				
				if(empty($cart->{'id_address_'.$data['action']})){
					$address = $this->addAddressAjaxProcess($cart->id_customer, $form);
					
					$cart->{'id_address_'.$data['action']} = $address->id_address;
					$cart->upd();
				} else{
					$address = $this->{$data['action'].'AddressAjaxProcess'}($cart, $form);
				}
			}
			
			echo json_encode(array('address' => $address, 'cart' => $cart));
		}
		
		public function addAddressAjaxProcess($id_customer, $form, $id_address = false){
			
			$obj = new AddressModels($id_address);
			
			$obj->id_customer     = $id_customer;
			$obj->alias           = $this->escape($form['alias']);
			$obj->lastname        = $this->escape($form['lastname']);
			$obj->voie            = $this->escape($form['voie']);
			$obj->complement_voie = $this->escape($form['complement_voie']);
			$obj->postcode        = $form['postcode'];
			$obj->ville           = $this->escape($form['ville']);
			$obj->phone           = $this->escape($form['phone']);
			$obj->date_add        = !empty($id_address) ? $obj->date_add : $this->date;
			$obj->date_upd        = $this->date;
			
			if(!empty($id_address)){
				$obj->upd();
			} else{
				$obj->add();
			}
			
			return $obj;
		}
		
		public function deliveryAddressAjaxProcess($cart, $form){
			
			$id_delivery = !empty($cart->id_cart_delivery) ? $cart->id_cart_delivery : false;
			
			$obj = new CartDeliveryModels($id_delivery);
			
			$obj->id_cart         = $cart->id_cart;
			$obj->id_customer     = $cart->id_customer;
			$obj->alias           = $this->escape($form['alias']);
			$obj->lastname        = $this->escape($form['lastname']);
			$obj->voie            = $this->escape($form['voie']);
			$obj->complement_voie = $this->escape($form['complement_voie']);
			$obj->postcode        = $form['postcode'];
			$obj->ville           = $this->escape($form['ville']);
			$obj->phone           = $this->escape($form['phone']);
			$obj->date_add        = !empty($id_delivery) ? $obj->date_add : $this->date;
			$obj->date_upd        = $this->date;
			
			if(!empty($id_delivery)){
				$obj->upd();
			} else{
				$obj->add();
				
				$cart->id_cart_delivery = $obj->id_cart_delivery;
				$cart->upd();
			}
			
			return $obj;
		}
		
		public function invoiceAddressAjaxProcess($cart, $form){
			return $this->addAddressAjaxProcess($cart->id_customer, $form, $cart->id_address_invoice);
		}
		
		public function ajaxSearchCarrier($id_cart){
			
			$cart = new CartModels((int)$id_cart);
			$carrier = false;
			
			if(!empty($cart->id_customer)){
				$customer = new CustomersModels($cart->id_customer);
				
				$carrier = $cart->searchAvailableCarrier($customer->id_group, $cart);
			}
			
			echo json_encode(array('carrier' => $carrier));
		}
		
		public function ajaxUpdateIdCarrier($tabs){
			
			$cart = new CartModels((int)$tabs['id_cart']);
			$cart->id_carrier = (int)$tabs['id_carrier'];
			$cart->total_shipping_ht = $cart->priceCarrier($cart);
			$cart->date_upd = $this->date;
			$cart->upd();
			
			echo json_encode(array('id_cart' => $cart->id_cart));
		}
		
		public function ajaxSearchProduct($query){
			$result = $this->models->searchProduct($this->escape($query));
			echo json_encode(array('product' => $result));
		}
		
		public function ajaxAddCart($id_customer){
			$cart = new CartModels();
			$address = new AddressModels();
			
			$id_invoice = $address->getAddressByAlias($id_customer, 'Facturation');
			$id_delivery = $address->getAddressByAlias($id_customer, 'Livraison');
			
			$cart->used  = 0;
			$cart->id_carrier  = 0;
			$cart->id_customer = $id_customer;
			$cart->id_address_delivery = ($id_delivery) ? $id_delivery : $id_invoice;
			$cart->id_address_invoice  = $id_invoice;
			$cart->total_ht = 0;
			$cart->total_ecotax = 0;
			$cart->total_shipping_ht = 0;
			$cart->weight   = 0;
			$cart->date_add = $this->date;
			$cart->date_upd = $this->date;
			$cart->add();
			
			echo json_encode(array('id_cart' => (int)$cart->id_cart));
		}
		
		public function ajaxDeleteCart($id_cart){
			
			$cart = new CartModels($id_cart);
			
			$delete = false;
			
			if(isset($cart->id_cart)){
				$cart->delete();
				$delete = true;
			}
			
			echo json_encode(array('delete' => $delete));
		}
		
		public function ajaxAddProductInCart($query){
			
			$id_cart    = (int)$this->escape($query['id_cart']);
			$id_product = (int)$this->escape($query['id_product']);
			$quantity   = (int)$this->escape($query['quantity']);
			$price      = $this->escape($query['price']);
			$reset      = (int)$this->escape($query['reset_qte']);
			
			$product = new CartProductModels();
			$cart = new CartModels($id_cart);
			$customer = new CustomersModels($cart->id_customer);
			$article = new ProductsModels($id_product);
			
			$cart_product = $product->searchIdCartProduct($id_cart, $id_product);
			$modulo = $quantity % $article->conditionnement;
			
			if($modulo > 0){
				$quantity = ($quantity - $modulo) + $article->conditionnement;
			}
			
			if($cart_product){
				$product = new CartProductModels($cart_product);
				
				if($reset === 0) {
					$product->quantity += $quantity;
				} else{
					$product->quantity = $quantity;
				}
				
				if($price != '' && $price > 0 && $price >= $article->prix_achat) {
					$product->price = (float)$price;
				}
				
				$product->date_upd  = $this->date;
				
				if($product->quantity > 0){
					$product->upd();
				} else{
					$product->delete();
				}
				
			} else {
				$product->id_cart    = $id_cart;
				$product->id_product = $id_product;
				$product->quantity   = $quantity;
				$product->price      = $product->productPrice($id_product, $article, $cart->id_customer, $customer->id_group);
				$product->date_add   = $this->date;
				$product->date_upd   = $this->date;
				
				if($product->quantity > 0) {
					$product->add();
				}
			}
			
			$cart_product = $product->totalProductCart($id_cart);
			
			$cart->total_ht     = $cart_product->total_ht;
			$cart->total_ecotax = $cart_product->total_ecotax;
			$cart->weight       = $cart_product->weight;
			$cart->upd();
			
			$result = $product->searchCartProduct($this->escape($query['id_cart']));
			
			echo json_encode(array('product' => $result, 'cart' => $cart));
		}
		
		public function ajaxSummary($id_cart){
			$cart = new CartModels((int)$id_cart);
			$cart_product = new CartProductModels();
			
			$total_product  = $this->totalProductExcl($cart);
			$total_shipping = $cart->total_shipping_ht;
			$total_ht  = $this->totalTaxExcl($total_product, $total_shipping);
			$total_ttc = $this->totalTaxIncl($total_ht, $cart, $cart_product);
			
			$summary = array(
				'product_ht'  => $total_product,
				'shipping_ht' => $total_shipping,
				'total_ht'    => $total_ht,
				'total_ttc'   => $total_ttc
			);
			
			echo json_encode(array('summary' => $summary));
		}
		
		public function ajaxSearchPayment($id_customer){
			
			$customer = new CustomersModels($id_customer);
			$payment  = new PaymentAccessModels;
			
			$payment_access = $payment->getAllowPaymentAccess($customer->id_payment);
			
			echo json_encode(array('payment_access' => $payment_access));
		}
		
		public function totalProductExcl($cart){
			return $cart->total_ht + $cart->total_ecotax;
		}
		
		public function totalTaxExcl($total_product, $total_shipping){
			return $total_product + $total_shipping;
		}
		
		public function totalTaxIncl($total_ht, $cart, $cart_product){
			$tax_shipping = $cart->taxShippingCart();
			$tax_product  = $cart_product->taxProductCart($cart->id_cart);
			
			return $total_ht + $tax_shipping + $tax_product;
		}
	}