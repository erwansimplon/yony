<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ClientsModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getCustomers($limit, $affichage, $where = false){
			
			$table = array('customer_gender' => 'g',
						   'customer_group' => 'cg');
			
			$liaison = array('c.id_gender' => 'g.id_gender',
							 'c.id_group'  => 'cg.id_group');
			
			$select = $this->select->select('c.*, g.name as sexe, cg.name_group,
											 if(c.active = 1, "Oui", "Non") as afficher')
									->from('clients', 'c')
									->join('inner', $table, $liaison)
									->limit($limit.','.$affichage)
									->orderby(array('c' => 'id_customer DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countCustomers($where = false){
			$select = $this->select->select('count(*) as nb')
									->from('clients', 'c');
			
			if($where){
				$select = $this->select->where($where);
			}
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
	}