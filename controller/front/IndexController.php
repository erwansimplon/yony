<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class IndexController extends Render
	{
		protected $email;
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		public function __construct ()
		{
			$this->email = new EmailCore();
			$this->models = new IndexModels();
			$this->date = date('Y-m-d H:i:s');
			
			parent::__construct();
			$this->content();
		}
		
		public function content ()
		{
			$this->template();
			echo $this->template->render($this->assign);
		}
		
		public function template(){
			
			$this->assign = array(
				'title' => 'Home',
				'view'  => $this->view(),
			);
		}
		
		public function view($table = false){
			
			if(!$table){
				$table = 'global';
			}
			
			$prefix_view = 'front/'.$table.'/Front'.ucfirst($table);
			
			$view = !empty($_GET['action']) ? $prefix_view.'ActionViews.twig'
											: $prefix_view.'Views.twig';
			
			return $view;
		}
	}