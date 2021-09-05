<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class EmployeesController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'employees';
		
		public function __construct()
		{
			Autoloader::register(_DIR_ORM_.'EmployeeModels');
			
			$this->models = new EmployeeModels();
			
			parent::__construct();
			$this->content();
		}
		
		public function content()
		{
			$this->template();
			$this->postProcess();
			parent::content();
		}
		
		public function template(){
			
			$obj = new EmployeeModels();
			$objet = $obj->getEmployeeByToken($this->escape($_SESSION['employee']['token']));
			
			$this->assign = array(
				'admin_menu' => true,
				'table'      => $this->table,
				'view'       => $this->view($this->table),
				'title'      => 'Mes préférences',
				'objet'      => $objet,
			);
		}
		
		public function postProcess($params_url = false)
		{
			if(!empty($_POST['modifier'])){
				
				$update = $this->updateProcess();
				
				if($update){
					header('Location: '.$this->table.'&success=upd');
				} else{
					header('Location: '.$this->table.'&fail=upd');
				}
			}
			
			parent::postProcess($params_url);
		}
		
		public function updateProcess(){
			
			$id_employees = (isset($_POST['id_employees'])) ? (int)$_POST['id_employees'] : false;
			
			$obj = new EmployeeModels($id_employees);
			
			if(!empty($obj->id_employees)){
				
				$obj->name     = $this->escape($_POST['name']);
				$obj->email    = $this->escape($_POST['email']);
				if(!empty($_POST['password'])){
					$obj->password = $this->crypt($_POST['password']);
				}
				$obj->date_upd = $this->date;
				
				return $obj->upd();
			}
			
			return false;
		}
		
		public function view($table = false)
		{
			$prefix_view = 'admin/'.$table.'/Admin'.ucfirst($table);
			
			$view = $prefix_view.'ActionViews.twig';
			
			return $view;
		}
	}

//$token = openssl_random_pseudo_bytes(16);

//Convert the binary data into hexadecimal representation.
//$token = bin2hex($token);