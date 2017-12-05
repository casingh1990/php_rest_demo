<?php

require_once 'models/Person.php';

$method = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$resp = "";
if ($method == "POST")
{
  $resp = process_post();
}
else if ($method == "GET")
{
  $resp = process_get();
}
else
{
  $resp = array(
    "status" => "error",
    "msg"    => "unsupported http method"
  );
}

echo json_encode($resp);

/* * *************************************************************************** */

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
    return array(
      "status"  => "error",
      "msg" => $e->getMessage()
    );
  }
}

function process_post()
{
  /** just for testing purposes
   $data = <<<test
    {
    "first_name": "Jessica",
    "last_name": "Doe",
    "age": 37,
    "email": "jesssica.doe@example.com",
    "interests": [
    "Archery",
    "Painting",
    "Paintball",
    "Sportsball",
    "Music"
    ],
    "admission_date": "2017-01-08",
    "admission_time": "4:23pm",
    "is_active": null
    }
test;
  $json = $data;
**/
  $json = file_get_contents('php://input');

  try
  {
    $person = Person::from_json($json);
  }
  catch (Exception $e)
  {
    return array(
      "status"  => "error",
      "msg" => $e->getMessage()
    );
  }

  $person->save();
  return array(
    "status" => "success",
    "msg"    => $person->id
  );
}
