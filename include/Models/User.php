<?php
namespace Models;
/**
 * 
 * @author jacob
 * 
 */
use Classes\FrmModel;

class User
{
	protected $_model = 'service_user';
    
    private $_user_status = array('disabled', 'normal', 'pro');

	protected $attributes = Array();
	protected $_db;
	protected $_DbModel;

	public function __construct()
	{
		$this->_DbModel = FrmModel::getInstance();
		$this->_db = $this->_DbModel->getDb();
	}
	
	public function getStatus()
	{
		return isset($this->_user_status[$this->id]) ? $this->_user_status[$this->id] : reset($this->_user_status);
	}
	
	/**
	 * Data Validation input fields
	 * @return array
	 */
	public function getInputFields()
	{
		return array(
				'username' => array(
						'filter' => FILTER_VALIDATE_REGEXP,
						"options"=>array("regexp"=>"/^(.*){3,30}$/")),
				'password' => array(
						'filter' => FILTER_VALIDATE_REGEXP,
						"options"=>array("regexp"=>"/^(.*){3,50}$/")),
	
		);
	}

	public function getByUsername( $username )
	{
		$query = "SELECT * 
			FROM $this->_model 
			WHERE username=:username
			LIMIT 1";
		$db = $this->_db->prepare($query);
		$db->bindParam(':username', $username, \PDO::PARAM_STR);
		$db->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
		$db->execute();
		return $db->fetch();
	}



	/**
	 * Save from array
	 */
	 public function save($data)
	 {
		$res = $this->_DbModel->save($data, $this->_model);
		return $res;
	 }

				
}
?>