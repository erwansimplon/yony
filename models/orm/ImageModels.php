<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class ImageModels extends Database
	{
		protected $pdo;
		
		protected $select;
		
		public function __construct()
		{
			$this->pdo = new Database();
			
			$this->select = new SelectSqlCore();
		}
	
		public function getFormatImageByType($type){
			$select = $this->select->select('*')
									->from('images')
									->where('type LIKE "'.$type.'%"');
			
			return $this->pdo->query('mysql', $select);
		}
	}