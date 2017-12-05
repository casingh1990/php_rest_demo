<?php
require_once 'RestClient.php';
require_once 'functions.php';
?>
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

$data = get_test_data();

echo '<div class="alert alert-info">Making Post Request</div>';
$rest_client = new RestClient();

try{
$result = $rest_client->do_post($data);
}
catch(Exception $e){
	display_error_message($e->getMessage());
}

/**
 * if create operation was successful
 **/
if (($result) && ($result->status === "success")){

  display_success_message("Post requst successful");

	/**
	 * Get the created person information
	 **/
	 $person = get_and_display_person($result->msg);
	 //update Age
	 update_person($person, "age", random_int(20,50));
	 //show person again
	 get_and_display_person($result->msg);

}//end Successfully posted person
else{
	if (!$result){
		display_error_message("Error Sending Request to Rest Server");
	}
	else{
	  display_error_message($result->msg);
	}
}
?>
</div>
</body>
</html>
