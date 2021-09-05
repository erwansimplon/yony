<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CustomersModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_customer;
		public $id_group;
		public $id_payment;
		public $id_gender;
		public $active;
		public $name;
		public $email;
		public $password;
		public $date_add;
		public $date_upd;
		
		public $table = 'clients';
		public $id = 'id_customer';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Customers', $id);
			}
		}
		
		public function getCustomersById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getCustomersActif(){
			$select = $this->select->select('*')
									->from($this->table)
									->where('active = 1');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			return array(
				'id_group'   => $this->id_group,
				'id_payment' => $this->id_payment,
				'id_gender'  => $this->id_gender,
				'active'     => $this->active,
				'name'       => $this->name,
				'email'      => $this->email,
				'password'   => $this->password,
				'date_add'   => $this->date_add,
				'date_upd'   => $this->date_upd
			);
		}
		
		/**
		 * @param mixed $id_customer
		 */
		public function setIdCustomer($id_customer)
		{
			$this->id_customer = $id_customer;
		}
		
		/**
		 * @param mixed $id_group
		 */
		public function setIdGroup($id_group)
		{
			$this->id_group = $id_group;
		}
		
		/**
		 * @param mixed $id_payment
		 */
		public function setIdPayment($id_payment)
		{
			$this->id_payment = $id_payment;
		}
		
		/**
		 * @param mixed $id_gender
		 */
		public function setIdGender($id_gender)
		{
			$this->id_gender = $id_gender;
		}
		
		/**
		 * @param mixed $active
		 */
		public function setActive($active)
		{
			$this->active = $active;
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
			
			$this->id_customer = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
								   ->set($this->loadDataInArray())
								   ->where($this->id.' = "'.$this->id_customer.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_customer.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}