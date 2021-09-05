<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsDiscountsModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_products_discounts;
		public $id_product;
		public $id_customer;
		public $id_group;
		public $remise;
		public $prix_net_ht;
		public $prix_net_ttc;
		public $date_from;
		public $date_to;
		public $date_add;
		public $date_upd;
		
		protected $table = 'products_discounts';
		protected $id = 'id_products_discounts';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'ProductsDiscounts', $id);
			}
		}
		
		public function getProductsDiscountsById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getDiscountsByIdProduct($id){
			
			$table = array('clients' => 'c',
						   'customer_gender' => 'gender',
						   'customer_group'  => 'groupe');
			
			$liaison = array('c.id_customer'    => 'd.id_customer',
							 'gender.id_gender' => 'c.id_gender',
							 'groupe.id_group'  => 'd.id_group');
			
			$select = $this->select->select('d.*,
											IFNULL(CONCAT(gender.name, " ", c.name), "Tous clients") as client,
											IFNULL(groupe.name_group, "Tous les groupes") as group_client')
									->from($this->table, 'd')
									->join('left', $table, $liaison)
									->where('d.id_product = "'.$id.'"')
									->orderby(array('d' => 'id_products_discounts DESC'));
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_product'   => $this->id_product,
						  'id_customer'  => $this->id_customer,
						  'id_group'     => $this->id_group,
						  'remise'       => $this->remise,
						  'prix_net_ht'  => $this->prix_net_ht,
						  'prix_net_ttc' => $this->prix_net_ttc,
						  'date_from'    => $this->date_from,
						  'date_to'      => $this->date_to,
						  'date_add'     => $this->date_add,
						  'date_upd'     => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_products_discounts
		 */
		public function setIdProductsDiscounts($id_products_discounts)
		{
			$this->id_products_discounts = $id_products_discounts;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $id_customer
		 */
		public function setIdCustomer($id_customer)
		{
			$this->id_customer = $id_customer;
		}
		
		/**
		 * @param mixed $id_group
		 */
		public function setIdGroup($id_group)
		{
			$this->id_group = $id_group;
		}
		
		/**
		 * @param mixed $remise
		 */
		public function setRemise($remise)
		{
			$this->remise = $remise;
		}
		
		/**
		 * @param mixed $prix_net_ht
		 */
		public function setPrixNetHt($prix_net_ht)
		{
			$this->prix_net_ht = $prix_net_ht;
		}
		
		/**
		 * @param mixed $prix_net_ttc
		 */
		public function setPrixNetTtc($prix_net_ttc)
		{
			$this->prix_net_ttc = $prix_net_ttc;
		}
		
		/**
		 * @param mixed $date_from
		 */
		public function setDateFrom($date_from)
		{
			$this->date_from = $date_from;
		}
		
		/**
		 * @param mixed $date_to
		 */
		public function setDateTo($date_to)
		{
			$this->date_to = $date_to;
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
									->where($this->id.' = "'.$this->id_products_discounts.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_products_discounts.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}