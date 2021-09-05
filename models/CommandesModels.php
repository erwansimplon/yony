<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CommandesModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		protected $table = 'orders';
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getOrders($limit, $affichage, $where = false){
			
			$table = array('clients' => 'c',
						   'order_payment' => 'op',
						   'order_state' => 'os');
			
			$liaison = array('o.id_customer' => 'c.id_customer',
							 'o.id_payment'  => 'op.id_order_payment',
							 'o.current_state' => 'os.id_order_state');
			
			$select = $this->select->select('o.*, c.name as name_customer,
											 op.name as name_payment,
											 os.name as name_statut, os.color, os.background')
									->from($this->table, 'o')
									->join('inner', $table, $liaison)
									->limit($limit.','.$affichage)
									->orderby(array('o' => 'id_order DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countOrders($where = false){
			
			$table = array('clients' => 'c',
						   'order_payment' => 'op',
						   'order_state' => 'os');
			
			$liaison = array('o.id_customer' => 'c.id_customer',
							 'o.id_payment'  => 'op.id_order_payment',
							 'o.current_state' => 'os.id_order_state');
			
			$select = $this->select->select('count(*) as nb')
									->from($this->table, 'o')
									->join('inner', $table, $liaison);
			
			if($where){
				$select = $this->select->where($where);
			}
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
		
		public function searchClient($query){
			
			$table = array('customer_gender' => 'g',
						   'customer_group' => 'cg');
			
			$liaison = array('c.id_gender' => 'g.id_gender',
							 'c.id_group'  => 'cg.id_group');
			
			$select = $this->select->select('c.id_customer, c.name, c.email,
											 g.name as sexe, cg.name_group,
											 if(c.active = 1, "Oui", "Non") as afficher')
									->from('clients', 'c')
									->join('inner', $table, $liaison)
									->where('c.id_customer LIKE "%'.$query.'%" OR
																 c.name LIKE "%'.$query.'%" OR
																 c.email LIKE "%'.$query.'%"')
									->groupby('c.id_customer')
									->orderby(array('c' => 'id_customer DESC'));
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function searchProduct($query){
			
			$select = $this->select->select('id_product, reference, name, quantity, conditionnement, prix_vente')
									->from('products')
									->where('reference LIKE "%'.$query.'%" OR
											 name LIKE "%'.$query.'%" OR
											 ean13 LIKE "%'.$query.'%"',
											'active = 1')
									->groupby('id_product')
									->orderby(array('products' => 'reference ASC'));
			
			return $this->pdo->query('mysql', $select);
		}
		
	}