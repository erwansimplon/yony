<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderCreditNoteModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_credit_note;
		public $id_order;
		public $number;
		public $date_add;
		
		protected $table = 'order_credit_note';
		protected $id = 'id_order_credit_note';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderCreditNote', $id);
			}
		}
		
		public function getOrderCreditNoteById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order' => $this->id_order,
						  'number'   => $this->number,
						  'date_add' => $this->date_add);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_credit_note
		 */
		public function setIdOrderCreditNote($id_order_credit_note)
		{
			$this->id_order_credit_note = $id_order_credit_note;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $number
		 */
		public function setNumber($number)
		{
			$this->number = $number;
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
									->where($this->id.' = "'.$this->id_order_credit_note.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_credit_note.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}