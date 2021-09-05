<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CarrierModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_carrier;
		public $id_tax;
		public $name;
		public $delay;
		public $suivi;
		public $active;
		public $free_carrier;
		public $facturation;
		public $date_add;
		public $date_upd;
		
		public $table = 'carrier';
		public $id = 'id_carrier';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Carrier', $id);
			}
		}
		
		public function getCarrierById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
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
		public function getIdTax()
		{
			return $this->id_tax;
		}
		
		/**
		 * @param mixed $id_tax
		 */
		public function setIdTax($id_tax)
		{
			$this->id_tax = $id_tax;
		}
		
		/**
		 * @return mixed
		 */
		public function getName()
		{
			return $this->name;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @return mixed
		 */
		public function getDelay()
		{
			return $this->delay;
		}
		
		/**
		 * @param mixed $delay
		 */
		public function setDelay($delay)
		{
			$this->delay = $delay;
		}
		
		/**
		 * @return mixed
		 */
		public function getSuivi()
		{
			return $this->suivi;
		}
		
		/**
		 * @param mixed $suivi
		 */
		public function setSuivi($suivi)
		{
			$this->suivi = $suivi;
		}
		
		/**
		 * @return mixed
		 */
		public function getActive()
		{
			return $this->active;
		}
		
		/**
		 * @param mixed $active
		 */
		public function setActive($active)
		{
			$this->active = $active;
		}
		
		/**
		 * @return mixed
		 */
		public function getFreeCarrier()
		{
			return $this->free_carrier;
		}
		
		/**
		 * @param mixed $free_carrier
		 */
		public function setFreeCarrier($free_carrier)
		{
			$this->free_carrier = $free_carrier;
		}
		
		/**
		 * @return mixed
		 */
		public function getFacturation()
		{
			return $this->facturation;
		}
		
		/**
		 * @param mixed $facturation
		 */
		public function setFacturation($facturation)
		{
			$this->facturation = $facturation;
		}
		
		/**
		 * @return mixed
		 */
		public function getDateAdd()
		{
			return $this->date_add;
		}
		
		/**
		 * @param mixed $date_add
		 */
		public function setDateAdd($date_add)
		{
			$this->date_add = $date_add;
		}
		
		/**
		 * @return mixed
		 */
		public function getDateUpd()
		{
			return $this->date_upd;
		}
		
		/**
		 * @param mixed $date_upd
		 */
		public function setDateUpd($date_upd)
		{
			$this->date_upd = $date_upd;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_tax'       => $this->id_tax,
						  'name'         => $this->name,
						  'delay'        => $this->delay,
						  'suivi'        => $this->suivi,
						  'active'       => $this->active,
						  'free_carrier' => $this->free_carrier,
						  'facturation'  => $this->facturation,
						  'date_add'     => $this->date_add,
						  'date_upd'     => $this->date_upd);
			
			return $tabs;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			$this->id_carrier = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_carrier.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_carrier.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}