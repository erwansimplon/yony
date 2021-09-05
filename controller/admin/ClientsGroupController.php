<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class ClientsGroupController extends AdminController
	{
		protected $models;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'clients_group';
		
		public function __construct ()
		{
			Autoloader::register(_DIR_ORM_.'CategoryModels');
			Autoloader::register(_DIR_ORM_TAX_.'TvaModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'RemisesModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'GroupAccessModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGroupModels');
			Autoloader::register(_DIR_ORM_MANUFACTURERS_.'ManufacturersModels');
			
			$this->models = new ClientsGroupModels();
			
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
			
			$tva = new TvaModels();
			$category = new CategoryModels();
			$manufacturers = new ManufacturersModels();
			
			if(!empty($_GET['g'])){
				$objet = new CustomerGroupModels($_GET['g']);
				$objet = get_object_vars($objet);
				
				$access = new GroupAccessModels();
				$allow_manufacturer = $access->getAllowManufacturerByGroup($objet['id_group']);
				$manufacturer = $manufacturers->getManufacturersActive($allow_manufacturer);
				
				$remises = new RemisesModels();
				$remise_cat = $remises->getRemiseCategoryByIdGroup($objet['id_group']);
				$remise_fab = $remises->getRemiseManufacturerByIdGroup($objet['id_group']);
				
				$remise_cat = array_column($remise_cat, 'remise', 'id_category');
				$remise_fab = array_column($remise_fab, 'remise', 'id_manufacturer');
			}
			
			$this->assign = array(
				'admin_menu'   => true,
				'tree'         => true,
				'js'           => 'group',
				'title'        => 'Groupe client',
				'table'        => $this->table,
				'view'         => $this->view(),
				'list_tva'     => $tva->getAllTva(),
				'category'     => $category->getCategoryTree(),
				'objet'        => !empty($objet['id_group']) ? $objet : false,
				'action'       => !empty($_GET['action']) ? $_GET['action'] : false,
				'remise_cat'   => !empty($remise_cat) ? $remise_cat : false,
				'remise_fab'   => !empty($remise_fab) ? $remise_fab : false,
				'manufacturer' => !empty($manufacturer) ? $manufacturer : false,
			);
		}
		
		public function initContent(){
			
			$nb = $this->models->countCustomerGroup();
			$group = $this->models->getCustomerGroup($this->limit, $this->pagination);
			
			$this->assign['customer_group'] = $group;
			$this->assign['nb_customer_group'] = $nb;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			parent::postProcess($params_url);
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				
				if($key == 'tva_name'){
					$key = 't.name';
				} else{
					$key = 'g.'.$key;
				}
				
				
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getCustomerGroup($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countCustomerGroup($sql);
			
			$this->assign['customer_group'] = $search;
			$this->assign['nb_customer_group'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			$group = $this->customerGroupProcess();
			
			if($group->id_group){
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			$group = $this->customerGroupProcess(true);
			
			if($group->id_group){
				return true;
			}
			
			return false;
		}
		
		public function customerGroupProcess($upd = false){
			
			$id_group = (isset($_POST['id_group'])) ? (int)$_POST['id_group'] : false;
			
			$obj = new CustomerGroupModels($id_group);
			
			$obj->id_tva        = (int)$_POST['id_tva'];
			$obj->name_group    = $this->escape($_POST['name_group']);
			$obj->display_price = isset($_POST['display_price']) ? 1 : 0;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProcess($id){
			
			if(isset($id)) {
				$obj = new CustomerGroupModels($id);
				
				if(!$this->models->countCustomerInGroup($obj->id_group)){
					
					$this->deleteRemiseProcess($obj->id_group);
					$this->deleteAccessProcess($obj->id_group);
					
					return $obj->delete();
				}
				
				return true;
			}
			
			return false;
		}
		
		public function deleteRemiseProcess($id_group){
			$remise = new RemisesModels();
			$remise->deleteRemisesByIdGroup($id_group);
		}
		
		public function deleteAccessProcess($id_group){
			$access = new GroupAccessModels();
			$access->deleteAccessByIdGroup($id_group);
		}
		
		public function ajaxAddRemiseCategory($form){
			$this->addRemise($form, 'Category', 'id_category');
		}
		
		public function ajaxAddRemiseFabricant($form){
			$this->addRemise($form, 'Manufacturer', 'id_manufacturer');
		}
		
		public function addRemise($tabs, $type, $id_type){
			
			$form = $this->unserializeForm($tabs);
			
			if(!empty($form['id_group']) && !empty($form[$id_type])){
				
				$obj = new RemisesModels();
				$id_remise = $obj->{'getIdBy'.$type}($form['id_group'], $form[$id_type]);
				
				if($id_remise){
					$obj = new RemisesModels($id_remise);
				} else {
					$obj->id_group        = (int)$form['id_group'];
					$obj->id_category     = (!empty($form['id_category'])) ? $form['id_category'] : 0;
					$obj->id_manufacturer = (!empty($form['id_manufacturer'])) ? $form['id_manufacturer'] : 0;
				}
				
				$obj->remise = $this->float($form['reduction_'.$id_type]);
				
				return ($id_remise) ? $obj->upd() : $obj->add();
			}
			
			return false;
		}
	}