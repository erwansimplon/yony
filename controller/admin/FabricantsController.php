<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class FabricantsController extends AdminController
	{
		protected $models;
		protected $image;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'fabricants';
		
		public function __construct ()
		{
			Autoloader::register('core/Image');
			Autoloader::register(_DIR_ORM_.'ImageModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGroupModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'GroupAccessModels');
			Autoloader::register(_DIR_ORM_MANUFACTURERS_.'ManufacturersModels');
			Autoloader::register(_DIR_ORM_MANUFACTURERS_.'ManufacturersImageModels');
			
			$this->models = new FabricantsModels();
			$this->image = new ImageModels();
			
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
			
			if(!empty($_GET['f'])){
				$objet = new ManufacturersModels($_GET['f']);
				$objet_image = new ManufacturersImageModels($objet->id_image);
				$objet = array_merge(get_object_vars($objet), get_object_vars($objet_image));
				
				$group_access = new GroupAccessModels();
				$access = $group_access->getAllowGroupAccess('id_manufacturer', (int)$_GET['f']);
			}
			
			$group = new CustomerGroupModels();
			
			$this->assign = array(
				'admin_menu'   => true,
				'table'        => $this->table,
				'view'         => $this->view(),
				'title'        => ucfirst($this->table),
				'action'       => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'        => !empty($objet['id_manufacturer']) ? $objet : false,
				'groupes'      => $group->getAllCustomerGroup(),
				'group_access' => !empty($access) ? array_column($access, 'id_group') : false,
			);
		}
		
		public function initContent(){
			
			$fabricant = $this->models->getManufacturers($this->limit, $this->pagination);
			$nb = $this->models->countManufacturers();
			
			$this->assign['manufacturers'] = $fabricant;
			$this->assign['nb_manufacturer'] = $nb;
			
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
			
			$search = $this->models->getManufacturers($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countManufacturers($sql);
			
			$this->assign['manufacturers'] = $search;
			$this->assign['nb_manufacturer'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$manufacturer = $this->fabricantProcess();
			
			if(!empty($manufacturer->id_manufacturer)){
				$this->accessGroupProcess($manufacturer->id_manufacturer);
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$manufacturer = $this->fabricantProcess(true);
			
			if(!empty($manufacturer->id_manufacturer)){
				$this->accessGroupProcess($manufacturer->id_manufacturer);
				return true;
			}
			
			return false;
		}
		
		public function fabricantProcess($upd = false){
			
			$id_manufacturer = (isset($_POST['id_manufacturer'])) ? (int)$_POST['id_manufacturer'] : false;
			
			$obj = new ManufacturersModels($id_manufacturer);
			
			if(!empty($_FILES['logo']['tmp_name'])) {
				if($upd){
					$this->deleteImage($obj, (int)$obj->id_manufacturer);
				}
				
				$id_image = $this->uploadImage($_FILES['logo']);
			}
			
			$obj->id_image = (isset($id_image) ? $id_image : (($upd) ? $obj->id_image : 0));
			$obj->name     = $this->escape($_POST['name']);
			$obj->active   = isset($_POST['active']) ? 1 : 0;
			$obj->date_add = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function accessGroupProcess($id_manufacturer){
			
			$clear = new GroupAccessModels();
			$clear->deleteByManufacturer($id_manufacturer);
			
			$id_group = !empty($_POST['id_group']) ? $_POST['id_group'] : false;
			$allow_manufacturer = false;
			
			if($id_group) {
				$allow_manufacturer = implode(",", $_POST['id_group']);
				
				foreach ($id_group as $group) {
					$obj = new GroupAccessModels();
					$obj->id_group = $group;
					$obj->id_category = 0;
					$obj->id_manufacturer = $id_manufacturer;
					$obj->afficher = 1;
					$obj->add();
				}
			}
			
			$group = new CustomerGroupModels();
			$list_group = $group->getCustomerGroupExcluded($allow_manufacturer);
			
			foreach ($list_group as $item){
				$obj = new GroupAccessModels();
				$obj->id_group = $item->id_group;
				$obj->id_category = 0;
				$obj->id_manufacturer = $id_manufacturer;
				$obj->afficher = 0;
				$obj->add();
			}
		}
		
		public function deleteProcess($id){
			
			if(isset($id)) {
				$obj = new ManufacturersModels($id);
				$group = new GroupAccessModels();
				
				if(!empty($obj->id_manufacturer)){
					
					$this->deleteImage($obj, $id);
					$group->deleteByManufacturer($obj->id_manufacturer);
					
					return $obj->delete();
				}
			}
			
			return false;
		}
		
		public function deleteImage($obj, $id){
			
			$data = $obj->getClearBddById($id);
			
			if($data->id_image){
				$image = new Image();
				$image->clearImage('manufacturer/', $data->logo);
				
				$obj_img = new ManufacturersImageModels($data->id_image);
				$obj_img->delete();
			}
		}
		
		public function uploadImage($link_image){
			
			$image = new Image();
			$success = array();
			$base_name = $ext = '';
			
			$params_img = $this->image->getFormatImageByType('manufacturer');
			
			foreach ($params_img as $params){
				
				$upload_img = $image->uploadImage(
					$link_image,
					$params->dir,
					$params->type,
					$params->largeur,
					$params->hauteur
				);
				
				list($upload, $base_name, $ext) = $upload_img;
				
				if(!$upload){
					$success['error'] = true;
				}
			}
			
			$image->clearUpload($link_image['tmp_name']);
			
			if(empty(array_filter($success))){
				
				$new_img = new ManufacturersImageModels();
				
				$new_img->logo = $base_name;
				$new_img->ext  = $ext;
				
				$new_img->add();
				
				return $new_img->id_manufacturers_image;
			}
			
			return false;
		}
	}