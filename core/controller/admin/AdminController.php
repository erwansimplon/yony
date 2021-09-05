<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class AdminController extends Render
	{
		protected $table;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		public function __construct ($pagination = false)
		{
			$this->date = date('Y-m-d H:i:s');
			parent::__construct('admin/');
			
			if($pagination) {
				Autoloader::register('core/pagination');
			}
		}
		
		public function content ()
		{
			echo $this->template->render($this->assign);
		}
		
		public function postProcess($params_url = false){
			
			$link = ($params_url) ? $this->table.$params_url : $this->table;
			
			if(!empty($_POST['rechercher_'.$this->table])){
			
				unset($_POST['rechercher_'.$this->table]);
				
				$this->searchProcess();
			}
			
			if (!empty($_POST['ajouter_'.$this->table])){
				
				$add = $this->insertProcess();
				
				if($add){
					header('Location: '.$link);
				}
			}
			
			if(!empty($_POST['modifier_'.$this->table])){
				
				$update = $this->updateProcess();
				
				if($update){
					header('Location: '.$link);
				}
			}
			
			if(!empty($_GET['d'])){
				
				$delete = $this->deleteProcess($_GET['d']);
				
				if($delete){
					header('Location: '.$link);
				}
			}
		}
		
		public function initPagination(){
			$this->page  = !empty($_GET['p']) ? $_GET['p'] : 1;
			$this->limit = ($this->page -1) * $this->pagination;
			
			$this->assign['page'] = $this->page;
		}
		
		public function pagination($nb){
			
			list($nb_pagination, $p_start, $p_end) = Pagination::generate($this->page, $nb, $this->pagination);
			
			$this->assign['link_pagination'] = $this->table.'&p=';
			$this->assign['nb_pagination'] = $nb_pagination;
			$this->assign['p_start'] = $p_start;
			$this->assign['p_end'] = $p_end;
		}
		
		public function unserializeForm($string) {
			
			$form = array();
			$items = explode("&", $string);
			
			foreach ($items as $item) {
				$item = str_replace('%20', ' ', $item);
				$item = str_replace('%2C', '.', $item);
				list($key, $value) = explode("=", urldecode($item));
				$form[$key] = $value;
			}
			
			return $form;
		}
		
		public function generateBreadcrumb($tabs, $objet, $id){
			
			$parent = $objet->getFamilleById($id);
			
			if($parent->id_parent != 0){
				$tabs[] = $parent->name;
				return $this->generateBreadcrumb($tabs, $objet, $parent->id_parent);
			} else{
				$tabs[] = $parent->name;
			}
			
			krsort($tabs);
			return $tabs;
		}
		
		public function crypt($string){
			return password_hash($this->escape($string), PASSWORD_BCRYPT, array('cost' => 12));
		}
		
		public function date($date){
			return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
		}
		
		public function float($number){
			return round(str_replace(',', '.', $number), 2);
		}
		
		public function escape($string)
		{
			return addcslashes(strip_tags(nl2br($string)), '"');
		}
		
		public function cut($string, $start, $end){
			if(strlen($string) > $end){
				$string = substr($string,$start,$end);
				$espace = strrpos($string, ' ');
				$string = substr($string,$start, $espace);
			}
			
			return $string;
		}
		
		public function view($table = false){
			
			if(!$table){
				$table = 'global';
			}
			
			$prefix_view = 'admin/'.$table.'/Admin'.ucfirst($table);
			
			$view = !empty($_GET['action']) ? $prefix_view.'ActionViews.twig'
											: $prefix_view.'Views.twig';
			
			return $view;
		}
	}