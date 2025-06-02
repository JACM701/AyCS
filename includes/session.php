<?php
 session_start();

class Session {

  private $logged_in = false;
  public $user_id;
  public $message;

 function __construct(){
   $this->check_message();
   $this->check_login();
 }

  public function isUserLoggedIn($value=""){
    if(isset($_SESSION['user_id'])){
      return true;
    }
    return false;
  }
  public function login($user_id){
    if($user_id){
      $this->user_id = $_SESSION['user_id'] = $user_id;
      $this->logged_in = true;
    }
  }
  private function userLoginSetup()
  {
    if(isset($_SESSION['user_id'])){
      $this->user_id = $_SESSION['user_id'];
      $this->logged_in = true;
    } else {
      unset($this->user_id);
      $this->logged_in = false;
    }
  }
  public function logout(){
    unset($_SESSION['user_id']);
    unset($this->user_id);
    $this->logged_in = false;
  }

  public function msg($type ='', $msg =''){
    if(!empty($msg)){
      if(strlen(trim($msg)) > 0){
        $_SESSION['msg'] = array($type => $msg);
      }
    } else {
      return $this->message;
    }
  }

  private function check_message(){
    if(isset($_SESSION['msg'])){
      $this->message = $_SESSION['msg'];
      unset($_SESSION['msg']);
    } else {
      $this->message = "";
    }
  }

  private function check_login(){
    if(isset($_SESSION['user_id'])){
      $this->user_id = $_SESSION['user_id'];
      $this->logged_in = true;
    } else {
      unset($this->user_id);
      $this->logged_in = false;
    }
  }

  private function sread($name){
    // ... existing code ...
  }
}

$session = new Session();
$msg = $session->msg();

?>
