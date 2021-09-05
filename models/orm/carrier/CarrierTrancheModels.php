<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CarrierTrancheModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_carrier_tranche;
		public $id_carrier;
		public $val_mini;
		public $val_maxi;
		public $price;
		
		public $table = 'carrier_tranche';
		public $id = 'id_carrier_tranche';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CarrierTranche', $id);
			}
		}
		
		public function getCarrierTrancheById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getTrancheByIdCarrier($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_carrier = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function deleteByCarrier($id){
			$delete = $this->delete->delete($this->table.' WHERE id_carrier = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		/**
		 * @return mixed
		 */
		public function getIdCarrierTranche()
		{
			return $this->id_carrier_tranche;
		}
		
		/**
		 * @param mixed $id_carrier_tranche
		 */
		public function setIdCarrierTranche($id_carrier_tranche)
		{
			$this->id_carrier_tranche = $id_carrier_tranche;
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
		public function getValMini()
		{
			return $this->val_mini;
		}
		
		/**
		 * @param mixed $val_mini
		 */
		public function setValMini($val_mini)
		{
			$this->val_mini = $val_mini;
		}
		
		/**
		 * @return mixed
		 */
		public function getValMaxi()
		{
			return $this->val_maxi;
		}
		
		/**
		 * @param mixed $val_maxi
		 */
		public function setValMaxi($val_maxi)
		{
			$this->val_maxi = $val_maxi;
		}
		
		/**
		 * @return mixed
		 */
		public function getPrice()
		{
			return $this->price;
		}
		
		/**
		 * @param mixed $price
		 */
		public function setPrice($price)
		{
			$this->price = $price;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_carrier' => $this->id_carrier,
						  'val_mini'   => $this->val_mini,
						  'val_maxi'   => $this->val_maxi,
						  'price'      => $this->price);
			
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