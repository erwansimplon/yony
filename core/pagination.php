<?php
	/**
	 * Created by PhpStorm.
	 * User: guillet
	 * Date: 27/05/18
	 * Time: 12:31
	 */
	
	class Pagination
	{
		static function generate($page, $nb, $affichage){
			
			$nb_pagination = ceil($nb / $affichage);
			
			if($page -2 <= 0){
				$p_start = 1;
			} else{
				$p_start = $page -2;
			}
			
			if($page +2 >= $nb_pagination){
				$p_end = $nb_pagination;
			} else{
				$p_end = $page +2;
			}
			
			return array($nb_pagination, $p_start, $p_end);
		}
	}