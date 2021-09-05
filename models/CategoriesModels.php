<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CategoriesModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		protected $table = 'categories';
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
		}
		
		public function getFamilleByIdParent($id_parent){
			$select = $this->select->select('*, if(active = 1, "Oui", "Non") as afficher')
									->from($this->table)
									->where('id_parent = "'.$id_parent.'"')
									->orderby(array('categories' => 'position ASC'));
			
			return $this->pdo->prepare('mysql', $select);
		}
		
		public function getFamilleById($id){
			$select = $this->select->select('id_category, id_parent, name')
									->from($this->table)
									->where('id_category = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function searchFamille($where){
			
			$select = $this->select->select('*, if(active = 1, "Oui", "Non") as afficher')
									->from($this->table)
									->where($where);
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function lastPositionIdParent($id_parent){
			$select = $this->select->select('IFNULL(MAX(position), 0) +1 as position')
									->from($this->table)
									->where('id_parent = "'.$id_parent.'"');
			
			$result = $this->pdo->query('mysql', $select, false, true);
			
			return $result->position;
		}
		
		public function updatePositionByCategory($id, $position){
			
			$update = $this->update->update($this->table)
									->set(array('position' => $position))
									->where('id_category = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
	}