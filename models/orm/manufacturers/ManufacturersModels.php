<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ManufacturersModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_manufacturer;
		public $id_image;
		public $name;
		public $active;
		public $date_add;
		public $date_upd;
		
		protected $id = 'id_manufacturer';
		protected $table = 'manufacturers';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Manufacturers', $id);
			}
		}
		
		public function getManufacturersById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getManufacturersActive($access = false){
			$select = $this->select->select('*')
									->from($this->table)
									->where('active = 1')
									->orderby(array($this->table => 'name ASC'));
			
			if($access){
				$select = $this->select->where('id_manufacturer IN('.$access.')');
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getClearBddById($id){
			
			$table = array('manufacturers_image' => 'img');
			
			$liaison = array('m.id_image' => 'img.id_manufacturers_image');
			
			$select = $this->select->select('*')
									->from($this->table, 'm')
									->join('left', $table, $liaison)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_image'  => $this->id_image,
						  'name'      => $this->name,
						  'active'    => $this->active,
						  'date_add'  => $this->date_add,
						  'date_upd'  => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_manufacturer
		 */
		public function setIdManufacturer($id_manufacturer)
		{
			$this->id_manufacturer = $id_manufacturer;
		}
		
		/**
		 * @param mixed $id_image
		 */
		public function setIdImage($id_image)
		{
			$this->id_image = $id_image;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $active
		 */
		public function setActive($active)
		{
			$this->active = $active;
		}
		
		/**
		 * @param mixed $date_add
		 */
		public function setDateAdd($date_add)
		{
			$this->date_add = $date_add;
		}
		
		/**
		 * @param mixed $date_upd
		 */
		public function setDateUpd($date_upd)
		{
			$this->date_upd = $date_upd;
		}
		
		
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
								   ->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_manufacturer = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function add_image($data){
			$insert = $this->insert->insert('manufacturers_image', $data)
								   ->value($data);
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
								   ->set($this->loadDataInArray())
								   ->where($this->id.' = "'.$this->id_manufacturer.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_manufacturer.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}