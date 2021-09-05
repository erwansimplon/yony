<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class GroupAccessModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_group_access;
		public $id_group;
		public $id_category;
		public $id_manufacturer;
		public $afficher;
		
		public $table = 'customer_group_access';
		public $id = 'id_group_access';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'GroupAccess', $id);
			}
		}
		
		public function getGroupAccessById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getCustomerAccess($type, $id_type, $id_group){
			$select = $this->select->select('afficher')
									->from($this->table)
									->where('id_group = "'.$id_group.'"',
											$type.' = "'.$id_type.'"',
											'afficher = 1');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->afficher)){
				return true;
			}
			
			return false;
		}
		
		public function getAllowGroupAccess($id_type, $id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($id_type.' = "'.$id.'"',
											'afficher = 1');
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function getAllowManufacturerByGroup($id){
			$select = $this->select->select('TRIM(TRAILING "," FROM (GROUP_CONCAT(id_manufacturer SEPARATOR ","))) as manufacturer')
									->from($this->table)
									->where('id_group = "'.$id.'"',
											'id_manufacturer > 0',
											'afficher = 1')
									->groupby('id_group');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->manufacturer)){
				return $result->manufacturer;
			}
			
			return false;
		}
		
		public function deleteByCategory($id){
			$delete = $this->delete->delete($this->table.' WHERE id_category = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function deleteByManufacturer($id){
			$delete = $this->delete->delete($this->table.' WHERE id_manufacturer = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function deleteAccessByIdGroup($id){
			$delete = $this->delete->delete($this->table.' WHERE id_group = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function loadDataInArray(){
			return array('id_group'        => $this->id_group,
						 'id_category'     => $this->id_category,
						 'id_manufacturer' => $this->id_manufacturer,
						 'afficher'        => $this->afficher);
		}
		
		/**
		 * @param mixed $id_group_access
		 */
		public function setIdGroupAccess($id_group_access)
		{
			$this->id_group_access = $id_group_access;
		}
		
		/**
		 * @param mixed $id_group
		 */
		public function setIdGroup($id_group)
		{
			$this->id_group = $id_group;
		}
		
		/**
		 * @param mixed $id_category
		 */
		public function setIdCategory($id_category)
		{
			$this->id_category = $id_category;
		}
		
		/**
		 * @param mixed $id_manufacturer
		 */
		public function setIdManufacturer($id_manufacturer)
		{
			$this->id_manufacturer = $id_manufacturer;
		}
		
		/**
		 * @param mixed $afficher
		 */
		public function setAfficher($afficher)
		{
			$this->afficher = $afficher;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_group_access.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_group_access.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}