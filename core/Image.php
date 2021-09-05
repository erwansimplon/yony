<?php
	/**
	 * Created by PhpStorm.
	 * User: guillet
	 * Date: 27/05/18
	 * Time: 12:31
	 */
	
	class Image
	{
		
		private static $dir;
		private static $image = null;
		
		public function __construct()
		{
			self::$dir = $_SERVER['DOCUMENT_ROOT'].'/assets/img/';
		}
		
		static function renameImage($image, $sub_dir, $name){
			$path_info = pathinfo($image['name']);
			$ext = '.'.$path_info['extension'];
			
			if(empty(self::$image)) {
				self::$image = self::generateName();
			}
			
			$dir = self::$dir.$sub_dir.self::$image.'/';
			
			if(!file_exists($dir)){
				mkdir($dir);
			}
			
			return array(self::$image, $ext, $dir.self::$image.'_'.$name.$ext);
		}
		
		static function uploadImage($image, $sub_dir, $name, $largeur = false, $longueur = false){
			if(isset($image['tmp_name'])) {
				list($base_name, $ext, $name_image) = self::renameImage($image, $sub_dir, $name);
				$rename = copy($image['tmp_name'], $name_image);
				
				if ($largeur && $longueur){
					self::resizeImage($name_image, $largeur, $longueur);
				}
				
				return array($rename, $base_name, $ext);
			}
			
			return false;
		}
		
		static function generateName(){
			return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);
		}
		
		static function resizeImage($image, $largeur, $longueur){
			$imagick = new Imagick($image);
			$imagick->setimagebackgroundcolor(new ImagickPixel("white"));
			
			$imagick->thumbnailImage($largeur, $longueur, true, true);
			$imagick->writeImage($image);
			$imagick->clear();
		}
		
		static function clearImage($dossier, $image){
			$dir = self::$dir.$dossier.$image.'/';
			
			foreach (preg_grep("/^$image/i", scandir($dir)) as $list){
				unlink($dir.$list);
			}
			
			if(file_exists($dir)){
				rmdir($dir);
			}
		}
		
		static function clearUpload($image){
			if (file_exists($image)){
				unlink($image);
			}
		}
	}