<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CartDeliveryModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_cart_delivery;
		public $id_cart;
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
		
		protected $table = 'cart_delivery';
		protected $id = 'id_cart_delivery';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CartDelivery', $id);
			}
		}
		
		public function getCartDeliveryById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_cart'         => $this->id_cart,
						  'id_customer'     => $this->id_customer,
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
		 * @param mixed $id_cart_delivery
		 */
		public function setIdCartDelivery($id_cart_delivery)
		{
			$this->id_cart_delivery = $id_cart_delivery;
		}
		
		/**
		 * @param mixed $id_cart
		 */
		public function setIdCart($id_cart)
		{
			$this->id_cart = $id_cart;
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
			
			$this->id_cart_delivery = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_cart_delivery.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_cart_delivery.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
		
		public function deleteByCart($id_cart){
			$delete = $this->delete->delete($this->table.' WHERE id_cart = "'.$id_cart.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}