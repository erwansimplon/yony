<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderHistoryModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_history;
		public $id_order;
		public $id_employee;
		public $id_order_state;
		public $date_add;
		
		protected $table = 'order_history';
		protected $id = 'id_order_history';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderHistory', $id);
			}
		}
		
		public function getOrderHistoryById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrderHistoryByIdOrder($id){
			
			$table = array(
				'employees'   => 'e',
				'order_state' => 'os'
			);
			
			$liaison = array(
				'h.id_employee'    => 'e.id_employees',
				'h.id_order_state' => 'os.id_order_state'
			);
			
			$select = $this->select->select('h.*,
											 e.id_profil as profil_employee,
											 e.name as name_employee,
											 os.name as order_state,
											 os.background, os.color')
									->from($this->table, 'h')
									->join('left', $table, $liaison)
									->where('h.id_order = "'.$id.'"')
									->orderby(array('h' => 'date_add DESC'));
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order'       => $this->id_order,
						  'id_employee'    => $this->id_employee,
						  'id_order_state' => $this->id_order_state,
						  'date_add'       => $this->date_add);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_history
		 */
		public function setIdOrderHistory($id_order_history)
		{
			$this->id_order_history = $id_order_history;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $id_employee
		 */
		public function setIdEmployee($id_employee)
		{
			$this->id_employee = $id_employee;
		}
		
		/**
		 * @param mixed $id_order_state
		 */
		public function setIdOrderState($id_order_state)
		{
			$this->id_order_state = $id_order_state;
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
									->where($this->id.' = "'.$this->id_order_history.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_history.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}