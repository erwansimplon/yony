<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsFeaturesLangModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_products_features_lang;
		public $id_products_features;
		public $id_product;
		public $value;
		
		protected $id = 'id_products_features_lang';
		protected $table = 'products_features_lang';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ProductsFeaturesLang', $id);
			}
		}
		
		public function getProductsFeaturesLangById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
								
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getFeaturesLangByIdProduct($id){
			
			$table = array('products_features' => 'pf');
			
			$liaison = array('pfl.id_products_features' => 'pf.id_products_features');
			
			$select = $this->select->select('pfl.*, pf.type')
									->from($this->table,'pfl')
									->join('inner', $table, $liaison)
									->where('pfl.id_product = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getRestProductInFeature($id){
			
			$table = array('products_features' => 'pf');
			
			$liaison = array('pfl.id_products_features' => 'pf.id_products_features');
			
			$select = $this->select->select('pfl.*, pf.type')
									->from($this->table,'pfl')
									->join('inner', $table, $liaison)
									->where('pfl.id_products_features = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getViewIdFeatureInProduct($id){
			
			$table = array('products_features' => 'pf');
			
			$liaison = array('pfl.id_products_features' => 'pf.id_products_features');
			
			$select = $this->select->select('TRIM(TRAILING "," FROM (GROUP_CONCAT(pfl.id_products_features SEPARATOR ","))) as id_features')
									->from($this->table,'pfl')
									->join('inner', $table, $liaison)
									->where('pfl.id_product = "'.$id.'"')
									->groupby('pfl.id_product');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->id_features)){
				return $result->id_features;
			}
			
			return false;
		}
		
		public function getAllowFeatures($id){
			
			$table = array('products_features' => 'pf');
			
			$liaison = array('pfl.id_products_features' => 'pf.id_products_features');
			
			$deny_feature = $this->getViewIdFeatureInProduct($id);
			
			$select = $this->select->select('pfl.*, pf.type')
									->from($this->table,'pfl')
									->join('inner', $table, $liaison)
									->groupby('pfl.id_products_features');
			if($deny_feature){
				$select = $this->select->where('pfl.id_products_features NOT IN('.$deny_feature.')');
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_products_features' => $this->id_products_features,
						  'id_product'			 => $this->id_product,
						  'value'                => $this->value);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_products_features_lang
		 */
		public function setIdProductsFeaturesLang($id_products_features_lang)
		{
			$this->id_products_features_lang = $id_products_features_lang;
		}
		
		/**
		 * @param mixed $id_products_features
		 */
		public function setIdProductsFeatures($id_products_features)
		{
			$this->id_products_features = $id_products_features;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $value
		 */
		public function setValue($value)
		{
			$this->value = $value;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_products_features_lang = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_products_features_lang.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_products_features_lang.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
	}