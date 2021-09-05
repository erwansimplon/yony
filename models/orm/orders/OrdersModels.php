<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrdersModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order;
		public $reference;
		public $id_cart;
		public $id_customer;
		public $id_payment;
		public $current_state;
		public $total_product;
		public $total_excl;
		public $total_incl;
		public $date_add;
		public $date_upd;
		
		protected $table = 'orders';
		protected $id = 'id_order';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Orders', $id);
			}
		}
		
		public function getOrdersById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrdersByIdOrder($id){
			
			$table = array(
				'clients'         => 'c',
				'customer_gender' => 'cg',
				'order_payment'   => 'op',
				'order_state'     => 'os'
			);
			
			$liaison = array(
				'o.id_customer'   => 'c.id_customer',
				'c.id_gender'     => 'cg.id_gender',
				'o.id_payment'    => 'op.id_order_payment',
				'o.current_state' => 'os.id_order_state'
			);
			
			$select = $this->select->select('o.*,
											 CONCAT(cg.name," ", c.name) as customer_name,
											 c.email as customer_email,
											 c.date_add as customer_add,
											 op.name as payment_name,
											 os.name as state_name,
											 os.background, os.color,
											 os.delivery, os.invoice, os.credit_note')
									->from($this->table, 'o')
									->join('inner', $table, $liaison)
									->where('o.id_order = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, true, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('reference'           => $this->reference,
						  'id_cart'             => $this->id_cart,
						  'id_customer'         => $this->id_customer,
						  'id_payment'          => $this->id_payment,
						  'current_state'       => $this->current_state,
						  'total_product'       => $this->total_product,
						  'total_excl'          => $this->total_excl,
						  'total_incl'          => $this->total_incl,
						  'date_add'            => $this->date_add,
						  'date_upd'            => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $reference
		 */
		public function setReference()
		{
			return 'CDE'.substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1, 10))), 0, 8);
		}
		
		/**
		 * @param mixed $id_cart
		 */
		public function setIdCart($id_cart)
		{
			$this->id_cart = $id_cart;
		}
		
		/**
		 * @param mixed $id_customer
		 */
		public function setIdCustomer($id_customer)
		{
			$this->id_customer = $id_customer;
		}
		
		/**
		 * @param mixed $current_state
		 */
		public function setCurrentState($current_state)
		{
			$this->current_state = $current_state;
		}
		
		/**
		 * @param mixed $id_payment
		 */
		public function setIdPayment($payement)
		{
			$this->id_payement = $payement;
		}
		
		/**
		 * @param mixed $total_product
		 */
		public function setTotalProduct($total_product)
		{
			$this->total_product = $total_product;
		}
		
		/**
		 * @param mixed $total_excl
		 */
		public function setTotalExcl($total_excl)
		{
			$this->total_excl = $total_excl;
		}
		
		/**
		 * @param mixed $total_incl
		 */
		public function setTotalIncl($total_incl)
		{
			$this->total_incl = $total_incl;
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
			
			$this->id_order = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_order.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}