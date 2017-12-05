<?php

require_once 'models/Person.php';


$method = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$resp = "";
switch($method){
  case "POST":
    $resp = process_post();
    break;
  case "GET":
    $resp = process_get();
    break;
  case "PUT":
    $resp = process_put();
    break;
  default:
    $resp = array(
      "status" => "error",
      "msg"    => "unsupported http method"
    );
    break;
}

echo json_encode($resp);

/* * *************************************************************************** */

function process_put(){
  $json = file_get_contents('php://input');
  $person;
  try
  {
    $decoded = json_decode($json, true);
    $person = Person::from_json($json);
    if ((!isset($person->id)) || (empty($person->id))){
      return process_error("ID is not set \nID must be set $json " . var_export($person, true));
    }
  }
  catch (Exception $e)
  {
    return process_error($e->getMessage());
  }
  $person->save();
  return array(
    "status" => "success",
    "msg"    => $person->id
  );
}

function process_get()
{
  $id = filter_input(INPUT_GET, "id");
  if (empty($id)){
    return array(
      "status" => "error",
      "msg" => "id must be specified"
    );
  }
  try{
    return array(
      "status" => "success",
      "msg" => Person::from_id($id)
    );
  }catch(Exception $e){
    return process_error($e->getMessage());
  }
}

function process_post()
{
  $json = file_get_contents('php://input');

  try
  {
    $person = Person::from_json($json);
  }
  catch (Exception $e)
  {
    return process_error($e->getMessage());
  }

  $person->save();
  return array(
    "status" => "success",
    "msg"    => $person->id
  );
}

function process_error($error_message){
  return array(
    "status"  => "error",
    "msg" => $error_message
  );
}
