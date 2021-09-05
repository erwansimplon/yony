<?php
	
	class SelectSqlCore
	{
		private $select = [];
		private $from = [];
		private $join = [];
		private $conditions = [];
		private $groupby = [];
		private $orderby = [];
		private $limit = [];
		
		/**
		 * @return $this
		 */
		public function select(){
			$this->select = func_get_args();
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

			$this->join = '';

			for($i = 0; $i < count($table_join); $i++){
				
				if($type == 'inner') {

					if(count($table_join) > '1'){
						$this->join .= " INNER JOIN ".$table_join[$i].' '.$alias[$i]." ON ".$liaison_from[$i].' = '.$liaison_to[$i];
					}
					else{
						$this->join = " INNER JOIN ".$table_join[$i].' '.$alias[$i]." ON ".$liaison_from[$i].' = '.$liaison_to[$i];
					}
				}
				else{

					if(count($table_join) > '1'){
						$this->join .= " LEFT JOIN ".$table_join[$i].' '.$alias[$i]." ON ".$liaison_from[$i].' = '.$liaison_to[$i];
					}
					else{
						$this->join = " LEFT JOIN ".$table_join[$i].' '.$alias[$i]." ON ".$liaison_from[$i].' = '.$liaison_to[$i];
					}
				}
				
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
		 * @return $this
		 */
		public function groupby(){
			$this->groupby = func_get_args();
			return $this;
		}
		
		/**
		 * @param $OrderByArray
		 *
		 * @return $this
		 */
		public function orderby($OrderByArray){
			
			if(is_array($OrderByArray)) {
				$table = array_keys($OrderByArray);
				$colonne = array_values($OrderByArray);
				
				for ($i = 0; $i < count($table); $i++) {
					$this->orderby = $table[$i] . '.' . $colonne[$i];
				}
			}
			else {
				$this->orderby = $OrderByArray;
			}
			
			return $this;
		}
		
		/**
		 * @return $this
		 */
		public function limit(){
			
			$this->limit = func_get_args();
			return $this;
		}
		
		/**
		 * @return string
		 */
		public function __toString(){
			$query = 'SELECT '.implode(', ', $this->select)
					. ' FROM ' . $this->from;
			
			if(!empty($this->join)) {
				$query .= $this->join;
				unset($this->join);
			}
			
			if(!empty($this->conditions)) {
				$query .= ' WHERE ' . implode(' AND ', $this->conditions);
				unset($this->conditions);
			}
			
			if(!empty($this->groupby)) {
				$query .= ' GROUP BY ' . implode(' AND ', $this->groupby);
				unset($this->groupby);
			}
			
			if(!empty($this->orderby)) {
				$query .= ' ORDER BY ' . $this->orderby;
				unset($this->orderby);
			}
			
			if(!empty($this->limit)) {
				$query .= ' LIMIT ' . implode(', ', $this->limit);
				unset($this->limit);
			}
			
			return $query;
		}
		
	}