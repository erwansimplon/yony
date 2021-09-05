<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class OrderAddressModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_order_address;
		public $id_order;
		public $alias;
		public $lastname;
		public $voie;
		public $complement_voie;
		public $postcode;
		public $ville;
		public $phone;
		public $date_add;
		
		protected $id = 'id_order_address';
		protected $table = 'order_address';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'OrderAddress', $id);
			}
		}
		
		public function getOrderAddressById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getOrderAddressByIdOrder($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_order = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_order'        => $this->id_order,
						  'alias'           => $this->alias,
						  'lastname'        => $this->lastname,
						  'voie'            => $this->voie,
						  'complement_voie' => $this->complement_voie,
						  'postcode'        => $this->postcode,
						  'ville'           => $this->ville,
						  'phone'           => $this->phone,
						  'date_add'        => $this->date_add);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_order_address
		 */
		public function setIdOrderAddress($id_order_address)
		{
			$this->id_order_address = $id_order_address;
		}
		
		/**
		 * @param mixed $id_order
		 */
		public function setIdOrder($id_order)
		{
			$this->id_order = $id_order;
		}
		
		/**
		 * @param mixed $alias
		 */
		public function setAlias($alias)
		{
			$this->alias = $alias;
		}
		
		/**
		 * @param mixed $lastname
		 */
		public function setLastname($lastname)
		{
			$this->lastname = $lastname;
		}
		
		/**
		 * @param mixed $voie
		 */
		public function setVoie($voie)
		{
			$this->voie = $voie;
		}
		
		/**
		 * @param mixed $complement_voie
		 */
		public function setComplementVoie($complement_voie)
		{
			$this->complement_voie = $complement_voie;
		}
		
		/**
		 * @param mixed $postcode
		 */
		public function setPostcode($postcode)
		{
			$this->postcode = $postcode;
		}
		
		/**
		 * @param mixed $ville
		 */
		public function setVille($ville)
		{
			$this->ville = $ville;
		}
		
		/**
		 * @param mixed $phone
		 */
		public function setPhone($phone)
		{
			$this->phone = $phone;
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
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_address = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_order_address.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_order_address.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}