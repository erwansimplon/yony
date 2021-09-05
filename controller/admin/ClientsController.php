<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class ClientsController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'clients';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomersModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGroupModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGenderModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerPaymentModels');
			
			$this->models = new ClientsModels();
			
			parent::__construct(true);
			$this->content();
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
				$objet = new CustomersModels($_GET['c']);
				$objet = get_object_vars($objet);
			}
			
			$this->assign = array(
				'admin_menu' => true,
				'title'      => ucfirst($this->table),
				'table'      => $this->table,
				'view'       => $this->view(),
				'action'     => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'      => !empty($objet['id_customer']) ? $objet : false,
			);
		}
		
		public function initContent(){
			
			$group = new CustomerGroupModels();
			$gender = new CustomerGenderModels();
			$payment = new CustomerPaymentModels();
			
			$nb = $this->models->countCustomers();
			$customers = $this->models->getCustomers($this->limit, $this->pagination);
			
			$this->assign['nb_customer']  = $nb;
			$this->assign['customers']    = $customers;
			$this->assign['list_group']   = $group->getAllCustomerGroup();
			$this->assign['list_gender']  = $gender->getAllCustomerGender();
			$this->assign['list_payment'] = $payment->getAllCustomerPayment();
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			parent::postProcess($params_url);
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				$sql .= ' AND c.'.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getCustomers($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countCustomers($sql);
			
			$this->assign['customers'] = $search;
			$this->assign['nb_customer'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$customer = $this->customerProcess();
			
			if(!empty($customer->id_customer)){
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$customer = $this->customerProcess(true);
			
			if(!empty($customer->id_customer)){
				return true;
			}
			
			return false;
		}
		
		public function customerProcess($upd = false){
			
			$id_customer = (isset($_POST['id_customer'])) ? (int)$_POST['id_customer'] : false;
			
			$obj = new CustomersModels($id_customer);
			
			$obj->id_group   = (int)$_POST['id_group'];
			$obj->id_payment = (int)$_POST['id_payment'];
			$obj->id_gender  = (int)$_POST['id_gender'];
			$obj->active     = isset($_POST['active']) ? 1 : 0;
			$obj->name       = $this->escape($_POST['name']);
			$obj->email      = $this->escape($_POST['email']);
			$obj->date_add   = ($upd) ? $this->date : $obj->date_add;
			$obj->date_upd   = $this->date;
			
			if(!empty($_POST['password'])) {
				$obj->password = $this->crypt($_POST['password']);
			}
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProcess($id){
			
			if(!empty($id)) {
				$obj = new CustomersModels((int)$id);
				
				if(!empty($obj->id_customer)){
					return $obj->delete();
				}
			}
			
			return false;
		}
	}