<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class CommandesStatutController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'order_statut';
		protected $table_view = 'commandes_statut';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_ORDERS_.'OrderStateModels');
			
			$this->models = new CommandesStatutModels();
			
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
				$objet = new OrderStateModels($_GET['o']);
				$objet = get_object_vars($objet);
			}
			
			$this->assign = array(
				'admin_menu'   => true,
				'title'        => 'Méthode de Paiement',
				'table'        => $this->table_view,
				'view'         => $this->view(),
				'objet'        => !empty($objet['id_order_state']) ? $objet : false,
				'action'       => !empty($_GET['action']) ? $_GET['action'] : false,
			);
		}
		
		public function initContent(){
			
			$nb = $this->models->countOrderState();
			$payment = $this->models->getOrderState($this->limit, $this->pagination);
			
			$this->assign['order_state'] = $payment;
			$this->assign['nb_state'] = $nb;
			
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
			
			$search = $this->models->getOrderState($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countOrderState($sql);
			
			$this->assign['order_state'] = $search;
			$this->assign['nb_state'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$state = $this->StateProcess();
			
			if(!empty($state->id_order_state)){
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$state = $this->StateProcess(true);
			
			if(!empty($state->id_order_state)){
				return true;
			}
			
			return false;
		}
		
		public function StateProcess($upd = false){
			
			$id_state = (isset($_POST['id_order_state'])) ? (int)$_POST['id_order_state'] : false;
			
			$obj = new OrderStateModels($id_state);
			
			$obj->name        = $this->escape($_POST['name']);
			$obj->background  = $this->escape($_POST['background']);
			$obj->color       = $this->escape($_POST['color']);
			$obj->delivery    = isset($_POST['delivery']) ? 1 : 0;
			$obj->invoice     = isset($_POST['invoice']) ? 1 : 0;
			$obj->credit_note = isset($_POST['credit_note']) ? 1 : 0;
			$obj->date_add    = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd    = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProcess($id){
			
			if(!empty((int)$id)) {
				$obj = new OrderStateModels((int)$id);
				
				if(!empty($obj->id_order_state)){
					return $obj->delete();
				}
			}
			
			return false;
		}
	}