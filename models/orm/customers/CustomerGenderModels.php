<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CustomerGenderModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_gender;
		public $name;
		
		public $id = 'id_gender';
		public $table = 'customer_gender';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CustomerGender', $id);
			}
		}
		
		public function getCustomerGenderById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAllCustomerGender(){
			$select = $this->select->select('*')
									->from($this->table);
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			return array('name' => $this->name);
		}
		
		/**
		 * @param mixed $id_gender
		 */
		public function setIdGender($id_gender)
		{
			$this->id_gender = $id_gender;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_gender = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_gender.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_gender.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}