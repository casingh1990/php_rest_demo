<?php
require_once 'Database.php';

class Interest{
  /**
   * The ID in database
   * @var Integer
   */
  var $id;
  /**
   * Name of this interest
   * @var String
   */
  var $name;
  /**
   * table name that this maps to in database
   */
  const TABLE_NAME = "interests";

  /**
   * Constructor
   */
  function __construct()
  {
  }

  /**
   * Creates an instance given the name. If the name is not found then it is
   * created in database
   * @param String $name
   * @return \Interest
   */
  static function withName($name)
  {
    $interest = new Interest();
    $interest->name = $name;
    $interest->load_id();
    return $interest;
  }

  /**
   * This is not used
   * @param type $id
   * @param type $name
   * @return \Interest
   *
  static function with_id_and_name($id, $name){
    $interest = new Interest();
    $interest->id = $id;
    $interest->name = $name;
    return $interest;
  }*/

  /**
   * Creates an instance given the id.
   * The properties are loaded from database
   * @param Integer $id
   * @return \Interest
   * @throws Exception
   */
  static function with_id($id){
    $db = new Database();
    $sql = "select * from " . Interest::TABLE_NAME . " where id=:id";
    $stm = $db->pdo->prepare($sql);
    $stm->bindParam(':id', $id);
    $stm->execute();
    $interest = new Interest();
    $interest->id = $id;
    if ($stm->rowCount() > 0){
      $row = $stm->fetch();
      $interest->name = $row["name"];
    }else{
      throw new Exception("ID not found", 1);
    }
    return $interest;
  }

  /**
   * Loads the ID from database given the name
   */
  function load_id(){
    $db = new Database();
    $sql = "select * from " . Interest::TABLE_NAME . " where name=:name";
    $stm = $db->pdo->prepare($sql);
    $stm->bindParam(':name', $this->name);
    $stm->execute();
    if ($stm->rowCount() > 0){
      $row = $stm->fetch();
      $this->id = $row["id"];
    }
    else{
      $this->save();
      $this->id = $db->pdo->lastInsertId();
    }
  }

  /**
   * Saves the current state to database
   */
  public function save(){
    $db = new Database();
    $sql = "";
    $stm = null;
    if (empty($this->id)){
      $sql = "insert into interests (name) values (:name)";
      $stm = $db->pdo->prepare($sql);
    }
    else{
      $sql = "update interests set name=:name where id=:id";
      $stm = $db->pdo->prepare($sql);
      $stm->bindParam(':id', $this->id);
    }
    $stm->bindParam(':name', $this->name);
    $stm->execute();
  }
}
