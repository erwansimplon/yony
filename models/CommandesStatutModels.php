<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CommandesStatutModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		protected $table = 'order_state';
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getOrderState($limit, $affichage, $where = false){
			
			$select = $this->select->select('*,
											 if(delivery = 1, "Oui", "Non") as livraison,
											 if(invoice = 1, "Oui", "Non") as facture,
											 if(credit_note = 1, "Oui", "Non") as avoir')
									->from($this->table)
									->limit($limit.','.$affichage)
									->orderby(array($this->table => 'id_order_state DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countOrderState($where = false){
			
			$select = $this->select->select('count(*) as nb')
									->from($this->table);
			
			if($where){
				$select = $this->select->where($where);
			}
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
		
	}