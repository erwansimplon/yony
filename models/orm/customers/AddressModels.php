<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class AddressModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_address;
		public $id_customer;
		public $alias;
		public $lastname;
		public $voie;
		public $complement_voie;
		public $postcode;
		public $ville;
		public $phone;
		public $date_add;
		public $date_upd;
		
		protected $id = 'id_address';
		protected $table = 'adresses';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Address', $id);
			}
		}
		
		public function getAddressById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getAddressByAlias($id_customer, $alias){
			$select = $this->select->select('id_address')
									->from($this->table)
									->where('id_customer = "'.$id_customer.'"',
											'alias = "'.$alias.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return isset($result->id_address) ? $result->id_address : false;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_customer'     => $this->id_customer,
						  'alias'           => $this->alias,
						  'lastname'        => $this->lastname,
						  'voie'            => $this->voie,
						  'complement_voie' => $this->complement_voie,
						  'postcode'        => $this->postcode,
						  'ville'           => $this->ville,
						  'phone'           => $this->phone,
						  'date_add'        => $this->date_add,
						  'date_upd'        => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_address
		 */
		public function setIdAddress($id_address)
		{
			$this->id_address = $id_address;
		}
		
		/**
		 * @param mixed $id_customer
		 */
		public function setIdCustomer($id_customer)
		{
			$this->id_customer = $id_customer;
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
			
			$this->id_address = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_address.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_address.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}