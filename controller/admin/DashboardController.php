<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class DashboardController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		
		/**
		 * MailController constructor.
		 */
		public function __construct ()
		{
			$this->models = new DashboardModels();
			
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
				'admin_menu' => true,
				'title'      => 'Dashboard',
				'css'        => 'dashboard',
				'view'       => 'admin/AdminDashboardViews.twig',
				'not_found'  => isset($_GET['not_found']) ? $_GET['not_found'] : false,
			);
		}
		
		public function postProcess($params_url = false){
			parent::postProcess($params_url);
		}
		
	}