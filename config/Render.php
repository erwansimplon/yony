<?php
/**
 * Created by PhpStorm.
 * User: guillet
 * Date: 27/05/18
 * Time: 15:36
 */
	use Twig\Extra\Intl\IntlExtension;
	
	class Render
	{
		protected $twig;
		protected $template;
		
		public function __construct ($dir = '')
		{
			include_once ('./core/twig/vendor/autoload.php');
			$loader = new Twig_Loader_Filesystem('./views');
			$this->twig = new Twig_Environment($loader, array(
				'debug' => true,
				'cache' => false
			));
			$this->twig->addGlobal('_get', $_GET);
			$this->twig->addGlobal('_session', $_SESSION);
			$this->twig->addExtension(new Twig_Extension_Debug());
			$this->twig->addExtension(new IntlExtension());
			$this->template = $this->twig->load('templates/'.$dir.'templates.twig');
		}
		
		public static function renderTpl($file){
			include_once ('./core/twig/vendor/autoload.php');
			$loader = new Twig_Loader_Filesystem('./views');
			$twig = new Twig_Environment($loader, array(
				'debug' => true,
				'cache' => false
			));
			$twig->addGlobal('_get', $_GET);
			$twig->addGlobal('_session', $_SESSION);
			$twig->addExtension(new Twig_Extension_Debug());
			$twig->addExtension(new IntlExtension());
			return $twig->load('templates/'.$file);
		}
	}