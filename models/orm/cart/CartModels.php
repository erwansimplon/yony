<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CartModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_cart;
		public $used;
		public $id_carrier;
		public $id_customer;
		public $id_address_delivery;
		public $id_cart_delivery;
		public $id_address_invoice;
		public $total_ht;
		public $total_ecotax;
		public $total_shipping_ht;
		public $weight;
		public $date_add;
		public $date_upd;
		
		protected $table = 'cart';
		protected $id = 'id_cart';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Cart', $id);
			}
		}
		
		public function getCartById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getCartByIdCart($id_cart){
			
			$table = array(
				'clients'         => 'cli',
				'customer_gender' => 'cg',
			);
			
			$liaison = array(
				'c.id_customer' => 'cli.id_customer',
				'cli.id_gender' => 'cg.id_gender',
			);
			
			$select = $this->select->select('c.*, CONCAT(cg.name," ", cli.name) as customer_name,
											 cli.email as customer_email,
											 cli.date_add as customer_add')
									->from($this->table, 'c')
									->join('inner', $table, $liaison)
									->where('c.id_cart = "'.$id_cart.'"');
			
			return $this->pdo->query('mysql', $select, true, true);
		}
		
		public function searchAvailableCarrier($id_group, $cart){
			
			$table = array('carrier_access' => 'ca',
						   'carrier_tranche' => 'ct');
			
			$liaison = array('(ca.id_carrier' => 'c.id_carrier
							   AND ca.id_group = "'.$id_group.'"
							   AND ca.afficher = 1)',
							 'ct.id_carrier'  => 'c.id_carrier');
			
			$select = $this->select->select('c.id_carrier, c.name, c.delay,
											 "'.$cart->id_carrier.'" as default_carrier,
											 IF(c.free_carrier = 1, 0, ct.price) as price')
									->from('carrier', 'c')
									->join('left', $table, $liaison)
									->where('c.free_carrier = 1
											 OR (ct.val_mini <= IF(c.facturation = 2, "'.$cart->weight.'", "'.$cart->total_ht.'")',
											'ct.val_maxi >= IF(c.facturation = 2, "'.$cart->weight.'", "'.$cart->total_ht.'"))')
									->orderby(array('ct' => 'price ASC'));
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function priceCarrier($cart){
			
			$table = array('carrier_tranche' => 'ct');
			
			$liaison = array('ct.id_carrier'  => '"'.$cart->id_carrier.'"');
			
			$select = $this->select->select('IF(c.free_carrier = 1, 0, ct.price) as price')
									->from('carrier', 'c')
									->join('left', $table, $liaison)
									->where('c.id_carrier = "'.$cart->id_carrier.'"',
											'(c.free_carrier = 1
											 OR (ct.val_mini <= IF(c.facturation = 2, "'.$cart->weight.'", "'.$cart->total_ht.'")',
											'ct.val_maxi >= IF(c.facturation = 2, "'.$cart->weight.'", "'.$cart->total_ht.'")))')
									->groupby('c.id_carrier');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(!empty($result->price)){
				return $result->price;
			}
			
			return 0;
		}
		
		public function taxShippingCart(){
			
			$table = array('taxes' => 't');
			
			$liaison = array('c.id_tax' => 't.id_tax');
			
			$select = $this->select->select('ROUND(('.$this->total_shipping_ht.' * (t.valeur /100)), 2) as total_tax')
									->from('carrier', 'c')
									->join('inner', $table, $liaison)
									->where('c.id_carrier = "'.$this->id_carrier.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(!empty($result->total_tax)){
				return $result->total_tax;
			}
			
			return 0;
		}
		
		public function taxShippingCartDetails(){
			$table = array('taxes' => 't');
			
			$liaison = array('c.id_tax' => 't.id_tax');
			
			$select = $this->select->select('c.id_tax, t.name as tax_name, t.valeur as tax_value,
											 ROUND(('.$this->total_shipping_ht.' * (t.valeur /100)), 2) as amount')
									->from('carrier', 'c')
									->join('inner', $table, $liaison)
									->where('c.id_carrier = "'.$this->id_carrier.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('used'                => $this->used,
						  'id_carrier'          => $this->id_carrier,
						  'id_customer'         => $this->id_customer,
						  'id_address_delivery' => $this->id_address_delivery,
						  'id_cart_delivery'    => $this->id_cart_delivery,
						  'id_address_invoice'  => $this->id_address_invoice,
						  'total_ht'            => $this->total_ht,
						  'total_ecotax'        => $this->total_ecotax,
						  'total_shipping_ht'   => $this->total_shipping_ht,
						  'weight'              => $this->weight,
						  'date_add'            => $this->date_add,
						  'date_upd'            => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_cart
		 */
		public function setIdCart($id_cart)
		{
			$this->id_cart = $id_cart;
		}
		
		/**
		 * @param mixed $used
		 */
		public function setUsed($used)
		{
			$this->used = $used;
		}
		
		/**
		 * @param mixed $id_carrier
		 */
		public function setIdCarrier($id_carrier)
		{
			$this->id_carrier = $id_carrier;
		}
		
		/**
		 * @param mixed $id_customer
		 */
		public function setIdCustomer($id_customer)
		{
			$this->id_customer = $id_customer;
		}
		
		/**
		 * @param mixed $id_address_delivery
		 */
		public function setIdAddressDelivery($id_address_delivery)
		{
			$this->id_address_delivery = $id_address_delivery;
		}
		
		/**
		 * @param mixed $id_cart_delivery
		 */
		public function setIdCartDelivery($id_cart_delivery)
		{
			$this->id_cart_delivery = $id_cart_delivery;
		}
		
		/**
		 * @param mixed $id_address_invoice
		 */
		public function setIdAddressInvoice($id_address_invoice)
		{
			$this->id_address_invoice = $id_address_invoice;
		}
		
		/**
		 * @param mixed $total_ht
		 */
		public function setTotalHt($total_ht)
		{
			$this->total_ht = $total_ht;
		}
		
		/**
		 * @param mixed $total_ecotax
		 */
		public function setTotalEcotax($total_ecotax)
		{
			$this->total_ecotax = $total_ecotax;
		}
		
		/**
		 * @param mixed $total_shipping_ht
		 */
		public function setTotalShippingHt($total_shipping_ht)
		{
			$this->total_shipping_ht = $total_shipping_ht;
		}
		
		/**
		 * @param mixed $weight
		 */
		public function setWeight($weight)
		{
			$this->weight = $weight;
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
			
			$this->id_cart = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_cart.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_cart.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}