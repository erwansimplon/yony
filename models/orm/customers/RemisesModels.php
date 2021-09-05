<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class RemisesModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_remises;
		public $id_group;
		public $id_category;
		public $id_manufacturer;
		public $remise;
		
		public $table = 'remises';
		public $id = 'id_remises';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Remises', $id);
			}
		}
		
		public function getRemisesById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getRemiseCategoryByIdGroup($id_group){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_group = "'.$id_group.'"',
											'id_category > 0');
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function getRemiseManufacturerByIdGroup($id_group){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_group = "'.$id_group.'"',
											'id_manufacturer > 0');
			
			return $this->pdo->query('mysql', $select, true);
		}
		public function getIdByCategory($id_group, $id_category){
			$select = $this->select->select('id_remises')
									->from($this->table)
									->where('id_group = "'.$id_group.'"',
											'id_category = "'.$id_category.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->id_remises)){
				return $result->id_remises;
			}
			
			return false;
		}
		
		public function getIdByManufacturer($id_group, $id_manufacturer){
			$select = $this->select->select('id_remises')
									->from($this->table)
									->where('id_group = "'.$id_group.'"',
											'id_manufacturer = "'.$id_manufacturer.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->id_remises)){
				return $result->id_remises;
			}
			
			return false;
		}
		
		public function deleteRemisesByIdGroup($id){
			$delete = $this->delete->delete($this->table.' WHERE id_group = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function loadDataInArray(){
			return array('id_group'        => $this->id_group,
						 'id_category'     => $this->id_category,
						 'id_manufacturer' => $this->id_manufacturer,
						 'remise'          => $this->remise);
		}
		
		/**
		 * @param mixed $id_remises
		 */
		public function setIdRemises($id_remises)
		{
			$this->id_remises = $id_remises;
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
		 * @param mixed $remise
		 */
		public function setRemise($remise)
		{
			$this->remise = $remise;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_remises.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_remises.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}