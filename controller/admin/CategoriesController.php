<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class CategoriesController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $table = 'categories';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_.'CategoryModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGroupModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'GroupAccessModels');
			
			$this->models = new CategoriesModels();
			
			parent::__construct();
			
			if(isset($_POST['function'])) {
				$this->{'ajax'.$_POST['function']}($_POST['data']);
			} else{
				$this->content();
			}
		}
		
		public function content ()
		{
			$this->template();
			$this->initContent();
			$this->postProcess();
			parent::content();
		}
		
		public function template(){
	
			if(!empty($_GET['c'])){
				$objet = new CategoryModels((int)$_GET['c']);
				$objet = get_object_vars($objet);
				
				$group_access = new GroupAccessModels();
				$access = $group_access->getAllowGroupAccess('id_category', (int)$_GET['c']);
			}
			
			$category = new CategoryModels();
			$group    = new CustomerGroupModels();
			
			$this->assign = array(
				'admin_menu'    => true,
				'return'        => true,
				'tree'          => true,
				'draggable'     => true,
				'title'         => 'Catégories',
				'table'         => $this->table,
				'view'          => $this->view(),
				'category_tree' => $category->getCategoryTree(),
				'groupes'       => $group->getAllCustomerGroup(),
				'group_access'  => !empty($access) ? array_column($access, 'id_group') : false,
				'action'        => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'         => !empty($objet['id_category']) ? $objet : false,
			);
		}
		
		public function initContent(){
			$id = !empty($_GET['p']) ? $_GET['p'] : 1;
			$tabs_parents = array();
			
			$category = $this->models->getFamilleByIdParent($id);
			$parent = $this->models->getFamilleById($id);
			$breadcrumb = $this->generateBreadcrumb($tabs_parents, $this->models, $parent->id_category);
			
			$this->assign['category'] = $category;
			$this->assign['parent'] = $parent;
			$this->assign['breadcrumb'] = $breadcrumb;
		}
		
		public function postProcess($params_url = false){
			
			$page = (isset($_GET['p'])) ? '&p='.$_GET['p'] : '';
			parent::postProcess($page);
		}
		
		public function ajaxChangePosition($items){
			
			
			$tabs = $this->unserializeForm($items);
			
			foreach ($tabs as $pos => $item){
				$this->models->updatePositionByCategory($item, $pos);
			}
			
			$obj = new CategoryModels();
			$obj->RegenerateNleftNright();
			
			echo json_encode(array('position' => $tabs));
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->searchFamille($sql);
		
			$this->assign['category'] = $search;
		}
		
		public function insertProcess(){
			
			$category = $this->categoryProcess();
			
			if(!empty($category->id_category)){
				$this->accessGroupProcess($category->id_category);
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$category = $this->categoryProcess(true);
			
			if(!empty($category->id_category)){
				$this->accessGroupProcess($category->id_category);
				return true;
			}
			
			return false;
		}
		
		public function categoryProcess($upd = false){
			
			$id_parent = (int)$_POST['id_parent'];
			$id_category = (isset($_POST['id_category'])) ? (int)$_POST['id_category'] : false;
			
			$obj = new CategoryModels($id_category);
			$parent = new CategoryModels($id_parent);
			
			$obj->id_parent = $_POST['id_parent'];
			$obj->name      = $this->escape($_POST['name']);
			$obj->position  = ($upd) ? $obj->position : $this->models->lastPositionIdParent($id_parent);
			$obj->active    = isset($_POST['active']) ? 1 : 0;
			$obj->level     = $parent->level +1;
			$obj->date_add  = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd  = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			$obj->RegenerateNleftNright();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function accessGroupProcess($id_category){
			
			$clear = new GroupAccessModels();
			$clear->deleteByCategory($id_category);
			
			$id_group = !empty($_POST['id_group']) ? $_POST['id_group'] : false;
			$allow_category = false;
			
			if($id_group) {
				$allow_category = implode(",", $_POST['id_group']);
				
				foreach ($id_group as $group) {
					$obj = new GroupAccessModels();
					$obj->id_group = $group;
					$obj->id_category = $id_category;
					$obj->id_manufacturer = 0;
					$obj->afficher = 1;
					$obj->add();
				}
			}
			
			$group = new CustomerGroupModels();
			$list_group = $group->getCustomerGroupExcluded($allow_category);
			
			foreach ($list_group as $item){
				$obj = new GroupAccessModels();
				$obj->id_group = $item->id_group;
				$obj->id_category = $id_category;
				$obj->id_manufacturer = 0;
				$obj->afficher = 0;
				$obj->add();
			}
		}
		
		public function deleteProcess($id){
			
			if(isset($id)) {
				$obj = new CategoryModels($id);
				$group = new GroupAccessModels();
				
				if(!empty($obj->id_category)){
					$delete = $obj->delete();
					$obj->RegenerateNleftNright();
					$group->deleteByCategory($obj->id_category);
					return $delete;
				}
			}
			
			return false;
		}
		
		public function unserializeForm($string) {
			
			$tabs = array();
			$items = explode("&", $string);
			
			$i = 1;
			foreach ($items as $item) {
				$value = explode("=", $item);
				$tabs[$i] = $value[1];
				$i++;
			}
			
			return $tabs;
		}
	}