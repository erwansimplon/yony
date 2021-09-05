<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class CommandesPaiementController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'order_payment';
		protected $table_view = 'commandes_paiement';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderPaymentModels');
			
			$this->models = new CommandesPaiementModels();
			
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
				$objet = new OrderPaymentModels($_GET['o']);
				$objet = get_object_vars($objet);
			}
			
			$mode_affichage = array(
				'1' => 'Client',
				'2' => 'Global'
			);
			
			$this->assign = array(
				'admin_menu'   => true,
				'tree'         => true,
				'title'        => 'Méthode de Paiement',
				'table'        => $this->table_view,
				'view'         => $this->view(),
				'affichage'    => $mode_affichage,
				'objet'        => !empty($objet['id_order_payment']) ? $objet : false,
				'action'       => !empty($_GET['action']) ? $_GET['action'] : false,
			);
		}
		
		public function initContent(){
			
			$nb = $this->models->countOrderPayment();
			$payment = $this->models->getOrderPayment($this->limit, $this->pagination);
			
			$this->assign['order_payment'] = $payment;
			$this->assign['nb_payment'] = $nb;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			$this->table = $this->table_view;
			parent::postProcess($params_url);
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getOrderPayment($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countOrderPayment($sql);
			
			$this->assign['order_payment'] = $search;
			$this->assign['nb_payment'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$payment = $this->PaymentProcess();
			
			if(!empty($payment->id_order_payment)){
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$payment = $this->PaymentProcess(true);
			
			if(!empty($payment->id_order_payment)){
				return true;
			}
			
			return false;
		}
		
		public function PaymentProcess($upd = false){
			
			$id_payment = (isset($_POST['id_order_payment'])) ? (int)$_POST['id_order_payment'] : false;
			
			$obj = new OrderPaymentModels($id_payment);
			
			$obj->name      = $this->escape($_POST['name']);
			$obj->affichage = (int)$_POST['affichage'];
			$obj->date_add  = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd  = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProcess($id){
			
			if(!empty((int)$id)) {
				$obj = new OrderPaymentModels((int)$id);
				
				if(!empty($obj->id_order_payment)){
					return $obj->delete();
				}
			}
			
			return false;
		}
	}