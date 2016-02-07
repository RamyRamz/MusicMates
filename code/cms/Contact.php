<?php

/**
 * Class to handle Contacts
 */

class Contact
{
  // Properties

  public $contactID = null;//
  public $contactName = null;//
  public $contactRelation = null;//
  public $contactNumber = null;


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
	  
	//Contact basic information
    if ( isset( $data['contactID'] ) ) $this->contactID = (int) $data['contactID'];
    if ( isset( $data['contactName'] ) ) $this->contactName = (string) $data['contactName'];
	if ( isset( $data['contactRelation'] ) ) $this->contactRelation = (string) $data['contactRelation'];
	if ( isset( $data['contactNumber'] ) ) $this->contactNumber = (string) $data['contactNumber'];
	
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
  * Returns a Contact object matching the given Contact ID
  *
  * @param int The Contact ID
  * @return Contact|false The Contact object, or false if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT * FROM contacts WHERE contactID = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Contact( $row );
  }


  /**
  * Returns all (or a range of) Contact objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the Contacts (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Contact objects; totalRows => Total number of Contacts
  */

  public static function getList( $numRows=1000000, $order="contactID ASC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM contacts
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $Contact = new Contact( $row );
      $list[] = $Contact;
    }

    // Now get the total number of Contacts that matched the criteria
    $sql = "SELECT FOUND_ROWS() AS totalRows";
    $totalRows = $conn->query( $sql )->fetch();
    $conn = null;
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }


  /**
  * Inserts the current Contact object into the database, and sets its ID property.
  */

  public function insert() {

    // Does the Contact object already have an ID?
    if ( !is_null( $this->contactID ) ) trigger_error ( "Contact::insert(): Attempt to insert an Contact object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the Contact
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO contacts ( contactName, contactRelation, contactNumber ) VALUES ( :contactName, :contactRelation, :contactNumber )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":contactName", $this->contactName, PDO::PARAM_STR );
    $st->bindValue( ":contactRelation", $this->contactRelation, PDO::PARAM_STR );
	$st->bindValue( ":contactNumber", $this->contactNumber, PDO::PARAM_STR );
    $st->execute();
    $this->contactID = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current Contact object in the database.
  */

  public function update() {

    // Does the Contact object have an ID?
    if ( is_null( $this->contactID ) ) trigger_error ( "Contact::update(): Attempt to update an Contact object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the Contact
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE contacts SET contactName = :contactName, contactRelation = :contactRelation, contactNumber = :contactNumber WHERE contactID = :contactID";
    $st = $conn->prepare ( $sql );
	$st->bindValue( ":contactName", $this->contactName, PDO::PARAM_STR );
    $st->bindValue( ":contactRelation", $this->contactRelation, PDO::PARAM_STR );
	$st->bindValue( ":contactNumber", $this->contactNumber, PDO::PARAM_STR );

    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current Contact object from the database.
  */

  public function delete() {

    // Does the Contact object have an ID?
    if ( is_null( $this->contactID ) ) trigger_error ( "Contact::delete(): Attempt to delete an Contact object that does not have its ID property set.", E_USER_ERROR );

    // Delete the Contact
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM contacts WHERE contactID = :id LIMIT 1" );
    $st->bindValue( ":id", $this->contactID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
