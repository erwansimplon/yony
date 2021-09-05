<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ProductsModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_product;
		public $id_manufacturer;
		public $id_category;
		public $id_tax;
		public $reference;
		public $name;
		public $ean13;
		public $active;
		public $quantity;
		public $conditionnement;
		public $prix_achat;
		public $prix_vente;
		public $ecotax;
		public $weight;
		public $date_add;
		public $date_upd;
		
		protected $table = 'products';
		protected $id = 'id_product';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Products', $id);
			}
		}
		
		public function getProductsById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function loadDataInArray(){
			$tabs = array('id_manufacturer' => $this->id_manufacturer,
						  'id_category'     => $this->id_category,
						  'id_tax'          => $this->id_tax,
						  'reference'       => $this->reference,
						  'name'            => $this->name,
						  'ean13'           => $this->ean13,
						  'active'          => $this->active,
						  'quantity'        => $this->quantity,
						  'conditionnement' => $this->conditionnement,
						  'prix_achat'      => $this->prix_achat,
						  'prix_vente'      => $this->prix_vente,
						  'ecotax'          => $this->ecotax,
						  'weight'          => $this->weight,
						  'date_add'        => $this->date_add,
						  'date_upd'        => $this->date_upd);
			
			return $tabs;
		}
		
		/**
		 * @param mixed $id_product
		 */
		public function setIdProduct($id_product)
		{
			$this->id_product = $id_product;
		}
		
		/**
		 * @param mixed $id_manufacturer
		 */
		public function setIdManufacturer($id_manufacturer)
		{
			$this->id_manufacturer = $id_manufacturer;
		}
		
		/**
		 * @param mixed $id_category
		 */
		public function setIdCategory($id_category)
		{
			$this->id_category = $id_category;
		}
		
		/**
		 * @param mixed $id_tax
		 */
		public function setIdTax($id_tax)
		{
			$this->id_tax = $id_tax;
		}
		
		/**
		 * @param mixed $reference
		 */
		public function setReference($reference)
		{
			$this->reference = $reference;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $ean13
		 */
		public function setEan13($ean13)
		{
			$this->ean13 = $ean13;
		}
		
		/**
		 * @param mixed $active
		 */
		public function setActive($active)
		{
			$this->active = $active;
		}
		
		/**
		 * @param mixed $quantity
		 */
		public function setQuantity($quantity)
		{
			$this->quantity = $quantity;
		}
		
		/**
		 * @param mixed $conditionnement
		 */
		public function setConditionnement($conditionnement)
		{
			$this->conditionnement = $conditionnement;
		}
		
		/**
		 * @param mixed $prix_achat
		 */
		public function setPrixAchat($prix_achat)
		{
			$this->prix_achat = $prix_achat;
		}
		
		/**
		 * @param mixed $prix_vente
		 */
		public function setPrixVente($prix_vente)
		{
			$this->prix_vente = $prix_vente;
		}
		
		/**
		 * @param mixed $ecotax
		 */
		public function setEcotax($ecotax)
		{
			$this->ecotax = $ecotax;
		}
		
		/**
		 * @param mixed $weight
		 */
		public function setWeight($weight)
		{
			$this->weight = $weight;
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
			
			$this->id_product = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$this->id_product.'"');
								
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$this->id_product.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}