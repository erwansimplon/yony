<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ClientsGroupModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		protected $table = 'customer_group';
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getCustomerGroup($limit, $affichage, $where = false){
			
			$table = array('tva' => 't');
			
			$liaison = array('g.id_tva' => 't.id_tva');
			
			$select = $this->select->select('g.*, t.name as tva_name, if(g.display_price = 1, "Oui", "Non") as afficher_prix')
									->from($this->table, 'g')
									->join('inner', $table, $liaison)
									->limit($limit.','.$affichage)
									->orderby(array('g' => 'id_group DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countCustomerGroup($where = false){
			
			$table = array('tva' => 't');
			
			$liaison = array('g.id_tva' => 't.id_tva');
			
			$select = $this->select->select('count(*) as nb')
									->from($this->table, 'g')
									->join('inner', $table, $liaison);
			
			if($where){
				$select = $this->select->where($where);
			}
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
		
		public function countCustomerInGroup($id){
			
			$select = $this->select->select('count(*) as nb')
									->from('clients')
									->where('id_group = "'.$id.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			if(isset($result->nb)){
				return $result->nb;
			}
			
			return false;
		}
	}