<?php
	
	class ReplaceSqlCore
	{
		private $replace = [];
		private $from = [];
		private $join = [];
		private $conditions = [];
		
		/**
		 * @param $table
		 * @param $colonne
		 *
		 * @return $this
		 */
		public function replace($table, $colonne){
			
			$colonne_replace = array_keys($colonne);
			$colonne_select = array_values($colonne);
			
			
			$this->replace = "REPLACE INTO $table (";
			
				$this->replace .= '`'.implode('`, `',$colonne_replace).'`';
			
			$this->replace .= ")";
			
			
			$this->replace .= " SELECT ";
			
				$this->replace .= '`'.implode('`, `',$colonne_select).'`';
			
			
			return $this;
		}
		
		/**
		 * @param      $table
		 * @param null $alias
		 *
		 * @return $this
		 */
		public function from($table, $alias = null){
			if(is_null($alias)){
				$this->from = $table;
			}else{
				$this->from = "$table AS $alias";
			}
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
		 * @return $this
		 */
		public function where(){
			$this->conditions = func_get_args();
			return $this;
		}
		
		/**
		 * @return string
		 */
		public function __toString(){
			$query = $this->replace
				. ' FROM ' . $this->from;
			
			if(!empty($this->join)) {
				$query .= $this->join;
				unset($this->join);
			}
			if(!empty($this->conditions)) {
				$query .= ' WHERE ' . implode(' AND ', $this->conditions);
				unset($this->conditions);
			}
			
			return $query;
		}
	}