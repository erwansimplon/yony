<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class AdressesController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'adresses';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'AddressModels');
			
			$this->models = new AdressesModels();
			
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
			
			if(!empty($_GET['a'])){
				$objet = new AddressModels($_GET['a']);
				$objet = get_object_vars($objet);
			}
			
			$this->assign = array(
				'admin_menu' => true,
				'title'      => 'Adresse',
				'table'      => $this->table,
				'view'       => $this->view(),
				'css'      	 => 'address',
				'js'         => 'address',
				'action'     => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'      => !empty($objet['id_address']) ? $objet : false,
			);
		}
		
		public function initContent(){
			
			$nb = $this->models->countAddress();
			$address = $this->models->getAddress($this->limit, $this->pagination);
			
			$this->assign['nb_address'] = $nb;
			$this->assign['address'] = $address;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			parent::postProcess($params_url);
		}
		
		public function ajaxSearchClient($query){
			$result = $this->models->searchClient($this->escape($query));
			echo json_encode(array('client' => $result));
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getAddress($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countAddress($sql);
			
			$this->assign['address'] = $search;
			$this->assign['nb_address'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$address = $this->addressProcess();
			
			if(!empty($address->id_address)){
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$address = $this->addressProcess(true);
			
			if(!empty($address->id_address)){
				return true;
			}
			
			return false;
		}
		
		public function addressProcess($upd = false){
			
			$id_address = (isset($_POST['id_address'])) ? (int)$_POST['id_address'] : false;
			
			$obj = new AddressModels($id_address);
			
			$obj->id_customer     = (int)$_POST['id_customer'];
			$obj->alias           = $this->escape($_POST['alias']);
			$obj->lastname        = $this->escape($_POST['lastname']);
			$obj->voie            = $this->escape($_POST['voie']);
			$obj->complement_voie = $this->escape($_POST['complement_voie']);
			$obj->postcode        = $_POST['postcode'];
			$obj->ville           = $this->escape($_POST['ville']);
			$obj->phone           = $this->escape($_POST['phone']);
			$obj->date_add        = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd        = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProcess($id){
			
			if(!empty($id)) {
				$obj = new AddressModels($id);
				
				if(!empty($obj->id_address)){
					return $obj->delete();
				}
			}
			
			return false;
		}
	}