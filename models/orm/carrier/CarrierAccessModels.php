<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CarrierAccessModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_carrier_access;
		public $id_group;
		public $id_carrier;
		public $afficher;
		
		public $table = 'carrier_access';
		public $id = 'id_carrier_access';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CarrierAccess', $id);
			}
		}
		
		public function getCarrierAccessById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAllowGroupAccess($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_carrier = "'.$id.'"',
											'afficher = "1"');
								
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function deleteByCarrier($id){
			$delete = $this->delete->delete($this->table.' WHERE id_carrier = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		/**
		 * @return mixed
		 */
		public function getIdCarrierAccess()
		{
			return $this->id_carrier_access;
		}
		
		/**
		 * @param mixed $id_carrier_access
		 */
		public function setIdCarrierAccess($id_carrier_access)
		{
			$this->id_carrier_access = $id_carrier_access;
		}
		
		/**
		 * @return mixed
		 */
		public function getIdGroup()
		{
			return $this->id_group;
		}
		
		/**
		 * @param mixed $id_group
		 */
		public function setIdGroup($id_group)
		{
			$this->id_group = $id_group;
		}
		
		/**
		 * @return mixed
		 */
		public function getIdCarrier()
		{
			return $this->id_carrier;
		}
		
		/**
		 * @param mixed $id_carrier
		 */
		public function setIdCarrier($id_carrier)
		{
			$this->id_carrier = $id_carrier;
		}
		
		/**
		 * @return mixed
		 */
		public function getAfficher()
		{
			return $this->afficher;
		}
		
		/**
		 * @param mixed $afficher
		 */
		public function setAfficher($afficher)
		{
			$this->afficher = $afficher;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_group'   => $this->id_group,
						  'id_carrier' => $this->id_carrier,
						  'afficher'   => $this->afficher);
			
			return $tabs;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			return $this->pdo->exec('mysql', $insert);
		}
		
		public function upd($id){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete($id){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}