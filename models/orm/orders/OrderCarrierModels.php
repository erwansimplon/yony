<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderCarrierModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_carrier;
		public $id_order;
		public $id_carrier;
		public $weight;
		public $shipping_excl;
		public $shipping_incl;
		public $tracking_number;
		public $date_add;
		public $date_upd;
		
		protected $table = 'order_carrier';
		protected $id = 'id_order_carrier';
		
		public function __construct($id = false, $id_order = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderCarrier', $id);
			}
			
			if($id_order){
				Autoloader::hydrate($this, 'Carrier', $id_order, 'Order');
			}
		}
		
		public function getOrderCarrierById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getCarrierByIdOrder($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_order = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrderCarrierByIdOrder($id){

			$table = array('carrier' => 'c');
			
			$liaison = array('oc.id_carrier' => 'c.id_carrier');
			
			$select = $this->select->select('*')
									->from($this->table, 'oc')
									->join('inner', $table, $liaison)
									->where('oc.id_order = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, true, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order'        => $this->id_order,
						  'id_carrier'      => $this->id_carrier,
						  'weight'          => $this->weight,
						  'shipping_excl'   => $this->shipping_excl,
						  'shipping_incl'   => $this->shipping_incl,
						  'tracking_number' => $this->tracking_number,
						  'date_add'        => $this->date_add,
						  'date_upd'        => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_carrier
		 */
		public function setIdOrderCarrier($id_order_carrier)
		{
			$this->id_order_carrier = $id_order_carrier;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $id_carrier
		 */
		public function setIdCarrier($id_carrier)
		{
			$this->id_carrier = $id_carrier;
		}
		
		/**
		 * @param mixed $weight
		 */
		public function setWeight($weight)
		{
			$this->weight = $weight;
		}
		
		/**
		 * @param mixed $shipping_excl
		 */
		public function setShippingExcl($shipping_excl)
		{
			$this->shipping_excl = $shipping_excl;
		}
		
		/**
		 * @param mixed $shipping_incl
		 */
		public function setShippingIncl($shipping_incl)
		{
			$this->shipping_incl = $shipping_incl;
		}
		
		/**
		 * @param mixed $tracking_number
		 */
		public function setTrackingNumber($tracking_number)
		{
			$this->tracking_number = $tracking_number;
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
									->where($this->id.' = "'.$this->id_order_carrier.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_carrier.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}