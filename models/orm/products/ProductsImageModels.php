<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsImageModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_products_image;
		public $product_id;
		public $cover;
		public $image;
		public $ext;
		
		protected $id = 'id_products_image';
		protected $table = 'products_image';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ProductsImage', $id);
			}
		}
		
		public function getProductsImageById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getImageByIdProduct($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('product_id = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getImageCoverExist($id_product){
			$select = $this->select->select($this->id)
									->from($this->table)
									->where('product_id = "'.$id_product.'"',
											'cover = 1');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if($result){
				return 0;
			}
			
			return 1;
		}
		
		public function resetCoverImage($id_product){
			$update = $this->update->update($this->table)
									->set(array('cover' => 0))
									->where('product_id = "'.$id_product.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function changeCoverImage($id_image){
			$update = $this->update->update($this->table)
									->set(array('cover' => 1))
									->where($this->id.' = "'.$id_image.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function loadDataInArray(){
			$tabs = array('product_id' => $this->product_id,
						  'cover'      => $this->cover,
						  'image'      => $this->image,
						  'ext'        => $this->ext);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_products_image
		 */
		public function setIdProductsImage($id_products_image)
		{
			$this->id_products_image = $id_products_image;
		}
		
		/**
		 * @param mixed $product_id
		 */
		public function setProductId($product_id)
		{
			$this->product_id = $product_id;
		}
		
		/**
		 * @param mixed $cover
		 */
		public function setCover($cover)
		{
			$this->cover = $cover;
		}
		
		/**
		 * @param mixed $image
		 */
		public function setImage($image)
		{
			$this->image = $image;
		}
		
		/**
		 * @param mixed $ext
		 */
		public function setExt($ext)
		{
			$this->ext = $ext;
		}
		
		
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_manufacturers_image = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_products_image.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_products_image.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}