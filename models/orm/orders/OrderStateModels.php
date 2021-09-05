<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderStateModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_state;
		public $name;
		public $background;
		public $color;
		public $delivery;
		public $invoice;
		public $credit_note;
		public $date_add;
		public $date_upd;
		
		protected $table = 'order_state';
		protected $id = 'id_order_state';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderState', $id);
			}
		}
		
		public function getOrderStateById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getStates(){
			$select = $this->select->select('*')
									->from($this->table);
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			$tabs = array('name'        => $this->name,
						  'background'  => $this->background,
						  'color'       => $this->color,
						  'delivery'    => $this->delivery,
						  'invoice'     => $this->invoice,
						  'credit_note' => $this->credit_note,
						  'date_add'    => $this->date_add,
						  'date_upd'    => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_state
		 */
		public function setIdOrderState($id_order_state)
		{
			$this->id_order_state = $id_order_state;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $background
		 */
		public function setBackground($background)
		{
			$this->background = $background;
		}
		
		/**
		 * @param mixed $color
		 */
		public function setColor($color)
		{
			$this->color = $color;
		}
		
		/**
		 * @param mixed $delivery
		 */
		public function setDelivery($delivery)
		{
			$this->delivery = $delivery;
		}
		
		/**
		 * @param mixed $invoice
		 */
		public function setInvoice($invoice)
		{
			$this->invoice = $invoice;
		}
		
		/**
		 * @param mixed $credit_note
		 */
		public function setCreditNote($credit_note)
		{
			$this->credit_note = $credit_note;
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
			
			$this->id_order_state = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_order_state.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_state.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}