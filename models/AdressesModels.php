<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class AdressesModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		protected $table = 'adresses';
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getAddress($limit, $affichage, $where = false){
			
			$select = $this->select->select('*')
									->from($this->table)
									->limit($limit.','.$affichage)
									->orderby(array($this->table => 'id_address DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countAddress($where = false){
			
			$select = $this->select->select('count(*) as nb')
									->from($this->table);
			
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
	}