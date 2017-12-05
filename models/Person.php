<?php

require_once 'Interest.php';
require_once 'Database.php';

class Person
{
  const PERSON_TABLE = "persons";
  const PERSON_INTEREST_TABLE = "person_interest";
  /**
   * @var Integer
   */
  var $id;

  /**
   *
   * @var String
   */
  var $first_name;

  /**
   *
   * @var String
   */
  var $last_name;

  /**
   *
   * @var Integer
   */
  var $age;

  /**
   *
   * @var $String
   */
  var $email;

  /**
   *
   * @var $Interest array
   */
  var $interests;

  /**
   *
   * @var DateTime
   */
  var $admission_date;

  /**
   *
   * @var DateTime
   */
  var $admission_time;

  /**
   *
   * @var boolean
   */
  var $is_active;

  /**
   * @var String
   **/
   var $msg;

  /**
   * Constructor
   */
  function __construct()
  {
    $this->interests = array();
    $this->msg = "";
  }

  /**
   * Save this person to the database
   */
  public function save(){
    $db = new Database();
    $sql = "";
    if (empty($this->id)){
      $sql = "insert into persons(
          first_name,
          last_name,
          age,
          email,
          admission_date,
          is_active
        )
      values(
        :first_name,
        :last_name,
        :age,
        :email,
        :admission_date,
        :is_active
        )";
      $stm = $db->pdo->prepare($sql);
    }
    else{
      $sql = "update persons set
        first_name=:first_name,
        last_name=:last_name,
        age=:age,
        email=:email,
        admission_date=:admission_date,
        is_active=:is_active
        where id=:id
        ";
        $stm = $db->pdo->prepare($sql);
        $stm->bindParam(':id', $this->id);

    }
    $date = $this->admission_date->format("Y-m-d H:i:s");
    $stm->bindParam(':first_name', $this->first_name);
    $stm->bindParam(':last_name', $this->last_name);
    $stm->bindParam(':age', $this->age);
    $stm->bindParam(':email', $this->email);
    $stm->bindParam(':admission_date', $date);
    //$stm->bindParam(':admission_time', $this->admission_time->format("H:i:s"));
    $stm->bindParam(':is_active', $this->is_active);
    $stm->execute();
    $this->id = $db->pdo->lastInsertId();
    $this->save_interests();
  }

  /**
   * Save this person's interests to database
   */
  private function save_interests(){
    $db = new Database();
    $sql = "delete from " . Person::PERSON_INTEREST_TABLE . " where person_id=:person_id";
    $stm = $db->pdo->prepare($sql);
    $stm->bindParam(':person_id', $this->id);
    $stm->execute();

    foreach($this->interests as $interest){
      $sql = "insert into " . Person::PERSON_INTEREST_TABLE . " (person_id, interest_id) values (:person_id, :interest_id)";
      $stm = $db->pdo->prepare($sql);
      $stm->bindParam(':person_id', $this->id);
      $stm->bindParam(':interest_id', $interest->id);
      $stm->execute();
    }
    $pi_stm = "";
  }

  /**
   * loads the interests for this person from the database
   */
  private function load_interests(){
    $db = new Database();
    $sql = "select * from " . Person::PERSON_INTEREST_TABLE . " where person_id=:person_id";
    $stm = $db->pdo->prepare($sql);
    $stm->bindParam(':person_id', $this->id);
    $stm->execute();
    $its = $stm->fetchAll();
    foreach($its as $it){
      $this->interests[] = Interest::with_id($it["interest_id"]);
    }
  }

  /**
   * Create an instance given the ID.
   * The properties are loaded from the database
   * @param Integer $id
   * @return Person
   */
  static function from_id($id){
    $db = new Database();
    $sql = "select * from " . Person::PERSON_TABLE . " where id=:person_id";
    $stm = $db->pdo->prepare($sql);
    $stm->bindParam(':person_id', $id);
    $stm->execute();
    $row = $stm->fetch();
    $person = new Person();
    $person->id = $id;
    //to pass validation, interests are loaded separately
    $row["interests"] = array();

    //mysql datetime to right format conversion
    $db_date = DateTime::createfromformat("Y-m-d H:i:s", $row["admission_date"]);
    $row["admission_date"] = $db_date->format("Y-m-d g:iA");
    $row["admission_time"] = $db_date->format("g:iA");

    //load values into person properties
    $person->from_array($row);
    $person->load_interests();
    return $person;
  }

  /**
   * Create an instance from a json representation
   * @param JSON $data
   * @return Person
   */
  static function from_json($data)
  {
    $decoded = json_decode($data, true);
    $decoded["admission_date"] .= " " . $decoded["admission_time"];
    $person = new Person();
    return $person->from_array($decoded);
  }

  /**
   * Extracts the value of the given index. If required, it also checks that
   * the value is not blank
   * @param Array $array
   * @param String $index
   * @param Boolean $required
   * @return Mixed
   */
  function get_index($array, $index, $required=false){
    if ((array_key_exists($index, $array)) &&
      (($required === false) || ($required && (!empty($array[$index]))))  )
    {
      return $array[$index];
    }
    else{
      $this->msg .= "$index is required " . (($required)?" and must not be empty ":" ") . "\n";
    }
  }

  /**
   * Set properties for this person using the given array values
   * @param Array $array
   * @return Person
   * @throws Exception
   */
  function from_array($array)
  {

    $this->first_name = $this->get_index($array, "first_name", true);
    $this->last_name = $this->get_index($array, "last_name", true);
    $this->email = $this->get_index($array, "email", true);

    $this->age = $this->get_index($array, "age");
    $this->is_active = $this->get_index($array, "is_active");

    $this->admission_date = DateTime::createfromformat("Y-m-d g:iA", $this->get_index($array, "admission_date"));
    $this->admission_time = DateTime::createfromformat("g:iA", $this->get_index($array, "admission_time"));

    $this->get_index($array, "interests");

    if (!empty($this->msg)){
      throw new Exception("Error Processing Request \n" . $this->msg, 1);
    }

    if (array_key_exists("interests", $array)){
      foreach($array["interests"] as $interest){
        $this->interests[] = Interest::withName($interest);
      }
    }

    return $this;
  }

}
