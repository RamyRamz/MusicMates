<?php

/**
 * Class to handle Guardians
 */

class Guardian
{
  // Properties

  public $guardianID = null;//
  public $guardianName = null;//
  public $guardianRelation = null;//
  public $guardianCell = null;
  public $guardianHome = null;
  public $guardianWork = null;
  public $guardianEmail = null;//
  public $guardianAddress = null;//
  public $guardianCity = null;//
  public $guardianProvince = null;//
  public $guardianCountry = null;//

  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
	  
	//Guardian basic information
    if ( isset( $data['guardianID'] ) ) $this->guardianID = (int) $data['guardianID'];
    if ( isset( $data['guardianName'] ) ) $this->guardianName = (string) $data['guardianName'];
	if ( isset( $data['guardianRelation'] ) ) $this->guardianRelation = (string) $data['guardianRelation'];
	//Guardian address information
	if ( isset( $data['guardianAddress'] ) ) $this->guardianAddress = preg_replace ( "/[a-zA-Z0-9]/", "", $data['guardianAddress'] );
	if ( isset( $data['guardianCity'] ) ) $this->guardianCity = (string) $data['guardianCity'];
	if ( isset( $data['guardianProvince'] ) ) $this->guardianProvince = (string) $data['guardianProvince'];
	if ( isset( $data['guardianCountry'] ) ) $this->guardianCountry = (string) $data['guardianCountry'];
    //Guardian contact information
	if ( isset( $data['guardianEmail'] ) ) $this->guardianEmail = preg_replace ( "/[^\.\_\@\$ a-zA-Z0-9()]/", "", $data['guardianEmail'] );
	if ( isset( $data['guardianCell'] ) ) $this->guardianCell = preg_replace ( "/[^\-\ 0-9]/", "", $data['guardianCell'] );
	if ( isset( $data['guardianHome'] ) ) $this->guardianHome = preg_replace ( "/[^\-\ 0-9]/", "", $data['guardianHome'] );
	if ( isset( $data['guardianWork'] ) ) $this->guardianWork = preg_replace ( "/[^\-\ 0-9]/", "", $data['guardianWork'] );
  }


  /**
  * Sets the object's properties using the edit form post values in the supplied array
  *
  * @param assoc The form post values
  */

  public function storeFormValues ( $params ) {

    // Store all the parameters
    $this->__construct( $params );

  }

  /**
  * Returns a Guardian object matching the given Guardian ID
  *
  * @param int The Guardian ID
  * @return Guardian|false The Guardian object, or false if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT * FROM guardians WHERE guardianID = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Guardian( $row );
  }


  /**
  * Returns all (or a range of) Guardian objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the Guardians (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Guardian objects; totalRows => Total number of Guardians
  */

  public static function getList( $numRows=1000000, $order="guardianID ASC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM guardians
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $Guardian = new Guardian( $row );
      $list[] = $Guardian;
    }

    // Now get the total number of Guardians that matched the criteria
    $sql = "SELECT FOUND_ROWS() AS totalRows";
    $totalRows = $conn->query( $sql )->fetch();
    $conn = null;
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }


  /**
  * Inserts the current Guardian object into the database, and sets its ID property.
  */

  public function insert() {

    // Does the Guardian object already have an ID?
    if ( !is_null( $this->guardianID ) ) trigger_error ( "Guardian::insert(): Attempt to insert an Guardian object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the Guardian
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO guardians ( guardianName, guardianRelation, guardianAddress, guardianCity, guardianProvince,guardianCountry, guardianEmail, guardianCell, guardianHome, guardianWork ) VALUES ( :guardianName, :guardianRelation, :guardianAddress, :guardianCity, :guardianProvince, :guardianCountry, :guardianEmail, :guardianCell, :guardianHome, :guardianWork )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":guardianName", $this->guardianName, PDO::PARAM_STR );
    $st->bindValue( ":guardianRelation", $this->guardianRelation, PDO::PARAM_STR );
	$st->bindValue( ":guardianAddress", $this->guardianAddress, PDO::PARAM_STR );
	$st->bindValue( ":guardianCity", $this->guardianCity, PDO::PARAM_STR );
	$st->bindValue( ":guardianProvince", $this->guardianProvince, PDO::PARAM_STR );
	$st->bindValue( ":guardianCountry", $this->guardianCountry, PDO::PARAM_STR );
	$st->bindValue( ":guardianEmail", $this->guardianEmail, PDO::PARAM_STR );
	$st->bindValue( ":guardianCell", $this->guardianCell, PDO::PARAM_STR );
	$st->bindValue( ":guardianHome", $this->guardianHome, PDO::PARAM_STR );
	$st->bindValue( ":guardianWork", $this->guardianWork, PDO::PARAM_STR );
    $st->execute();
    $this->guardianID = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current Guardian object in the database.
  */

  public function update() {

    // Does the Guardian object have an ID?
    if ( is_null( $this->guardianID ) ) trigger_error ( "Guardian::update(): Attempt to update an Guardian object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the Guardian
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE guardians SET guardianName = :guardianName, guardianRelation = :guardianRelation, guardianAddress = :guardianAddress, guardianCity = :guardianCity, guardianProvince = :guardianProvince, guardianCountry = :guardianCountry, guardianEmail = :guardianEmail, guardianCell = :guardianCell, guardianHome = :guardianHome, guardianWork = :guardianWork WHERE guardianID = :guardianID";
    $st = $conn->prepare ( $sql );
	$st->bindValue( ":guardianName", $this->guardianName, PDO::PARAM_STR );
    $st->bindValue( ":guardianRelation", $this->guardianRelation, PDO::PARAM_STR );
	$st->bindValue( ":guardianAddress", $this->guardianAddress, PDO::PARAM_STR );
	$st->bindValue( ":guardianCity", $this->guardianCity, PDO::PARAM_STR );
	$st->bindValue( ":guardianProvince", $this->guardianProvince, PDO::PARAM_STR );
	$st->bindValue( ":guardianCountry", $this->guardianCountry, PDO::PARAM_STR );
	$st->bindValue( ":guardianEmail", $this->guardianEmail, PDO::PARAM_STR );
	$st->bindValue( ":guardianCell", $this->guardianCell, PDO::PARAM_STR );
	$st->bindValue( ":guardianHome", $this->guardianHome, PDO::PARAM_STR );
	$st->bindValue( ":guardianWork", $this->guardianWork, PDO::PARAM_STR );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current Guardian object from the database.
  */

  public function delete() {

    // Does the Guardian object have an ID?
    if ( is_null( $this->guardianID ) ) trigger_error ( "Guardian::delete(): Attempt to delete an Guardian object that does not have its ID property set.", E_USER_ERROR );

    // Delete the Guardian
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM guardians WHERE guardianID = :id LIMIT 1" );
    $st->bindValue( ":id", $this->guardianID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
