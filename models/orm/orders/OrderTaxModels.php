<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderTaxModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_tax;
		public $id_order;
		public $tax_name;
		public $tax_value;
		public $amount;
		public $date_add;
		
		protected $table = 'order_tax';
		protected $id = 'id_order_tax';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderTax', $id);
			}
		}
		
		public function getOrderTaxById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrderTaxByIdOrder($id){
			
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_order = "'.$id.'"')
									->orderby(array($this->table => 'tax_value DESC'));
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function searchIdOrderTax($id_order, $tax){
			$select = $this->select->select($this->id)
									->from($this->table)
									->where('id_order = "'.$id_order.'"',
											'tax_name = "'.$tax->tax_name.'"',
											'tax_value = "'.$tax->tax_value.'"');
								
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(!empty($result->{$this->id})){
				return $result->{$this->id};
			}
			
			return false;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order'  => $this->id_order,
						  'tax_name'  => $this->tax_name,
						  'tax_value' => $this->tax_value,
						  'amount'    => $this->amount,
						  'date_add'  => $this->date_add);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_tax
		 */
		public function setIdOrderTax($id_order_tax)
		{
			$this->id_order_tax = $id_order_tax;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $tax_name
		 */
		public function setTaxName($tax_name)
		{
			$this->tax_name = $tax_name;
		}
		
		/**
		 * @param mixed $tax_value
		 */
		public function setTaxValue($tax_value)
		{
			$this->tax_value = $tax_value;
		}
		
		/**
		 * @param mixed $amount
		 */
		public function setAmount($amount)
		{
			$this->amount = $amount;
		}
		
		/**
		 * @param mixed $date_add
		 */
		public function setDateAdd($date_add)
		{
			$this->date_add = $date_add;
		}
		
		public function add(){
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_order_tax.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_tax.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}