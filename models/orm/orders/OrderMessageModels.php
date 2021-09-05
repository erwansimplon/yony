<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderMessageModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_message;
		public $id_order;
		public $id_employee;
		public $private;
		public $message;
		public $date_add;
		public $date_upd;
		
		protected $table = 'order_message';
		protected $id = 'id_order_message';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderMessage', $id);
			}
		}
		
		public function getOrderMessageById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrderMessageByIdOrder($id){
			
			$table = array('employees' => 'e');
			
			$liaison = array('m.id_employee' => 'e.id_employees AND private = 0');
			
			$select = $this->select->select('m.*,
											 e.id_profil as profil_employee,
											 e.name as name_employee')
									->from($this->table, 'm')
									->join('left', $table, $liaison)
									->where('m.id_order = "'.$id.'"')
									->orderby(array('m' => 'date_add ASC'));
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order'    => $this->id_order,
						  'id_employee' => $this->id_employee,
						  'private'     => $this->private,
						  'message'     => $this->message,
						  'date_add'    => $this->date_add,
						  'date_upd'    => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_message
		 */
		public function setIdOrderMessage($id_order_message)
		{
			$this->id_order_message = $id_order_message;
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
		 * @param mixed $private
		 */
		public function setPrivate($private)
		{
			$this->private = $private;
		}
		
		/**
		 * @param mixed $message
		 */
		public function setMessage($message)
		{
			$this->message = $message;
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
									->where($this->id.' = "'.$this->id_order_message.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_message.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}