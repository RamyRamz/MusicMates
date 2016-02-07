<?php

/**
 * Class to handle programs
 */

class Program
{
  // Properties

  public $programID = null;//
  public $programName = null;//
  public $programStart= null;
  public $programFrequency = null;


  /**
  * Sets the object's properties using the values in the supplied array
  *
  * @param assoc The property values
  */

  public function __construct( $data=array() ) {
	  
	//program basic information
    if ( isset( $data['programID'] ) ) $this->programID = (int) $data['programID'];
    if ( isset( $data['programName'] ) ) $this->programName = (string) $data['programName'];
	if ( isset( $data['programStart'] ) ) $this->programStart = (int) $data['programStart'];
	if ( isset( $data['programFrequency'] ) ) $this->programFrequency = (int) $data['programFrequency'];
	
  }


  /**
  * Sets the object's properties using the edit form post values in the supplied array
  *
  * @param assoc The form post values
  */

  public function storeFormValues ( $params ) {

    // Store all the parameters
    $this->__construct( $params );
	
    // Parse and store the program start date 
    if ( isset($params['programStart']) ) {
      $programStart = explode ( '-', $params['programStart'] );

      if ( count($programStart) == 3 ) {
        list ( $y, $m, $d ) = $programStart;
        $this->programStart = mktime ( 0, 0, 0, $m, $d, $y );
      }
    }
  }

  /**
  * Returns a program object matching the given program ID
  *
  * @param int The program ID
  * @return program|false The program object, or false if the record was not found or there was a problem
  */

  public static function getById( $id ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT * FROM programs WHERE programID = :id";
    $st = $conn->prepare( $sql );
    $st->bindValue( ":id", $id, PDO::PARAM_INT );
    $st->execute();
    $row = $st->fetch();
    $conn = null;
    if ( $row ) return new program( $row );
  }


  /**
  * Returns all (or a range of) program objects in the DB
  *
  * @param int Optional The number of rows to return (default=all)
  * @param string Optional column by which to order the programs (default="publicationDate DESC")
  * @return Array|false A two-element array : results => array, a list of program objects; totalRows => Total number of programs
  */

  public static function getList( $numRows=1000000, $order="programID ASC" ) {
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM programs
            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
    $st->execute();
    $list = array();

    while ( $row = $st->fetch() ) {
      $Program = new Program( $row );
      $list[] = $Program;
    }

    // Now get the total number of programs that matched the criteria
    $sql = "SELECT FOUND_ROWS() AS totalRows";
    $totalRows = $conn->query( $sql )->fetch();
    $conn = null;
    return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
  }


  /**
  * Inserts the current program object into the database, and sets its ID property.
  */

  public function insert() {

    // Does the program object already have an ID?
    if ( !is_null( $this->programID ) ) trigger_error ( "program::insert(): Attempt to insert an program object that already has its ID property set (to $this->id).", E_USER_ERROR );

    // Insert the program
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "INSERT INTO programs ( programName, programStart, programFrequency ) VALUES ( :programName, :programStart, :programFrequency )";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":programName", $this->programName, PDO::PARAM_STR );
    $st->bindValue( ":programStart", $this->programStart, PDO::PARAM_STR );
	$st->bindValue( ":programFrequency", $this->programFrequency, PDO::PARAM_STR );
    $st->execute();
    $this->programID = $conn->lastInsertId();
    $conn = null;
  }


  /**
  * Updates the current program object in the database.
  */

  public function update() {

    // Does the program object have an ID?
    if ( is_null( $this->programID ) ) trigger_error ( "Program::update(): Attempt to update a Program object that does not have its ID property set.", E_USER_ERROR );
   
    // Update the program
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $sql = "UPDATE programs SET programStart=FROM_UNIXTIME(:programStart), programName=:programName, programFrequency=:programFrequency WHERE programID = :programID";
    $st = $conn->prepare ( $sql );
    $st->bindValue( ":programName", $this->programName, PDO::PARAM_STR );
    $st->bindValue( ":programStart", $this->programStart, PDO::PARAM_STR );
	$st->bindValue( ":programFrequency", $this->programFrequency, PDO::PARAM_STR );
    $st->execute();
    $conn = null;
  }


  /**
  * Deletes the current program object from the database.
  */

  public function delete() {

    // Does the program object have an ID?
    if ( is_null( $this->programID ) ) trigger_error ( "Program::delete(): Attempt to delete an program object that does not have its ID property set.", E_USER_ERROR );

    // Delete the program
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $st = $conn->prepare ( "DELETE FROM programs WHERE programID = :id LIMIT 1" );
    $st->bindValue( ":id", $this->programID, PDO::PARAM_INT );
    $st->execute();
    $conn = null;
  }

}

?>
