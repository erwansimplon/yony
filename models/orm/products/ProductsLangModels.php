<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsLangModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_product_lang;
		public $id_product;
		public $description_short;
		public $description;
		public $indisponible;
		
		protected $table = 'products_lang';
		protected $id = 'id_product_lang';
		
		public function __construct($id_product = false, $id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ProductsLang', $id);
			}
			
			if ($id_product) {
				Autoloader::hydrate($this, 'ProductsLang', $id_product, 'Product');
			}
		}
		
		public function getProductsLangById($id){
			$select = $this->select->select('*')
								   ->from($this->table)
								   ->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getProductsLangByIdProduct($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_product = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_product'        => $this->id_product,
						  'description_short' => $this->description_short,
						  'description'       => $this->description,
						  'indisponible'      => $this->indisponible);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_product_lang
		 */
		public function setIdProductLang($id_product_lang)
		{
			$this->id_product_lang = $id_product_lang;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $description_short
		 */
		public function setDescriptionShort($description_short)
		{
			$this->description_short = $description_short;
		}
		
		/**
		 * @param mixed $description
		 */
		public function setDescription($description)
		{
			$this->description = $description;
		}
		
		/**
		 * @param mixed $indisponible
		 */
		public function setIndisponible($indisponible)
		{
			$this->indisponible = $indisponible;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
								   ->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		
		public function upd(){
			$update = $this->update->update($this->table)
								   ->set($this->loadDataInArray())
								   ->where($this->id.' = "'.$this->id_product_lang.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_product_lang.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function deleteProduct($id){
			$delete = $this->delete->delete($this->table.' WHERE id_product = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}