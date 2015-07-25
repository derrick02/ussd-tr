<?php
//code deduced from github.com/le-yo/ussd-training/mshwari.php
//connection to the database
require_once("connect2.php");
//database table used is user
//fields represented in table'user' are 'id, username, phonenumber'

global $phoneNumber;

$phoneNumber = "0".substr(trim($_GET["phoneNumber"]),3,9);

$text= $_REQUEST['text'];

$result =  getLevel($text);

$level = $result['level'];
$message = $result['latest_message'];

//main switch board
switch (strtolower($level)) {
    case 0:
    $response = getHomeMenu();//options are registering or entering data
        break;
    case 1:
    $response = getLevelOneMenu($message);//option are user is asked for username or the other option is user retrives info from db
     break;
    case 2:
    $response = getLevelTwoMenu($message);//option is one, user gets confirmation of registration and data is saved in db
      break;
    default:
     $response = getHomeMenu();//options are registering or entering data
    break;
}

sendOutput($response,1);
exit;

$exploded_text = explode('*',$text);

print_r($exploded_text);
exit;


$input = getInput();

if ( $input['text'] == "" || $input['text'] == " " ) {
     // This is the first request. Note how we start the response with CON
     $response  = "Sorry, but we do not accept blank replies! Please try again, Thank you!";
	 sendOutput($response,2);
}

function getLevelTwoMenu($text){
		//this function will gather info from the previous function getLevelOneMenu
	$phoneNumber = "0".substr(trim($_GET["phoneNumber"]),3,9);
	$username = $text;
	$result = createStaff($phoneNumber,$username);
	$response  = "Hello ".$text."! Your info has been successively stored in our database. Kindly exit and confirm next time round";
	sendOutput($response,2);
	return $response;

}

function getLevelOneMenu($text){

  switch (strtolower($text)) {
	  //case 1 below is from getHomeMenu option 1
	  //we will add response to function as level two
      case 1:
		$phoneNumber = "0".substr(trim($_GET["phoneNumber"]),3,9);
		$result = getStaff($phoneNumber);
		$username = $result['username'];
		$phone = $result['phonenumber'];
		//lets check if this guy had already registered or not
		if($phoneNumber == $phone){
			$response = "Sorry ".$username.", but you cannot register twice. Please exit and confirm";
       		sendOutput($response,2);
			}
		$response = "Please choose a Username:";
        break;
		
		//case 2 below is from getHomeMenu option 2
      case 2:
		$phoneNumber = "0".substr(trim($_GET["phoneNumber"]),3,9);
		$result = getStaff($phoneNumber);
		//lets check first is this guy is registered or not
		if($result['phonenumber'] == "" || $result['phonenumber'] == " "){
			$response  = "Hello Visitor!\nKindly exit and register first. Thank you!".$phone;
			sendOutput($response,2);			
			}
		$username = $result['username'];
		$phone = $result['phonenumber'];
		$response  = "Hello ".$username."!\nThank you for registering with us ealier.\nYour number is ".$phone;
       	sendOutput($response,2);
       	break;
      
	  default:
        $response = "We could not understand your response";
       	sendOutput($response,2);
        break;
  }
  return $response;

}
function getHomeMenu(){
	//this is level (0) starting point; then we goto function levelOne (1) first case
	$response = "1.Registration Desk".PHP_EOL;
	//this is level (0) starting point; then we goto function levelOne (1) second case
	$response .= "2.Retreive MyInfo!";

  	return $response;
}

//verify if the id belongs to one of the staff members
function getInput(){
$input = array();
$input['sessionId']   = $_REQUEST["sessionId"];
$input['serviceCode'] = $_REQUEST["serviceCode"];
$input['phoneNumber'] = $_REQUEST["phoneNumber"];
$input['text']        = $_REQUEST["text"];

return $input;

}
function getLevel($text){
  if($text == ""){
    $response['level'] = 0;
  }else{
    $exploded_text = explode('*',$text);
    $response['level'] = count($exploded_text);
    $response['latest_message'] = end($exploded_text);

  }
  return $response;
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

//create users
function createStaff($phoneNumber,$username){
  $query = mysql_query("INSERT INTO users (phonenumber,username) VALUES ('$phoneNumber','$username')");
  return $query;
}

//get users
function getStaff($phoneNumber){
    $query = mysql_query("SELECT * FROM users WHERE phonenumber='$phoneNumber'");
    if (mysql_num_rows($query) > 0) {
        $row = mysql_fetch_assoc($query);
    } else {
      $row['phonenumber'] = 0;
    }
   return $row;
}

//delete users
function deleteStaff($phoneNumber){
    $query = mysql_query("DELETE FROM users WHERE phonenumber='$phoneNumber'");
	return $query;
	}
	
//update users
function updateStaffName($username, $phoneNumber){
    $query = mysql_query("UPDATE users SET username = '$username' WHERE phonenumber='$phoneNumber'");
	return $query;
	}
	
/// LESSONS LEARNT, enyewe ni poa kuingia daro :) B| respect ... Do not forget to "Stay awesome!" bro
?>
