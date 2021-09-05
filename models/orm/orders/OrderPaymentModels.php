<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderPaymentModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_payment;
		public $name;
		public $affichage;
		public $date_add;
		public $date_upd;
		
		protected $table = 'order_payment';
		protected $id = 'id_order_payment';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderPayment', $id);
			}
		}
		
		public function getOrderPaymentById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getPayments(){
			$select = $this->select->select('*')
									->from($this->table)
									->where('affichage > 0');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			$tabs = array('name'      => $this->name,
						  'affichage' => $this->affichage,
						  'date_add'  => $this->date_add,
						  'date_upd'  => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_payment
		 */
		public function setIdOrderPayment($id_order_payment)
		{
			$this->id_order_payment = $id_order_payment;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $affichage
		 */
		public function setAffichage($affichage)
		{
			$this->affichage = $affichage;
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
			
			$this->id_order_payment = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_order_payment.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_payment.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}