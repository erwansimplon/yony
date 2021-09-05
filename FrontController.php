<?php
/**
 * Created by PhpStorm.
 * User: guillet
 * Date: 27/05/18
 * Time: 13:03
 */
	
error_reporting(E_ALL);
ini_set("display_errors", 1);
set_time_limit(0);

require_once 'config/Autoloader.php';

if(!isset($_SESSION)) {
	session_start();
}

if(isset($_GET['dir'])){
	$dir = $_GET['dir'].'/';
	$redirect = '../'.$dir.'dashboard';
} else{
	$dir = 'front/';
	$redirect = $dir.'index';
}

if(isset($_GET['page'])){
	$page = explode('_', $_GET['page']);
	$page = array_map('ucfirst', $page);
	$page = implode('', $page);
} else{
	$page = 'Index';
}

if(isset($_GET['dir']) && $page != 'Login' && (empty($_SESSION['employee']) OR $page == 'Logout')){
	session_destroy();
	header('Location: ../admin/');
}

$models = $page.'Models';
$controller = $page.'Controller';

Autoloader::register('config/Database');
Autoloader::register('config/Render');
Autoloader::register('config/Url');

Autoloader::register('core/sql/SelectSqlCore');
Autoloader::register('core/sql/ReplaceSqlCore');
Autoloader::register('core/sql/UpdateSqlCore');
Autoloader::register('core/sql/InsertSqlCore');
Autoloader::register('core/sql/DeleteSqlCore');

Autoloader::register('core/EmailCore');
Autoloader::register('core/xlsx.class');
Autoloader::register('core/XLSXReader');

if(isset($_GET['dir']) && $_GET['dir'] == 'admin'){
	Autoloader::register(_DIR_CORE_CONTROLLER_.$dir.'AdminController');
}

if(file_exists('models/'.$models.'.php')) {
	Autoloader::register('models/' . $models);
}

Autoloader::register('controller/'.$dir.$controller, $redirect);

new $controller();
