<?php

class Database {

    private $mysql;
	
	private $host;
	private $name;
	private $user;
	private $pass;
	
	/**
	 * @param $bdd
	 *
	 * @return \PDO|string
	 */
	protected function switchBdd($bdd)
	{
		switch ($bdd) {
			case 'mysql':
				
				$this->host = 'yokyfranhhyoky.mysql.db';
				$this->name = 'yokyfranhhyoky';
				$this->user = 'yokyfranhhyoky';
				$this->pass = 'Yoky2019';
				
				$database = $this->getMysql();
				break;
			default:
				$database = '';
		}
		
		return $database;
	}
	
	protected function getMysql ()
	{
		if($this->mysql === null) {
			$mysql = new PDO("mysql:host=$this->host;dbname=$this->name", $this->user, $this->pass);
			$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$mysql->exec('SET NAMES utf8');
			$this->mysql = $mysql;
		}
		
		return $this->mysql;
	}
	
    /**
     * @param      $statement
     * @param bool $singleResult
     *
     * @return array|mixed
     */
	public function query($bdd, $statement, $assoc = false, $singleResult = false)
	{
		$database = $this->switchBdd($bdd);
		
		$requete = $database->query($statement);
		
		$mode = ($assoc) ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ;
		$requete->setFetchMode($mode);
		
		if($singleResult){
			$data = $requete->fetch();
		}
		else{
			$data = $requete->fetchAll();
		}
		
		return $data;
		
	}
	
	/**
	 * @param      $statement
	 * @param      $parametres
	 * @param bool $singleResult
	 *
	 * @return array|mixed
	 */
	public function prepare ($bdd, $statement, $parametres = false, $singleResult = false)
	{
		$database = $this->switchBdd($bdd);
		
		$requete = $database->prepare($statement);
		$requete->setFetchMode(PDO::FETCH_OBJ);
		
		if($parametres) {
			$requete->execute($parametres);
		} else{
			$requete->execute();
		}
		
		if($singleResult){
			$data = $requete->fetch();
		}
		else{
			$data = $requete->fetchAll();
		}
		
		return $data;
	}
	
	/**
	 * @param $statement
	 *
	 * @return int
	 */
	public function exec ($bdd, $statement)
	{
		$database = $this->switchBdd($bdd);
		
		$requete = $database->exec($statement);
		
		return $requete;
	}
	
	public function lastInsertId ($bdd)
	{
		$database = $this->switchBdd($bdd);
		
		$requete = $database->lastInsertId();
		
		return $requete;
	}
}