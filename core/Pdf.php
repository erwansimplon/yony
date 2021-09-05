<?php
	/**
	 * Created by PhpStorm.
	 * User: guillet
	 * Date: 27/05/18
	 * Time: 12:31
	 */
	
	class Pdf
	{
		
		private static $dir;
		private static $pdf = null;
		
		public function __construct()
		{
			self::$dir = $_SERVER['DOCUMENT_ROOT'].'/assets/pdf/';
		}
		
		static function renamePdf(){
			
			if(empty(self::$pdf)) {
				self::$pdf = self::generateName();
			}
			
			$dir = self::$dir.self::$pdf.'/';
			$ext = '.pdf';
			
			if(!file_exists($dir)){
				mkdir($dir);
			}
			
			return array(self::$pdf.$ext, $dir.self::$pdf.$ext);
		}
		
		static function uploadPdf($pdf){
			list($base_name, $name_pdf) = self::renamePdf();
			
			$rename = move_uploaded_file($pdf['tmp_name'], $name_pdf);
			
			return array($rename, $base_name);
		}
		
		static function generateName(){
			return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);
		}
		
		static function clearPdf($pdf){
			$dir = self::$dir.$pdf.'/';
			
			unlink($dir.$pdf);
			
			if(file_exists($dir)){
				rmdir($dir);
			}
		}
	}