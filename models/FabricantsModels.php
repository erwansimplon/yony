<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class FabricantsModels extends Database
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
		
		public function getManufacturers($limit, $affichage, $where = false){
			
			$table = array('manufacturers_image' => 'img');
			
			$liaison = array('m.id_image' => 'img.id_manufacturers_image');
			
			$select = $this->select->select('m.*, img.*, if(m.active = 1, "Oui", "Non") as afficher')
									->from('manufacturers', 'm')
									->join('left', $table, $liaison)
									->limit($limit.','.$affichage)
									->orderby(array('m' => 'id_manufacturer DESC'));
			
			if($where){
				$select = $this->select->where($where);
			}
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function countManufacturers($where = false){
			$select = $this->select->select('count(*) as nb')
									->from('manufacturers');
			
			if($where){
				$select = $this->select->where($where);
			}
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->nb;
		}
	}