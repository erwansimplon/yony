<?php
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:00
	 */
	class ProduitsController extends AdminController
	{
		protected $models;
		protected $image;
		protected $template;
		protected $assign;
		protected $date;
		
		protected $pagination = 12;
		protected $page;
		protected $limit;
		
		protected $table = 'produits';
		
		public function __construct ()
		{
			Autoloader::register('core/Pdf');
			Autoloader::register('core/Image');
			Autoloader::register('models/CategoriesModels');
			Autoloader::register(_DIR_ORM_.'ImageModels');
			Autoloader::register(_DIR_ORM_.'CategoryModels');
			Autoloader::register(_DIR_ORM_TAX_.'TaxModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsLangModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsDiscountsModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsImageModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsAttachmentsModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsFeaturesModels');
			Autoloader::register(_DIR_ORM_PRODUCTS_.'ProductsFeaturesLangModels');
			Autoloader::register(_DIR_ORM_CUSTOMERS_.'CustomerGroupModels');
			Autoloader::register(_DIR_ORM_MANUFACTURERS_.'ManufacturersModels');
			
			$this->image = new ImageModels();
			$this->models = new ProduitsModels();
			
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
			
			$tabs_cat_parents = array();
			
			$tax        = new TaxModels();
			$category   = new CategoryModels();
			$categories = new CategoriesModels();
			$images     = new ProductsImageModels();
			$documents  = new ProductsAttachmentsModels();
			$features   = new ProductsFeaturesLangModels();
			$group_client  = new CustomerGroupModels();
			$manufacturers = new ManufacturersModels();
			
			if(!empty($_GET['a'])){
				
				$tab_obj = array();
				$table = array('Products', 'ProductsLang');
				
				foreach ($table as $obj){
					$class = $obj.'Models';
					$objet  = new $class((int)$_GET['a']);
					$tab_obj[$obj] = get_object_vars($objet);
				}
				
				$objet = call_user_func_array('array_merge', $tab_obj);
				
				$obj_promo = new ProductsDiscountsModels();
				$discounts = $obj_promo->getDiscountsByIdProduct((int)$_GET['a']);
				
				$cat_parent = $categories->getFamilleById($objet['id_category']);
				$breadcrumb = $this->generateBreadcrumb($tabs_cat_parents, $categories, $cat_parent->id_category);
			}
			
			$sub_menu = array(
				'Informations' => 'Informations',
				'Prix'         => 'Prix',
				'Associations' => 'Associations',
				'Quantity'     => 'Quantité',
				'Images'       => 'Images',
				'Documents'    => 'Documents',
				'Features'     => 'Caractéristiques'
			);
			
			$this->assign = array(
				'admin_menu'   => true,
				'tree'         => true,
				'tinymce'      => true,
				'datepicker'   => true,
				'fancybox'     => true,
				'js_image'     => true,
				'title'        => 'Produits',
				'js'           => 'product',
				'table'        => $this->table,
				'sub_menu'     => $sub_menu,
				'view'         => $this->view(),
				'taxes'        => $tax->getAllTax(),
				'category'     => $category->getCategoryTree(),
				'group_client' => $group_client->getAllCustomerGroup(),
				'manufacturer' => $manufacturers->getManufacturersActive(),
				'discounts'    => !empty($discounts) ? $discounts : false,
				'breadcrumb'   => !empty($breadcrumb) ? $breadcrumb : false,
				'action'       => !empty($_GET['action']) ? $_GET['action'] : false,
				'objet'        => !empty($objet['id_product']) ? $objet : false,
				'images'       => !empty($objet['id_product']) ? $images->getImageByIdProduct($objet['id_product']) : false,
				'documents'    => !empty($objet['id_product']) ? $documents->getAttachmentsByIdProduct($objet['id_product']) : false,
				'features'     => !empty($objet['id_product']) ? $features->getFeaturesLangByIdProduct($objet['id_product']) : false,
				'list_feature' => !empty($objet['id_product']) ? $features->getAllowFeatures($objet['id_product']) : false,
			);
		}
		
		public function initContent(){
			
			$nb = $this->models->countProducts();
			$products = $this->models->getProducts($this->limit, $this->pagination);
			
			$this->assign['nb_products'] = $nb;
			$this->assign['products'] = $products;
			
			$this->pagination($nb);
		}
		
		public function postProcess($params_url = false){
			
			parent::postProcess($params_url);
			
			if(!empty($_GET['d_discount'])){
				
				$delete = $this->deleteDiscountValueProcess($_GET['d_discount']);
				
				if($delete){
					header('Location: '.$_SERVER['HTTP_REFERER']);
				}
			}
			
			if(!empty($_GET['d_img'])){
				
				$delete = $this->deleteImageProcess($_GET['d_img']);
				
				if($delete){
					header('Location: '.$_SERVER['HTTP_REFERER']);
				}
			}
			
			if(!empty($_GET['d_pdf'])){
				
				$delete = $this->deleteDocumentProcess($_GET['d_pdf']);
				
				if($delete){
					header('Location: '.$_SERVER['HTTP_REFERER']);
				}
			}
			
			if(!empty($_GET['d_feature'])){
				
				$delete = $this->deleteFeatureValueProcess($_GET['d_feature']);
				
				if($delete){
					header('Location: '.$_SERVER['HTTP_REFERER']);
				}
			}
		}
		
		public function searchProcess(){
			
			$sql = '1';
			
			foreach (array_filter($_POST, 'strlen') as $key => $value){
				
				if($key == 'manufacturer'){
					$key = 'm.name';
				} else{
					$key = 'p.'.$key;
				}
				
				$sql .= ' AND '.$key.' LIKE "%'.$this->escape($value).'%"';
			}
			
			$search = $this->models->getProducts($this->limit, $this->pagination, $sql);
			$nb_search = $this->models->countProducts($sql);
			
			$this->assign['products'] = $search;
			$this->assign['nb_products'] = $nb_search;
			
			$this->pagination($nb_search);
		}
		
		
		public function insertProcess(){
			
			$product = $this->productProcess();
			
			if(!empty($product->id_product)){
				$this->imageProcess($product->id_product);
				$this->documentProcess($product->id_product);
				$this->productLangProcess($product->id_product);
				
				return true;
			}
			
			return false;
		}
		
		
		public function updateProcess(){
			$product = $this->productProcess(true);
			
			if(!empty($product->id_product)){
				$this->imageProcess($product->id_product);
				$this->documentProcess($product->id_product);
				$this->featureProcess();
				$this->productLangProcess($product->id_product, true);
				
				return true;
			}
			
			return false;
		}
		
		
		public function deleteProcess($id){
			
			if(!empty($id)) {
				$obj = new ProductsModels((int)$id);
				
				if (!empty($obj->id_product)) {
					$this->deleteImages($obj->id_product);
					$this->deleteDocument($obj->id_product);
					$this->deleteFeature($obj->id_product);
					$this->deleteProductLang($obj->id_product);
					$this->deleteProductDiscount($obj->id_product);
					
					return $obj->delete();
				}
			}
			
			return false;
		}
		
		public function deleteProductDiscount($id_product){
			$obj_promo = new ProductsDiscountsModels();
			$discounts = $obj_promo->getDiscountsByIdProduct((int)$id_product);
			
			foreach ($discounts as $discount){
				$this->deleteDiscountValueProcess($discount->id_products_discounts);
			}
		}
		
		public function deleteDiscountValueProcess($id_discount){
			$obj = new ProductsDiscountsModels((int)$id_discount);
			return $obj->delete();
		}
		
		public function deleteImages($id_product){
			$obj = new ProductsImageModels();
			
			$images = $obj->getImageByIdProduct((int)$id_product);
			
			if(isset($images)){
				foreach ($images as $img){
					$this->deleteImageProcess($img->id_products_image);
				}
			}
		}
		
		public function deleteImageProcess($id){
			if(isset($id)) {
				$obj = new ProductsImageModels($id);
				
				$image = new Image();
				$image->clearImage('product/', $obj->image);
				
				return $obj->delete();
			}
			
			return false;
		}
		
		
		public function ajaxUpdateCoverImage($data){
			
			$images = new ProductsImageModels();
			
			if(!empty((int)$data['id_product']) && !empty((int)$data['id_products_image'])) {
				$images->resetCoverImage((int)$data['id_product']);
				$images->changeCoverImage((int)$data['id_products_image']);
			}
		}
		
		public function imageProcess($id_product){
			if(!empty($_FILES['id_image']['tmp_name'])) {
				return $this->uploadImage($id_product, $_FILES['id_image']);
			}
			
			return false;
		}
		
		public function uploadImage($id_product, $link_image){
			
			$image = new Image();
			$success = array();
			$base_name = $ext = '';
			
			$params_img = $this->image->getFormatImageByType('product');
			
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
				
				$new_img = new ProductsImageModels();
				
				$new_img->product_id = $id_product;
				$new_img->cover      = $new_img->getImageCoverExist($id_product);
				$new_img->image      = $base_name;
				$new_img->ext        = $ext;
				
				$new_img->add();
				
				return $new_img->id_products_image;
			}
			
			return false;
		}
		
		
		public function documentProcess($id_product){
			
			if(!empty($_FILES['pdf']['tmp_name'])) {
				$pdf = new Pdf();
				
				list($move, $base_name) = $pdf->uploadPdf($_FILES['pdf']);
				
				if($move){
					
					$obj = new ProductsAttachmentsModels();
					
					$obj->id_product = $id_product;
					$obj->pdf_file   = $base_name;
					
					$obj->add();
				}
			}
		}
		
		public function deleteDocument($id_product){
			$obj = new ProductsAttachmentsModels();
			
			$documents = $obj->getAttachmentsByIdProduct($id_product);
			
			if(isset($documents)){
				foreach ($documents as $pdf){
					$this->deleteDocumentProcess($pdf->id_products_attachments);
				}
			}
		}
		
		public function deleteDocumentProcess($id){
			if(isset($id)) {
				$obj = new ProductsAttachmentsModels($id);
				
				$pdf = new Pdf();
				$pdf->clearPdf($obj->pdf_file);
				
				return $obj->delete();
			}
			
			return false;
		}
		
		
		public function featureProcess(){
			
			$post_feature = preg_grep("/^feature_/i", array_keys($_POST));
			
			if(!empty($post_feature)) {
				foreach ($post_feature as $value) {
					
					$feature = explode('_', $value);
					$obj = new ProductsFeaturesLangModels((int)$feature[1]);
					
					if (!empty($obj->id_products_features_lang)) {
						$obj->value = $this->escape($_POST[$value]);
						$obj->upd();
					}
				}
			}
		}
		
		public function ajaxAddFeature($data){
			
			$form = $this->unserializeForm($data);
			
			if(empty($form['id_products_features'])){
				$new_feature = new ProductsFeaturesModels();
				$new_feature->type = $this->escape($form['new_id_feature']);
				$new_feature->add();
				
				$id_feature = $new_feature->id_products_features;
			} else{
				$id_feature = (int)$form['id_products_features'];
			}
			
			$feature = new ProductsFeaturesModels($id_feature);
			
			if(!empty($feature->id_products_features)){
				
				$obj = new ProductsFeaturesLangModels();
				
				$obj->id_products_features = $feature->id_products_features;
				$obj->id_product = (int)$form['id_product'];
				$obj->value = $this->escape($form['value']);
				
				return $obj->add();
			}
			
			return false;
		}
		
		public function deleteFeature($id_product){
			$obj = new ProductsFeaturesLangModels();
			$features = $obj->getFeaturesLangByIdProduct((int)$id_product);
			
			foreach ($features as $feature){
				$this->deleteFeatureValueProcess($feature->id_products_features_lang);
			}
		}
		
		public function deleteFeatureValueProcess($id){
			if(!empty($id)) {
				$obj = new ProductsFeaturesLangModels((int)$id);
				$id_feature = $obj->id_products_features;
				
				$delete = $obj->delete();
				$rest = $obj->getRestProductInFeature($id_feature);
				
				if(!$rest){
					$feature = new ProductsFeaturesModels($id_feature);
					$feature->delete();
				}
				
				return $delete;
			}
			
			return false;
		}
		
		
		public function productProcess($upd = false){
			
			$id_product = (isset($_POST['id_product'])) ? (int)$_POST['id_product'] : false;
			
			$obj = new ProductsModels($id_product);
			
			$obj->id_manufacturer = (int)$_POST['id_manufacturer'];
			$obj->id_category     = (int)$_POST['id_category'];
			$obj->id_tax          = (int)$_POST['id_tax'];
			$obj->reference       = $this->escape($_POST['reference']);
			$obj->name            = $this->escape($_POST['name']);
			$obj->ean13           = $this->escape($_POST['ean13']);
			$obj->active          = isset($_POST['active']) ? 1 : 0;
			$obj->quantity        = (int)$_POST['quantity'];
			$obj->conditionnement = (int)$_POST['conditionnement'];
			$obj->prix_achat      = (float)$this->float($_POST['prix_achat']);
			$obj->prix_vente      = (float)$this->float($_POST['prix_vente']);
			$obj->ecotax          = !empty($_POST['ecotax']) ? (float)$this->float($_POST['ecotax']) : 0;
			$obj->weight          = !empty($_POST['weight']) ? (float)$this->float($_POST['weight']) : 0;
			$obj->date_add        = ($upd) ? $obj->date_add : $this->date;
			$obj->date_upd        = $this->date;
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function productLangProcess($id_product, $upd = false){
			
			$id_product_lang = (isset($_POST['id_product_lang'])) ? (int)$_POST['id_product_lang'] : false;
			
			$obj = new ProductsLangModels(false, $id_product_lang);
			
			$obj->id_product        = $id_product;
			$obj->description_short = $_POST['description_short'];
			$obj->description       = $_POST['description'];
			$obj->indisponible      = $this->escape($_POST['indisponible']);
			
			$action = ($upd) ? $obj->upd() : $obj->add();
			
			if($action){
				return $obj;
			}
			
			return false;
		}
		
		public function deleteProductLang($id_product){
			$obj = new ProductsLangModels();
			$obj->deleteProduct($id_product);
		}
		
		
		public function ajaxCoefTax($id){
			$tax = new TaxModels($id);
			echo json_encode(array('coef' => $tax->valeur));
		}
		
		public function ajaxSearchClient($query){
			$result = $this->models->searchClient($this->escape($query));
			echo json_encode(array('client' => $result));
		}
		
		public function ajaxAddPromo($data){
			
			$form = $this->unserializeForm($data);
			
			$id_customer = (!empty($form['id_customer'])) ? $form['id_customer'] : 0;
			$id_group    = (!empty($form['id_group_client'])) ? $form['id_group_client'] : 0;
			
			$product = new ProductsModels((int)$form['id_product']);
			$taxe    = new TaxModels($product->id_tax);
			
			
			if(isset($form['date_from']) && isset($form['date_to']) && !empty($form['remise_id_product'])){
				
				$add = false;
				$remise_produit = str_replace(',','.', $form['remise_id_product']);
				$remise = ($form['remise_type'] == 'remise') ? $remise_produit : 0;
				
				if($form['remise_type'] != 'remise') {
					
					$prix_net_ht = $remise_produit;
					$prix_net_ttc = $remise_produit;
					
					if ($form['remise_type'] == 'prix_net_ttc') {
						$prix_net_ht /= (1 + ($taxe->valeur /100));
					} else{
						$prix_net_ttc *= (1 + ($taxe->valeur /100));
					}
					
					if ($prix_net_ht >= $product->prix_achat) {
						$add = true;
					}
				} else{
					$add = true;
					$prix_net_ht = 0;
					$prix_net_ttc = 0;
				}
				
				if($add) {
					$obj = new ProductsDiscountsModels();
					$obj->id_product   = $product->id_product;
					$obj->id_customer  = (int)$id_customer;
					$obj->id_group     = (int)$id_group;
					$obj->remise       = (float)$remise;
					$obj->prix_net_ht  = (float)$prix_net_ht;
					$obj->prix_net_ttc = (float)$prix_net_ttc;
					$obj->date_from    = $this->date($form['date_from']);
					$obj->date_to      = $this->date($form['date_to']);
					$obj->date_add     = $this->date;
					$obj->date_upd     = $this->date;
					$obj->add();
				}
			}
		}
	}