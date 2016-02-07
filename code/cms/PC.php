<?php

/**
 * Class to handle Participants/ contact registration
 */

class PC
{
  // Properties
  public $PC_ID = null;//
  public $participantID = null;//
  public $contactID = null;//
  


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
	  
	//Participant basic information
    if ( isset( $data['PC_ID'] ) ) $this->PC_ID = (int) $data['PC_ID'];
	if ( isset( $data['participantID'] ) ) $this->participantID = (int) $data['participantID'];
	if ( isset( $data['contactID'] ) ) $this->contactID = (int) $data['contactID'];
    

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
  * Returns a Participant object matching the given Participant ID
  *
  * @param int The Participant ID
  * @return Participant|false The Participant object, or false if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT * FROM PartCont WHERE PC_ID = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new PC( $row );
  }


  /**
  * Returns all (or a range of) Participant objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the Participants (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Participant objects; totalRows => Total number of Participants
  */

  public static function getList( $numRows=1000000, $order="PC_ID ASC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM PartCont
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $PC = new PC( $row );
      $list[] = $PC;
    }

    // Now get the total number of Participants that matched the criteria
    $sql = "SELECT FOUND_ROWS() AS totalRows";
    $totalRows = $conn->query( $sql )->fetch();
    $conn = null;
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }


  /**
  * Inserts the current Participant object into the database, and sets its ID property.
  */

  public function insert() {

    // Does the Participant object already have an ID?
    if ( !is_null( $this->id ) ) trigger_error ( "PC::insert(): Attempt to insert an PC object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO PartCont ( participantID, contactID ) VALUES ( :participantID, :contactID )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":participantID", $this->participantID, PDO::PARAM_INT );
    $st->bindValue( ":contactID", $this->contactID, PDO::PARAM_INT );

    $st->execute();
    $this->PC_ID = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current Participant object in the database.
  */

  public function update() {

    // Does the Participant object have an ID?
    if ( is_null( $this->PC_ID ) ) trigger_error ( "PC::update(): Attempt to update a PC object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE PartCont SET participantID=:participantID, contactID = :contactID WHERE PC_ID = :PC_ID";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":participantID", $this->participantID, PDO::PARAM_INT );
    $st->bindValue( ":contactID", $this->contactID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current Participant object from the database.
  */

  public function delete() {

    // Does the Participant object have an ID?
    if ( is_null( $this->PC_ID ) ) trigger_error ( "PC::delete(): Attempt to delete a PC object that does not have its ID property set.", E_USER_ERROR );

    // Delete the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM PartCont WHERE PC_ID = :id LIMIT 1" );
    $st->bindValue( ":id", $this->PC_ID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
