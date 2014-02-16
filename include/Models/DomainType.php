<?php
namespace Models;
/**
 * 
 * @author jacob
 * 
 */

class DomainType extends Base
{
	protected $_model = 'service_domain_type';

	public function __construct()
	{
		parent::start(__CLASS__);
	}

	
	/**
	 * Save
	 */
	 public function save()
	 {	 	
	 	$query = "INSERT INTO $this->_model (name) values(:name)";	 	
	 	$db = $this->_db->prepare($query);
	 	$db->bindParam(':name', $this->name, \PDO::PARAM_STR);
	 	$result = $db->execute();
	 	return ($result) ? (int) $this->_db->lastInsertId() : false;
	 }

				
}
?>