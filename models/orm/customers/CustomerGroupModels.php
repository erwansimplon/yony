<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CustomerGroupModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_group;
		public $id_tva;
		public $name_group;
		public $display_price;
		
		public $table = 'customer_group';
		public $id = 'id_group';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CustomerGroup', $id);
			}
		}
		
		public function getCustomerGroupById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAllCustomerGroup(){
			$select = $this->select->select('*')
									->from($this->table);
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getCustomerGroupExcluded($where = false){
			$select = $this->select->select('*')
									->from($this->table);
			
			if($where){
				$select = $select->where('id_group NOT IN('.$where.')');
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_tva'        => $this->id_tva,
					      'name_group'    => $this->name_group,
						  'display_price' => $this->display_price);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_group
		 */
		public function setIdGroup($id_group)
		{
			$this->id_group = $id_group;
		}
		
		/**
		 * @param mixed $id_tva
		 */
		public function setIdTva($id_tva)
		{
			$this->id_tva = $id_tva;
		}
		
		/**
		 * @param mixed $name_group
		 */
		public function setNameGroup($name_group)
		{
			$this->name_group = $name_group;
		}
		
		/**
		 * @param mixed $display_price
		 */
		public function setDisplayPrice($display_price)
		{
			$this->display_price = $display_price;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_group = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_group.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_group.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}