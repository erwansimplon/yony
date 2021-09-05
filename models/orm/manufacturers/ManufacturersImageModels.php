<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ManufacturersImageModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_manufacturers_image;
		public $logo;
		public $ext;
		
		protected $id = 'id_manufacturers_image';
		protected $table = 'manufacturers_image';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ManufacturersImage', $id);
			}
		}
		
		public function getManufacturersImageById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('logo' => $this->logo,
						  'ext'  => $this->ext);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_manufacturers_image
		 */
		public function setIdManufacturersImage($id_manufacturers_image)
		{
			$this->id_manufacturers_image = $id_manufacturers_image;
		}
		
		/**
		 * @param mixed $logo
		 */
		public function setLogo($logo)
		{
			$this->logo = $logo;
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
									->where($this->id.' = "'.$this->id_manufacturers_image.'"');
								
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_manufacturers_image.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}