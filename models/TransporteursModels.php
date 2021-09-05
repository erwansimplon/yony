<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class TransporteursModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		protected $table = 'carrier';
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getCarriers($limit, $affichage, $where = false){
			
			$table = array('carrier_image' => 'img');
			
			$liaison = array('c.id_carrier' => 'img.id_carrier');
			
			$select = $this->select->select('c.id_carrier as id, c.*, img.*,
			 								 if(c.free_carrier = 1, "Oui", "Non") as is_free,
											 if(c.active = 1, "Oui", "Non") as afficher')
									->from('carrier', 'c')
									->join('left', $table, $liaison)
									->limit($limit.','.$affichage)
									->orderby(array('c' => 'id_carrier DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countCarriers($where = false){
			$select = $this->select->select('count(*) as nb')
									->from($this->table);
			
			if($where){
				$select = $this->select->where($where);
			}
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
	}