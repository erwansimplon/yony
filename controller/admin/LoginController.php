<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	
	class LoginController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		
		/**
		 * MailController constructor.
		 */
		public function __construct ()
		{
			Autoloader::register('models/orm/EmployeeModels');
			$this->models = new LoginModels();
			
			parent::__construct();
			$this->content();
		}
		
		public function content ()
		{
			$this->template();
			$this->postProcess();
			parent::content();
		}
		
		public function template(){
			$this->assign = array(
				'admin_menu'   => true,
				'title' => 'Panel Admin',
				'css'   => 'login',
				'view'  => 'admin/AdminLoginViews.twig'
			);
		}
		
		public function postProcess($params_url = false){
			if(!empty($_POST['submit_connexion'])){
				$this->processConnexion();
			}
		}
		
		public function processConnexion(){
			$employees = $this->models->getIdAuthEmployee($this->escape($_POST['email']));
			
			if($employees->id_employees && password_verify($this->escape($_POST['password']), $employees->password)) {
				$employee = new EmployeeModels($employees->id_employees);
				
				$_SESSION['employee'] = array('name'  => $employee->name,
											  'token' => $employee->token);
				
				header('Location: dashboard');
			}
		}
	}