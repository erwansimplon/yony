<?php
	
	class DeleteSqlCore
	{
		private $delete;
		
		public function delete($table){
			
			$this->delete = "DELETE FROM $table";
			
			return $this->delete;
		}
	}