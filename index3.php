<?php
//was proposing we do it from scratch derrick02
incude("connect2.php");

$getsinput = getInput();
$input = $getsinput['text'];
$level = getLevel($input);
$message = $level['latest_message'];
$phoneNumber = $getsinput['phoneNumber'];

switch ($level) {
	case 0:
		getHomeMenu();
		break;
	case 1:
		getMenu1($message);
		break;
	case 2:
		getMenu2();
	
	default:
		getHomeMenu();
		break;
}

function getInput(){
	//Declare an array to get input from the user,
	//assign the user a session id, get phone id, and phone no.
	$input = array();
	$input['sessionId']   = $_REQUEST["sessionId"];
	$input['serviceCode'] = $_REQUEST["serviceCode"];
	$input['phoneNumber'] = $_REQUEST["phoneNumber"];
	$input['text']        = $_REQUEST["text"];

	return $input;
}

function sendOutput($message,$type=2){
	//Type 1 is a continuation, type 2 output is an end

	if($type==1){
		echo "CON ".$message;
	}elseif($type==2){
		echo "END ".$message;
	}else{
		echo "END We faced an error";
	}
	exit;
}

function getLevel($input){

	if($input == ""){
		$level['level'] = 0;
		}
	else{
		$exploded_text = explode('*', $input);
		$level['level'] = count($exploded_text);
		$level['latest_message'] = end($exploded_text);
		}

	return $level;

}

function createStaff($message){

  	$query = mysql_query("INSERT INTO users (username)
  		VALUES ('$message')");

  	return $query;
}

function getStaff($phoneNumber){
    $query = mysql_query("SELECT * FROM users WHERE phonenumber='$phoneNumber'");

    if (mysql_num_rows($query) > 0) {
        $row = mysql_fetch_assoc($query);
    } else {
      $row['phonenumber'] = 0;
    }

   return $row;

}

function getHomeMenu()){
	$response = "1.Register".PHP_EOL;
	$response .= "2.Retreive Info";
	sendOutput($response , 1);
}

function getMenu1($input){
	switch($input){
		case 1:
			$response = "Enter your names";
			createStaff($message);
			break;
		case 2:
			$response = getStaff($phoneNumber);
			sendOutput($response , 2);
			break;
		default:
			$response = getHomeMenu();
			break;
	}
		sendOutput($response , 1);
}
function getMenu2(){
	$response = getStaff($phoneNumber);
	sendOutput($response , 2);
	}


?>
