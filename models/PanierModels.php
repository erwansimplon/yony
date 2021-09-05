<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class PanierModels extends Database
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
		
		public function getCarts($limit, $affichage, $where = false){
			
			if($where){
				$where .= ' AND c.used = 0';
			} else{
				$where = 'c.used = 0';
			}
			
			$table = array(
				'carrier' => 'trans',
				'clients' => 'cli'
			);
			
			$liaison = array(
				'c.id_carrier'  => 'trans.id_carrier',
				'c.id_customer' => 'cli.id_customer'
			);
			
			$select = $this->select->select('c.*, trans.name as name_carrier, cli.name as name_customer')
									->from('cart', 'c')
									->join('left', $table, $liaison)
									->where($where)
									->limit($limit.','.$affichage)
									->orderby(array('c' => 'id_cart DESC'));
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countCarts($where = false){
			
			if($where){
				$where .= ' AND c.used = 0';
			} else{
				$where = 'c.used = 0';
			}
			
			$table = array(
				'carrier' => 'trans',
				'clients' => 'cli'
			);
			
			$liaison = array(
				'c.id_carrier'  => 'trans.id_carrier',
				'c.id_customer' => 'cli.id_customer'
			);
			
			$select = $this->select->select('count(*) as nb')
									->from('cart', 'c')
									->join('left', $table, $liaison)
									->where($where);
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
	}