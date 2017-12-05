<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>CASingh Vector Application</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
    .container{
      border-right: 1px solid #AAAAAA;
			border-left: 1px solid #AAAAAA;
			border-bottom: 1px solid #AAAAAA;
			width: 50%;
    }
		.row{
			border-top: 1px solid #AAAAAA;
		}

    </style>
</head>

<body>
  <div>
    <h2>Demo Vector Assessment</h2>
<?php

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

$data_string = $data; //json_encode($data);

echo '<div class="alert alert-info">Making Post Request</div>';

$ch = curl_init('http://casingh.me/projects/vector_assessment/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
);

$result_json = curl_exec($ch);
$result = json_decode($result_json);

if ($result->status === "success"){

  echo '<div class="alert alert-success">Post requst successful</div>' . "\n";
  echo '<div class="alert alert-info">Making GET request</div>' . "\n";

  $new_req  = curl_init('http://casingh.me/projects/vector_assessment/?id=' . $result->msg);
  curl_setopt($new_req, CURLOPT_RETURNTRANSFER, true);
  $new_result_json = curl_exec($new_req);
  $resp = json_decode($new_result_json);

  if ($resp->status === "success"){
    $person = $resp->msg;
		$datetime = DateTime::createfromformat("Y-m-d H:i:s.u", $person->admission_date->date);
    ?>
    <div class="alert alert-success">
      Successfully Created Person
    </div>
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
          echo $interest->name . "<br />\n";
        } ?></div>
      </div><!-- end row -->
      <div class="row text-warning">
        <div class="col-sm-6  text-primary">Admission Date</div>
        <div class="col-sm-6"><?php echo $datetime->format("Y-m-d"); ?></div>
      </div><!-- end row -->
      <div class="row bg-success">
        <div class="col-sm-6 text-primary">Admission Time</div>
        <div class="col-sm-6"><?php echo $datetime->format("g:iA"); ?></div>
      </div><!-- end row -->
      <div class="row text-warning">
        <div class="col-sm-6 text-primary">Is Active</div>
        <div class="col-sm-6"><?php echo (($person->is_active)?"true":"false"); ?></div>
      </div><!-- end row -->
    </div><!-- end container -->
    <?php
  }//end successful get request
  else{
    echo "<div class=\"alert alert-warning\">" . $resp->msg . "</div>";
  }//end failed get request

}//end Successfully posted person
else{
  echo "<div class=\"alert alert-warning\">" . $result->msg . "</div>";
}
?>
</div>
</body>
</html>
