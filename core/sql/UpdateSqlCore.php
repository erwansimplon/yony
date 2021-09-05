<?php
	
	class UpdateSqlCore
	{
		private $update = [];
		private $join = [];
		private $set = [];
		private $conditions = [];
		
		/**
		 * @param      $table
		 * @param bool $alias
		 *
		 * @return $this
		 */
		public function update($table, $alias = false){
			
			$this->update = "UPDATE ";
			
			$this->update .= $alias ? "$table $alias"
									: "$table";
						
			
			return $this;
		}
		
		/**
		 * @param $type
		 * @param $table
		 * @param $liaison
		 *
		 * @return $this
		 */
		public function join($type, $table, $liaison){
			
			$table_join = array_keys($table);
			$alias = array_values($table);
			
			$liaison_from = array_keys($liaison);
			$liaison_to = array_values($liaison);
			
			for($i = 0; $i < count($table_join); $i++){
				
				if($type == 'inner') {
					$this->join = " INNER JOIN ";
				}
				
				else{
					$this->join = " LEFT JOIN ";
				}
				
				$this->join .= "$table_join[$i] $alias[$i] ON $liaison_from[$i] = $liaison_to[$i]";
				
			}
			
			return $this;
		}
		
		/**
		 * @param $set_colonne
		 *
		 * @return $this
		 */
		public function set($set_colonne){
			
			$colonne_update = array_keys($set_colonne);
			$colonne_select = array_values($set_colonne);
			
			$this->set = " SET ";
			
			for($i = 0; $i < count($colonne_update); $i++){
				
				$this->set .= $colonne_update[$i].' = "'.$colonne_select[$i].'", ';
				
			}
			
			$this->set = substr($this->set,0,-2);
			
			return $this;
		}
		
		/**
		 * @return $this
		 */
		public function where(){
			$this->conditions = func_get_args();
			return $this;
		}
		
		/**
		 * @return array|string
		 */
		public function __toString(){
			$query = $this->update;
			
			if(!empty($this->join)) {
				$query .= $this->join;
				unset($this->join);
			}
			
			$query .= $this->set;
			
			if(!empty($this->conditions)) {
				$query .= ' WHERE ' . implode(' AND ', $this->conditions);
				unset($this->conditions);
			}
			
			return $query;
		}
	}