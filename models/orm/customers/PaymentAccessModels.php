<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class PaymentAccessModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_payment_access;
		public $id_customer_payment;
		public $id_order_payment;
		public $afficher;
		public $date_add;
		public $date_upd;
		
		public $table = 'customer_payment_access';
		public $id = 'id_payment_access';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'PaymentAccess', $id);
			}
		}
		
		public function getPaymentAccessById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAllowPaymentAccess($id_payment){
			
			$table = array('order_payment' => 'op');
			
			$liaison = array('pa.id_order_payment' => 'op.id_order_payment');
			
			$select = $this->select->select('op.id_order_payment, op.name')
									->from($this->table, 'pa')
									->join('inner', $table, $liaison)
									->where('pa.id_customer_payment = "'.$id_payment.'"',
											'op.affichage = 2',
											'pa.afficher = 1');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function deleteAccessByIdPayment($id){
			$delete = $this->delete->delete($this->table.' WHERE id_customer_payment = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function loadDataInArray(){
			return array('id_customer_payment' => $this->id_customer_payment,
						 'id_order_payment'    => $this->id_order_payment,
						 'afficher'            => $this->afficher,
						 'date_add'            => $this->date_add,
						 'date_upd'            => $this->date_upd);
		}
		
		/**
		 * @param mixed $id_payment_access
		 */
		public function setIdPaymentAccess($id_payment_access)
		{
			$this->id_payment_access = $id_payment_access;
		}
		
		/**
		 * @param mixed $id_customer_payment
		 */
		public function setIdCustomerPayment($id_customer_payment)
		{
			$this->id_customer_payment = $id_customer_payment;
		}
		
		/**
		 * @param mixed $id_order_payment
		 */
		public function setIdOrderPayment($id_order_payment)
		{
			$this->id_order_payment = $id_order_payment;
		}
		
		/**
		 * @param mixed $afficher
		 */
		public function setAfficher($afficher)
		{
			$this->afficher = $afficher;
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
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_payment_access.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_payment_access.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}