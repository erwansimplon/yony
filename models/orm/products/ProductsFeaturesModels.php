<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsFeaturesModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_products_features;
		public $type;
		
		protected $id = 'id_products_features';
		protected $table = 'products_features';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ProductsFeatures', $id);
			}
		}
		
		public function getProductsFeaturesById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('type' => $this->type);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_products_features
		 */
		public function setIdProductsFeatures($id_products_features)
		{
			$this->id_products_features = $id_products_features;
		}
		
		/**
		 * @param mixed $type
		 */
		public function setType($type)
		{
			$this->type = $type;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_products_features = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_products_features.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_products_features.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
	}