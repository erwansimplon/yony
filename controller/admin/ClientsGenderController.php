<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class ClientsGenderController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'clients_gender';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGenderModels');
			
			$this->models = new ClientsGenderModels();
			
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
			
			if(!empty($_GET['g'])){
				$objet = new CustomerGenderModels($_GET['g']);
				$objet = get_object_vars($objet);
			}
			
			$this->assign = array(
				'admin_menu' => true,
				'title'      => 'Civilité',
				'table'      => $this->table,
				'view'       => $this->view(),
				'action'     => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'      => !empty($objet['id_gender']) ? $objet : false,
			);
		}
		
		public function initContent(){
			
			$nb = $this->models->countGender();
			$gender = $this->models->getGender($this->limit, $this->pagination);
			
			$this->assign['nb_gender'] = $nb;
			$this->assign['gender']   = $gender;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			parent::postProcess($params_url);
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getGender($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countGender($sql);
			
			$this->assign['gender'] = $search;
			$this->assign['nb_gender'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$gender = $this->genderProcess();
			
			if(!empty($gender->id_gender)){
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$gender = $this->genderProcess(true);
			
			if(!empty($gender->id_gender)){
				return true;
			}
			
			return false;
		}
		
		public function genderProcess($upd = false){
			
			$id_gender = (isset($_POST['id_gender'])) ? (int)$_POST['id_gender'] : false;
			
			$obj = new CustomerGenderModels($id_gender);
			
			$obj->name = $this->escape($_POST['name']);
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProcess($id){
			
			if(!empty($id)) {
				$obj = new CustomerGenderModels((int)$id);
				
				if(!empty($obj->id_gender)){
					return $obj->delete();
				}
			}
			
			return false;
		}
	}