<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CustomerPaymentModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_customer_payment;
		public $name;
		public $date_add;
		public $date_upd;
		
		public $table = 'customer_payment';
		public $id = 'id_customer_payment';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CustomerPayment', $id);
			}
		}
		
		public function getCustomerPaymentById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAllCustomerPayment(){
			$select = $this->select->select('*')
									->from($this->table);
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			return array('name'     => $this->name,
						 'date_add' => $this->date_add,
						 'date_upd' => $this->date_upd);
		}
		
		/**
		 * @param mixed $id_customer_payment
		 */
		public function setIdCustomerPayment($id_customer_payment)
		{
			$this->id_customer_payment = $id_customer_payment;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
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
			
			$this->id_customer_payment = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_customer_payment.'"');
								
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_customer_payment.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}