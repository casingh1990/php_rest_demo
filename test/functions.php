<?php

function display_notice_message($message){
  display_custom_message($message, "alert-info");
}

function display_success_message($message){
  display_custom_message($message, "alert-success");
}

function display_error_message($message){
  display_custom_message($message, "alert-warning");
}

function display_custom_message($message, $class){
  echo "<div class=\"alert $class\">$message</div>";
}

function get_test_data(){
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
      "is_active" : true
  }
test;
  return $data;
}

function update_person($person, $field, $value){
  display_notice_message("Updating $field with $value");
  $rest_client = new RestClient();
  if ($person !== null){
    try{
      //editing person information
      $person->$field = $value;
      $result = $rest_client->do_put(json_encode($person));
      if ($result->status == "success"){
        display_success_message("Successfully updated $field with $value");
      }
      else{
        display_error_message("Error updating $field with $value");
      }
    }
    catch(Exception $e){
      display_error_message("error updating <br />\n" . $e->getMessage());
    }//end exception
 }//end updating age
}//end function update_person

function get_and_display_person($id){
  display_notice_message("Making GET request");
  $rest_client = new RestClient();
  try{
    $resp = $rest_client->do_get("?id=" . $id);
    /**
  	 * If get operation was successful
  	 **/
    if ($resp->status === "success"){
      display_success_message("GET request successful");
      $person = $resp->msg;
  		display_person($person);
      return $person;
    }//end successful get request
    else{
      display_error_message($resp->msg);
    }//end failed get request
  }
  catch(Exception $e){
    display_error_message($e->getMessage());
  }
  return null;
}
function display_person($person){
  ?>
  <div class="container">
    <div class="row text-warning">
      <div class="col-sm-6 text-primary">ID</div>
      <div class="col-sm-6"><?php echo $person->id; ?></div>
    </div><!-- end row -->
    <div class="row bg-success">
      <div class="col-sm-6 text-primary">First Name</div>
      <div class="col-sm-6"><?php echo $person->first_name; ?></div>
    </div><!-- end row -->
    <div class="row text-warning">
      <div class="col-sm-6 text-primary">Last Name</div>
      <div class="col-sm-6"><?php echo $person->last_name; ?></div>
    </div><!-- end row -->
    <div class="row  bg-success">
      <div class="col-sm-6 text-primary">Age</div>
      <div class="col-sm-6"><?php echo $person->age; ?></div>
    </div><!-- end row -->
    <div class="row text-warning">
      <div class="col-sm-6 text-primary">Email</div>
      <div class="col-sm-6"><?php echo $person->email; ?></div>
    </div><!-- end row -->
    <div class="row bg-success">
      <div class="col-sm-6  text-primary">Interests</div>
      <div class="col-sm-6"><?php foreach ($person->interests as $interest){
        echo $interest . "<br />\n";
      } ?></div>
    </div><!-- end row -->
    <div class="row text-warning">
      <div class="col-sm-6  text-primary">Admission Date</div>
      <div class="col-sm-6"><?php echo $person->admission_date; ?></div>
    </div><!-- end row -->
    <div class="row bg-success">
      <div class="col-sm-6 text-primary">Admission Time</div>
      <div class="col-sm-6"><?php echo $person->admission_time; ?></div>
    </div><!-- end row -->
    <div class="row text-warning">
      <div class="col-sm-6 text-primary">Is Active</div>
      <div class="col-sm-6"><?php echo (($person->is_active)?"true":"false"); ?></div>
    </div><!-- end row -->
  </div><!-- end container -->
  <?php
}
