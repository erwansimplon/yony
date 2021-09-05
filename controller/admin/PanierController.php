<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	
	class PanierController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'panier';
		
		public function __construct()
		{
			
			Autoloader::register(_DIR_ORM_CART_.'CartModels');
			Autoloader::register(_DIR_ORM_CART_.'CartDeliveryModels');
			Autoloader::register(_DIR_ORM_CART_.'CartProductModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsModels');
			
			$this->models = new PanierModels();
			
			parent::__construct(true);
			
			if (isset($_POST['function'])) {
				$this->{'ajax' . $_POST['function']}($_POST['data']);
			} else {
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
			
			if(!empty($_GET['c'])){
				$objet = new CartModels((int)$_GET['c']);
				$objet = get_object_vars($objet);
				$objet_cart   = $this->getObjetByIdOrder('Cart', $objet['id_cart']);
				$objet_product = $this->getObjetByIdOrder('CartProduct', $objet['id_cart']);
			}
			
			$this->assign = array(
				'admin_menu'    => true,
				'datepicker'    => true,
				'title'         => 'Panier',
				'table'         => $this->table,
				'view'          => $this->view(),
				'action'        => $_GET['action'] ?? false,
				'objet'         => !empty($objet['id_cart']) ? $objet : false,
				'objet_cart'    => $objet_cart ?? false,
				'objet_product' => $objet_product ?? false,
			);
		}
		
		public function getObjetByIdOrder($objet, $params){
			$class = $objet.'Models';
			$obj = new $class();
			return $obj->{'get'.$objet.'ByIdCart'}($params);
		}
		
		public function view($table = false)
		{
			$view = parent::view($table);
			
			if(!empty($_GET['action']) && $_GET['action'] == 'view'){
				$view = 'admin/panier/AdminPanierViewsCart.twig';
			}
			
			return $view;
		}
		
		public function initContent($clause = false){
			
			$nb = $this->models->countCarts($clause);
			$carts = $this->models->getCarts($this->limit, $this->pagination, $clause);
			
			$this->assign['nb_carts'] = $nb;
			$this->assign['carts'] = $carts;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			parent::postProcess($params_url);
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				
				if($key == 'name_customer'){
					$key = 'cli.name';
				} elseif($key == 'name_carrier'){
					$key = 'trans.name';
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
		
		public function deleteProcess($id){
			
			if(!empty($id)) {
				$obj = new CartModels($id);
				
				if(!empty($obj->id_cart)){
					$delivery = new CartDeliveryModels();
					$delivery->deleteByCart($obj->id_cart);
					
					$product = new CartProductModels();
					$product->deleteByCart($obj->id_cart);
					
					return $obj->delete();
				}
			}
			
			return false;
		}
	}