<?php
/**
 * Created by PhpStorm.
 * User: guillet
 * Date: 27/05/18
 * Time: 12:31
 */

class Autoloader
{
    /**
     * Enregistre notre autoloader
     *
     * @param $class
     */
    static function register($class, $redirect = false){

        $tab = explode('\\', $class);

        $path = implode(DIRECTORY_SEPARATOR, $tab).'.php';
		
        if(file_exists($path)){
			include_once($path);
		} else{
			header('Location: '.$redirect.'&not_found=true');
		}
    }
	
	static function hydrate($class, $function, $id, $option = ''){
		
  		$fonction = 'get'.$function.'ById'.$option;
			
		if(method_exists($class, $fonction)){
			$data = $class->$fonction($id);
			
			if($data) {
				foreach ($data as $key => $value) {
					if (array_key_exists($key, $class)) {
						$class->{$key} = $value;
					}
				}
			}
		}
	}
}