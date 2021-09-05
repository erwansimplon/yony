<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CategoryModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_category;
		public $id_parent;
		public $name;
		public $position;
		public $active;
		public $nleft;
		public $nright;
		public $level;
		public $date_add;
		public $date_upd;
		
		protected $id = 'id_category';
		protected $table = 'categories';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
			$this->insert = new InsertSqlCore();
			$this->update = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'Category', $id);
			}
		}
		
		public function getCategoryById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getCategoryTree(){
			
			$table = array($this->table => 'p');
			
			$liaison = array('c.id_parent' => 'p.id_category');
			
			$select = $this->select->select('c.*, p.nright as parent_right,
											 p.position as parent_postion,
											 (SELECT MAX(position)
											  FROM '.$this->table.' a
											  WHERE a.id_parent = p.id_parent) as max_pos')
									->from($this->table, 'c')
									->join('left', $table, $liaison)
									->where('c.active = 1')
									->orderby(array('c' => 'nleft ASC'));
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function getAllCategory(){
			$select = $this->select->select('*')
									->from($this->table)
									->orderby(array($this->table => 'position ASC'));
			
			return $this->pdo->query('mysql', $select);
		}
		
		public function RegenerateNleftNright(){
			
			$requete = $this->getAllCategory();
			
			$categories_array = array();
			
			foreach ($requete as $category) {
				$categories_array[$category->id_parent]['subcategories'][] = $category->id_category;
			}
			$n = 1;
			
			if (isset($categories_array[0]) && $categories_array[0]['subcategories']) {
				foreach ($categories_array[0]['subcategories'] as $category_id) {
					$this->getNleftNright($categories_array, $category_id, $n);
				}
			}
			
		}
		
		function getNleftNright(&$categories, $id_category, &$n){
			
			$left = $n++;
			if (isset($categories[(int)$id_category]['subcategories'])) {
				foreach ($categories[(int)$id_category]['subcategories'] as $id_subcategory) {
					$this->getNleftNright($categories, (int)$id_subcategory, $n);
				}
			}
			$right = (int)$n++;
			
			$this->UpdateNleftNright($left, $right, $id_category);
		}
		
		function UpdateNleftNright($left, $right, $id_category){
			
			$set = array('`nleft`' => (int)$left,
						 '`nright`' => (int)$right);
			
			$update = $this->update->update($this->table)
									->set($set)
									->where($this->id.' = "'.$id_category.'"');
			
			$this->pdo->exec('mysql', $update);
		}
		
		/**
		 * @param mixed $id
		 */
		public function setIdCategory($id)
		{
			$this->id_category = $id;
		}
		
		/**
		 * @param mixed $id_parent
		 */
		public function setIdParent($id_parent)
		{
			$this->id_parent = $id_parent;
		}
		
		/**
		 * @param mixed $name
		 */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		/**
		 * @param mixed $position
		 */
		public function setPosition($position)
		{
			$this->position = $position;
		}
		
		/**
		 * @param mixed $active
		 */
		public function setActive($active)
		{
			$this->active = $active;
		}
		
		/**
		 * @param mixed $nleft
		 */
		public function setNleft($nleft)
		{
			$this->nleft = $nleft;
		}
		
		/**
		 * @param mixed $nright
		 */
		public function setNright($nright)
		{
			$this->nright = $nright;
		}
		
		/**
		 * @param mixed $level
		 */
		public function setLevel($level)
		{
			$this->level = $level;
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
		
		public function loadDataInArray(){
			$tabs = array('id_parent' => $this->id_parent,
						  'name'      => $this->name,
						  'position'  => $this->position,
						  'active'    => $this->active,
						  'level'     => $this->level,
						  'date_add'  => $this->date_add,
						  'date_upd'  => $this->date_upd);
			
			return $tabs;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_category = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd(){
			$update = $this->update->update($this->table)
								   ->set($this->loadDataInArray())
								   ->where($this->id.' = "'.$this->id_category.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete(){
			$delete = $this->delete->delete($this->table.'
											WHERE nleft >= '.$this->nleft.'
											AND nright <= '.$this->nright);
			
			return $this->pdo->exec('mysql', $delete);
		}
	}