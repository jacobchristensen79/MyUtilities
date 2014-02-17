<?php
namespace Models;
/**
 * 
 * @author jacob
 * 
 */

class Url extends Base
{
	protected $_model = 'service_url';

	public function __construct()
	{
		parent::start(__CLASS__);
	}
	
	/**
	 * update Clicks
	 */
	public function updateClicks()
	{
		$query = "UPDATE $this->_model set clicks=clicks+1, updated_at=NOW() where id=:id";
		$db = $this->_db->prepare($query);
		$db->bindParam(':id', $this->id, \PDO::PARAM_INT);
		return $db->execute();
	}
	
	/**
	 * update code
	 */
	public function updateCode()
	{
		$query = "UPDATE $this->_model set shortcode=:shortcode where id=:id";
		$db = $this->_db->prepare($query);
		$db->bindParam(':id', $this->id, \PDO::PARAM_INT);
		$db->bindParam(':shortcode', $this->shortcode, \PDO::PARAM_STR);
		return $db->execute();
	}

	
	/**
	 * Save
	 */
	public function save()
	{
		$query = "INSERT INTO $this->_model 
			(`service_link`, `service_user_id`, `service_domain_id`) 
			values(:service_link, :service_user_id, :service_domaind_id)";
		$db = $this->_db->prepare($query);
		
		$db->bindParam(':service_link', $this->service_link, \PDO::PARAM_STR);
		$db->bindParam(':service_user_id', $this->service_user_id, \PDO::PARAM_INT);
		$db->bindParam(':service_domaind_id', $this->service_domaind_id, \PDO::PARAM_INT);
		$result = $db->execute();
		$this->id = ($result) ? (int) $this->_db->lastInsertId() : false;		
		return $this->id;
	}
	
	
	public function getTopClicks($limit=5)
	{
		$query = "SELECT *
		FROM $this->_model
		ORDER BY clicks DESC
		LIMIT :limit";
		$db = $this->_db->prepare($query);
		
		$db->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
		$db->bindParam(':limit', $limit, \PDO::PARAM_INT);
		$db->execute();
		$result = $db->fetchAll();
		return ($result) ? $result : array();
	}
	
	public function getTopByUserId($userid, $limit=5)
	{
		$query = "SELECT *
		FROM $this->_model
		WHERE service_user_id=:userid
		ORDER BY clicks DESC
		LIMIT :limit";
		$db = $this->_db->prepare($query);
		
		$db->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
		$db->bindParam(':limit', $limit, \PDO::PARAM_INT);
		$db->bindParam(':userid', $userid, \PDO::PARAM_INT);
		$db->execute();
		$result = $db->fetchAll();
		return ($result) ? $result : array();
	}
	
	public function getByUserId($userid)
	{
		$query = "SELECT *
		FROM $this->_model
		WHERE service_user_id=:userid
		ORDER BY created_at DESC";
		$db = $this->_db->prepare($query);
		
		$db->setFetchMode(\PDO::FETCH_CLASS, __CLASS__);
		$db->bindParam(':userid', $userid, \PDO::PARAM_INT);
		$db->execute();
		$result = $db->fetchAll();
		return ($result) ? $result : array();
	}
			
}
?>