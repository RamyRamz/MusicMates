<?php

/**
 * Class to handle Participants
 */

class Participant
{
  // Properties

  public $participantID = null;//
  public $participantName = null;//
  public $participantDOB = null;//
  public $participantGender = null;
  public $participantSupp = null;//true or false value indicating whether or not the participant requires 1 on 1 support
  public $participantNeeds = null;


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
	  
	//Participant basic information
    if ( isset( $data['participantID'] ) ) $this->participantID = (int) $data['participantID'];
    if ( isset( $data['participantName'] ) ) $this->participantName = (string) $data['participantName'];
	if ( isset( $data['participantDOB'] ) ) $this->participantDOB = (int) $data['participantDOB'];
	if ( isset( $data['participantGender'] ) ) $this->participantGender = (string) $data['participantGender'];
	//Participant extra information
	if ( isset( $data['participantSupp'] ) ) $this->participantSupp = (int) $data['participantSupp'];
    if ( isset( $data['participantNeeds'] ) ) $this->participantNeeds = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['participantNeeds'] );


  }


  /**
  * Sets the object's properties using the edit form post values in the supplied array
  *
  * @param assoc The form post values
  */

  public function storeFormValues ( $params ) {

    // Store all the parameters
    $this->__construct( $params );
	
    // Parse and store the participant date of birth
    if ( isset($params['participantDOB']) ) {
      $participantDOB = explode ( '-', $params['participantDOB'] );

      if ( count($participantDOB) == 3 ) {
        list ( $y, $m, $d ) = $participantDOB;
        $this->participantDOB = mktime ( 0, 0, 0, $m, $d, $y );
      }
    }
  }

  /**
  * Returns a Participant object matching the given Participant ID
  *
  * @param int The Participant ID
  * @return Participant|false The Participant object, or false if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT * FROM participants WHERE participantID = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new Participant( $row );
  }


  /**
  * Returns all (or a range of) Participant objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the Participants (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of Participant objects; totalRows => Total number of Participants
  */

  public static function getList( $numRows=1000000, $order="participantID ASC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM participants
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $Participant = new Participant( $row );
      $list[] = $Participant;
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
    if ( !is_null( $this->participantID ) ) trigger_error ( "Participant::insert(): Attempt to insert an Participant object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO participants ( participantName, participantDOB, participantGender, participantSupp, participantNeeds ) VALUES ( :participantName, :participantDOB, :participantGender, :participantSupp, :participantNeeds )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":participantName", $this->participantName, PDO::PARAM_STR );
    $st->bindValue( ":participantDOB", $this->participantDOB, PDO::PARAM_STR );
	$st->bindValue( ":participantGender", $this->participantGender, PDO::PARAM_STR );
	$st->bindValue( ":participantSupp", $this->participantSupp, PDO::PARAM_STR );
	$st->bindValue( ":participantNeeds", $this->participantNeeds, PDO::PARAM_STR );
    $st->execute();
    $this->participantID = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current Participant object in the database.
  */

  public function update() {

    // Does the Participant object have an ID?
    if ( is_null( $this->participantID ) ) trigger_error ( "Participant::update(): Attempt to update an Participant object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE participants SET participantDOB=FROM_UNIXTIME(:participantDOB), participantName=:participantName, participantGender=:participantGender, participantSupp=:participantSupp, participantNeeds=:participantNeeds WHERE participantID = :participantID";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":participantName", $this->participantName, PDO::PARAM_STR );
    $st->bindValue( ":participantDOB", $this->participantDOB, PDO::PARAM_STR );
	$st->bindValue( ":participantGender", $this->participantGender, PDO::PARAM_STR );
	$st->bindValue( ":participantSupp", $this->participantSupp, PDO::PARAM_STR );
	$st->bindValue( ":participantNeeds", $this->participantNeeds, PDO::PARAM_STR );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current Participant object from the database.
  */

  public function delete() {

    // Does the Participant object have an ID?
    if ( is_null( $this->participantID ) ) trigger_error ( "Participant::delete(): Attempt to delete an Participant object that does not have its ID property set.", E_USER_ERROR );

    // Delete the Participant
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM participants WHERE participantID = :id LIMIT 1" );
    $st->bindValue( ":id", $this->participantID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
