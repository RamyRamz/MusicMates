<?php

/**
 * Class to handle Participants/ guardian registration
 */

class PG
{
  // Properties
  public $PG_ID = null;//
  public $participantID = null;//
  public $guardianID = null;//
  


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
	  
	//Participant basic information
    if ( isset( $data['PG_ID'] ) ) $this->PG_ID = (int) $data['PG_ID'];
	if ( isset( $data['participantID'] ) ) $this->participantID = (int) $data['participantID'];
	if ( isset( $data['guardianID'] ) ) $this->guardianID = (int) $data['guardianID'];
    

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
    $sql = "SELECT * FROM PartGuard WHERE PG_ID = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new PG( $row );
  }


  /**
  * Returns all (or a range of) Participant objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the Participants (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Participant objects; totalRows => Total number of Participants
  */

  public static function getList( $numRows=1000000, $order="PG_ID ASC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM PartGuard
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $PG = new PG( $row );
      $list[] = $PG;
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
    if ( !is_null( $this->id ) ) trigger_error ( "PG::insert(): Attempt to insert an PG object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO PartGuard ( participantID, guardianID ) VALUES ( :participantID, :guardianID )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":participantID", $this->participantID, PDO::PARAM_INT );
    $st->bindValue( ":guardianID", $this->guardianID, PDO::PARAM_INT );

    $st->execute();
    $this->PG_ID = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current Participant object in the database.
  */

  public function update() {

    // Does the Participant object have an ID?
    if ( is_null( $this->PG_ID ) ) trigger_error ( "PG::update(): Attempt to update a PG object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE PartGuard SET participantID=:participantID, guardianID = :guardianID WHERE PG_ID = :PG_ID";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":participantID", $this->participantID, PDO::PARAM_INT );
    $st->bindValue( ":guardianID", $this->guardianID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current Participant object from the database.
  */

  public function delete() {

    // Does the Participant object have an ID?
    if ( is_null( $this->PG_ID ) ) trigger_error ( "PG::delete(): Attempt to delete a PG object that does not have its ID property set.", E_USER_ERROR );

    // Delete the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM PartGuard WHERE PG_ID = :id LIMIT 1" );
    $st->bindValue( ":id", $this->PG_ID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
