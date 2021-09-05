<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class EmployeeModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_employees;
		public $id_profil;
		public $name;
		public $email;
		public $password;
		public $token;
		public $date_add;
		public $date_upd;
		
		protected $table = 'employees';
		protected $id = 'id_employees';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id){
				Autoloader::hydrate($this, 'Employee', $id);
			}
		}
		
		public function getEmployeeById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getEmployeeByToken($token){
			$select = $this->select->select('id_employees, id_profil, name, email')
									->from($this->table)
									->where('token = "'.$token.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		/**
		 * @param mixed $id_employees
		 */
		public function setIdEmployees($id_employees)
		{
			$this->id_employees = $id_employees;
		}
		
		/**
		 * @param mixed $id_profil
		 */
		public function setIdProfil($id_profil)
		{
			$this->id_profil = $id_profil;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $email
		 */
		public function setEmail($email)
		{
			$this->email = $email;
		}
		
		/**
		 * @param mixed $password
		 */
		public function setPassword($password)
		{
			$this->password = $password;
		}
		
		/**
		 * @param mixed $token
		 */
		public function setToken($token)
		{
			$this->token = $token;
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
		
		public function loadDataInArray(){
			$tabs = array('id_profil' => $this->id_profil,
						  'name'      => $this->name,
						  'email'     => $this->email,
						  'password'  => $this->password,
						  'token'     => $this->token,
						  'date_add'  => $this->date_add,
						  'date_upd'  => $this->date_upd);
			
			return $tabs;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
								   ->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_employees = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
								   ->set($this->loadDataInArray())
								   ->where($this->id.' = "'.$this->id_employees.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_employees.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}