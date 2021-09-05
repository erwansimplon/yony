<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class TransporteursController extends AdminController
	{
		protected $models;
		protected $image;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'transporteurs';
		
		public function __construct ()
		{
			Autoloader::register('core/Image');
			Autoloader::register(_DIR_ORM_.'ImageModels');
			Autoloader::register(_DIR_ORM_TAX_.'TaxModels');
			Autoloader::register(_DIR_ORM_CARRIER_.'CarrierModels');
			Autoloader::register(_DIR_ORM_CARRIER_.'CarrierTrancheModels');
			Autoloader::register(_DIR_ORM_CARRIER_.'CarrierImageModels');
			Autoloader::register(_DIR_ORM_CARRIER_.'CarrierAccessModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGroupModels');
			
			parent::__construct(true);
			
			$this->image = new ImageModels();
			$this->models = new TransporteursModels();
			
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
			
			$tax    = new TaxModels();
			$group  = new CustomerGroupModels();
			$images = new CarrierImageModels();
			
			if(!empty($_GET['t'])){
				$objet = new CarrierModels($_GET['t']);
				$objet = get_object_vars($objet);
				
				
				$tranche = new CarrierTrancheModels();
				$group_access = new CarrierAccessModels();
				
				$plages = $tranche->getTrancheByIdCarrier((int)$_GET['t']);
				$access = $group_access->getAllowGroupAccess((int)$_GET['t']);
			}
			
			$sub_menu = array(
				'Params'      => 'Paramètre',
				'Price'       => 'Tarification',
				'Restriction' => 'Restriction',
			);
			
			$this->assign = array(
				'admin_menu'    => true,
				'title'         => 'Transporteurs',
				'js'            => 'carrier',
				'css'           => 'carrier',
				'sub_menu'      => $sub_menu,
				'table'         => $this->table,
				'view'          => $this->view(),
				'taxes'         => $tax->getAllTax(),
				'groupes'       => $group->getAllCustomerGroup(),
				'group_access'  => !empty($access) ? array_column($access, 'id_group') : false,
				'plages'        => !empty($plages) ? $plages : false,
				'action'        => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'         => !empty($objet['id_carrier']) ? $objet : false,
				'image'         => !empty($objet['id_carrier']) ? $images->getImageByIdCarrier($objet['id_carrier']) : false,
			);
		}
		
		public function initContent(){
			
			$carrier = $this->models->getCarriers($this->limit, $this->pagination);
			$nb = $this->models->countCarriers();
			
			$this->assign['carriers'] = $carrier;
			$this->assign['nb_carrier'] = $nb;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			
			parent::postProcess($params_url);
			
			if(!empty($_GET['d_plages'])){
				
				$delete = $this->deleteTrancheProcess($_GET['d_plages']);
				
				if($delete){
					header('Location: '.$_SERVER['HTTP_REFERER']);
				}
			}
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getCarriers($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countCarriers($sql);
			
			$this->assign['carriers'] = $search;
			$this->assign['nb_carrier'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		public function insertProcess(){
			
			$carrier = $this->carrierProcess();
			
			if(!empty($carrier->id_carrier)){
				$this->accessGroupProcess($carrier->id_carrier);
				
				if($carrier->free_carrier == 0) {
					$this->addtrancheProcess($carrier->id_carrier);
				}
				
				return true;
			}
			
			return false;
		}
		
		public function updateProcess(){
			
			$carrier = $this->carrierProcess(true);
			
			if(!empty($carrier->id_carrier)){
				$this->trancheProcess($carrier->id_carrier, $carrier->free_carrier);
				$this->accessGroupProcess($carrier->id_carrier);
				return true;
			}
			
			return false;
		}
		
		public function carrierProcess($upd = false){
			
			$id_carrier = (isset($_POST['id_carrier'])) ? (int)$_POST['id_carrier'] : false;
			
			$obj = new CarrierModels($id_carrier);
			
			$obj->id_tax       = (int)$_POST['id_tax'];
			$obj->name         = $this->escape($_POST['name']);
			$obj->delay        = $this->escape($_POST['delay']);
			$obj->suivi        = $this->escape($_POST['suivi']);
			$obj->active       = isset($_POST['active']) ? 1 : 0;
			$obj->free_carrier = isset($_POST['free_carrier']) ? 1 : 0;
			$obj->facturation  = (int)$_POST['facturation'];
			$obj->date_add     = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd     = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			$this->imageProcess($obj, $upd);
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function imageProcess($obj, $upd = false){
			if(!empty($_FILES['logo']['tmp_name'])) {
				if($upd){
					$this->deleteImage($obj->id_carrier);
				}
				
				$this->uploadImage($_FILES['logo'], $obj->id_carrier);
			}
		}
		
		public function uploadImage($link_image, $id_carrier){
			
			$image = new Image();
			$success = array();
			$base_name = $ext = '';
			
			$params_img = $this->image->getFormatImageByType('carrier');
			
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
				
				$new_img = new CarrierImageModels();
				
				$new_img->id_carrier = $id_carrier;
				$new_img->logo = $base_name;
				$new_img->ext = $ext;
				
				$new_img->add();
				
				return $new_img->id_carrier_image;
			}
			
			return false;
		}
		
		public function trancheProcess($id_carrier, $free_carrier){
			
			if($free_carrier == 0) {
				if (!empty($_POST['val_maxi']) && !empty($_POST['price'])) {
					$this->addtrancheProcess($id_carrier);
				}
				
				$post_tranche = preg_grep("/^price_/i", array_keys($_POST));
				
				if (!empty($post_tranche)) {
					foreach ($post_tranche as $value) {
						
						$tranche = explode('_', $value);
						$obj = new CarrierTrancheModels((int)$tranche[1]);
						
						if (!empty($obj->id_carrier_tranche)) {
							$obj->val_mini = (float)$this->float($_POST['val_mini_' . $obj->id_carrier_tranche]);
							$obj->val_maxi = (float)$this->float($_POST['val_maxi_' . $obj->id_carrier_tranche]);
							$obj->price = (float)$this->float($_POST[$value]);
							$obj->upd($obj->id_carrier_tranche);
						}
					}
				}
			} else{
				$tranche = new CarrierTrancheModels();
				$tranche->deleteByCarrier($id_carrier);
			}
		}
		
		public function accessGroupProcess($id_carrier){
			
			$clear = new CarrierAccessModels();
			$clear->deleteByCarrier($id_carrier);
			
			$id_group = !empty($_POST['id_group']) ? $_POST['id_group'] : false;
			$allow_carrier = false;
			
			if($id_group) {
				$allow_carrier = implode(",", $_POST['id_group']);
				
				foreach ($id_group as $group) {
					$obj = new CarrierAccessModels();
					$obj->id_group = $group;
					$obj->id_carrier = $id_carrier;
					$obj->afficher = 1;
					$obj->add();
				}
			}
			
			$group = new CustomerGroupModels();
			$list_group = $group->getCustomerGroupExcluded($allow_carrier);
			
			foreach ($list_group as $item){
				$obj = new CarrierAccessModels();
				$obj->id_group = $item->id_group;
				$obj->id_carrier = $id_carrier;
				$obj->afficher = 0;
				$obj->add();
			}
		}
		
		public function addtrancheProcess($id_carrier){
			$obj = new CarrierTrancheModels();
			$obj->id_carrier = $id_carrier;
			$obj->val_mini   = (float)$this->float($_POST['val_mini']);
			$obj->val_maxi   = (float)$this->float($_POST['val_maxi']);
			$obj->price      = (float)$this->float($_POST['price']);
			$obj->add();
		}
		
		public function deleteProcess($id){
			
			if(isset($id)) {
				$obj = new CarrierModels((int)$id);
				$group = new CarrierAccessModels();
				$tranche = new CarrierTrancheModels();
				
				if(!empty($obj->id_carrier)){
					$group->deleteByCarrier($obj->id_carrier);
					$tranche->deleteByCarrier($obj->id_carrier);
					$this->deleteImage($obj->id_carrier);
					return $obj->delete();
				}
			}
			
			return false;
		}
		
		public function deleteTrancheProcess($id){
			$obj = new CarrierTrancheModels($id);
			
			if(!empty($obj->id_carrier_tranche)){
				return $obj->delete($obj->id_carrier_tranche);
			}
			
			return false;
		}
		
		public function deleteImage($id){
			
			$obj = new CarrierImageModels();
			$data = $obj->getImageByIdCarrier($id);
			
			if(isset($data->id_carrier_image)){
				$image = new Image();
				$image->clearImage('carrier/', $data->logo);
				
				$obj_img = new CarrierImageModels($data->id_carrier_image);
				$obj_img->delete($data->id_carrier_image);
			}
		}
		
		public function ajaxAddPlage($data){
			$form = $this->unserializeForm($data);
			
			$obj = new CarrierModels((int)$form['id_carrier']);
			$obj->free_carrier = isset($form['free_carrier']) ? 1 : 0;
			$obj->date_upd = $this->date;
			$obj->upd();
			
			$tranche = new CarrierTrancheModels();
			$tranche->id_carrier = (int)$form['id_carrier'];
			$tranche->val_mini   = (float)$this->float($form['val_mini_ajax']);
			$tranche->val_maxi   = (float)$this->float($form['val_maxi_ajax']);
			$tranche->price      = (float)$this->float($form['price_ajax']);
			$tranche->add();
		}
	}