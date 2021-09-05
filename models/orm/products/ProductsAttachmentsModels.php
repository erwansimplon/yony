<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsAttachmentsModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_products_attachments;
		public $id_product;
		public $pdf_file;
		
		protected $id = 'id_products_attachments';
		protected $table = 'products_attachments';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ProductsAttachments', $id);
			}
		}
		
		public function getProductsAttachmentsById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
								
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAttachmentsByIdProduct($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_product = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_product' => $this->id_product,
						  'pdf_file'   => $this->pdf_file);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_products_attachments
		 */
		public function setIdProductsAttachments($id_products_attachments)
		{
			$this->id_products_attachments = $id_products_attachments;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $pdf_file
		 */
		public function setPdfFile($pdf_file)
		{
			$this->pdf_file = $pdf_file;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_products_attachments = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_products_attachments.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_products_attachments.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
	}