<?php
	
	/**
	 * Created by Erwan.
	 * Date: 26/10/19
	 * Time: 09:03
	 */
	class CarrierImageModels extends Database
	{
		protected $pdo;
		
		protected $select;
		protected $insert;
		protected $update;
		protected $replace;
		protected $delete;
		
		public $id_carrier_image;
		public $id_carrier;
		public $logo;
		public $ext;
		
		public $table = 'carrier_image';
		public $id = 'id_carrier_image';
		
		public function __construct($id = false)
		{
			$this->pdo = new Database();
			
			$this->select  = new SelectSqlCore();
			$this->insert  = new InsertSqlCore();
			$this->update  = new UpdateSqlCore();
			$this->replace = new ReplaceSqlCore();
			$this->delete  = new DeleteSqlCore();
			
			if ($id) {
				Autoloader::hydrate($this, 'CarrierImage', $id);
			}
		}
		
		public function getCarrierImageById($id){
			$select = $this->select->select('*')
									->from($this->table)
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		public function getImageByIdCarrier($id){
			
			$select = $this->select->select('*')
									->from($this->table)
									->where('id_carrier = "'.$id.'"');
			
			return $this->pdo->query('mysql', $select, false, true);
		}
		
		/**
		 * @return mixed
		 */
		public function getIdCarrierImage()
		{
			return $this->id_carrier_image;
		}
		
		/**
		 * @param mixed $id_carrier_image
		 */
		public function setIdCarrierImage($id_carrier_image)
		{
			$this->id_carrier_image = $id_carrier_image;
		}
		
		/**
		 * @return mixed
		 */
		public function getIdCarrier()
		{
			return $this->id_carrier;
		}
		
		/**
		 * @param mixed $id_carrier
		 */
		public function setIdCarrier($id_carrier)
		{
			$this->id_carrier = $id_carrier;
		}
		
		/**
		 * @return mixed
		 */
		public function getLogo()
		{
			return $this->logo;
		}
		
		/**
		 * @param mixed $logo
		 */
		public function setLogo($logo)
		{
			$this->logo = $logo;
		}
		
		/**
		 * @return mixed
		 */
		public function getExt()
		{
			return $this->ext;
		}
		
		/**
		 * @param mixed $ext
		 */
		public function setExt($ext)
		{
			$this->ext = $ext;
		}
		
		public function loadDataInArray(){
			$tabs = array('id_carrier' => $this->id_carrier,
						  'logo'       => $this->logo,
						  'ext'        => $this->ext);
			
			return $tabs;
		}
		
		public function add(){
			
			$insert = $this->insert->insert($this->table, $this->loadDataInArray())
									->value($this->loadDataInArray());
			
			$result = $this->pdo->exec('mysql', $insert);
			
			$this->id_carrier_image = $this->pdo->lastInsertId('mysql');
			
			return $result;
		}
		
		public function upd($id){
			$update = $this->update->update($this->table)
									->set($this->loadDataInArray())
									->where($this->id.' = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $update);
		}
		
		public function delete($id){
			$delete = $this->delete->delete($this->table.' WHERE '.$this->id.' = "'.$id.'"');
			
			return $this->pdo->exec('mysql', $delete);
		}
	}