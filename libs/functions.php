function authenticate_v2($username='', $password='') {
  global $db;
  $username = remove_junk($db->escape($username));
  $password = remove_junk($db->escape($password));
  $sql = "SELECT id, username, password, user_level FROM users WHERE username = '{$username}' LIMIT 1";
  $result = $db->query($sql);
  if($db->num_rows($result)){
    $user = $db->fetch_assoc($result);
    $password_request = sha1($password);
    if($password_request === $user['password'] ){
      return $user;
    }
  }
  return false;
} 