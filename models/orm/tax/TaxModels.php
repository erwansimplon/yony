<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class TaxModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_tax;
		public $name;
		public $valeur;
		public $active;
		
		protected $table = 'taxes';
		protected $id = 'id_tax';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Tax', $id);
			}
		}
		
		public function getTaxById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAllTax(){
			$select = $this->select->select('*')
									->from($this->table);
			
			return $this->pdo->query('mysql', $select);
		}
		
		/**
		 * @param mixed $id_tax
		 */
		public function setIdTax($id_tax)
		{
			$this->id_tax = $id_tax;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $valeur
		 */
		public function setValeur($valeur)
		{
			$this->valeur = $valeur;
		}
		
		/**
		 * @param mixed $active
		 */
		public function setActive($active)
		{
			$this->active = $active;
		}
		
		public function loadDataInArray(){
			$tabs = array('name'   => $this->name,
						  'valeur' => $this->valeur,
						  'active' => $this->active);
			
			return $tabs;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_tax.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_tax.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}