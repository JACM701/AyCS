<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if(empty($errors)){
    $user = authenticate_v2($username, $password);

    if($user):
        //create session with id
        $session->login($user['ID']);
        //Update Sign in time
        updateLastLogIn($user['ID']);
        // redirect user to group home page by user level
        switch($user['Id_Rol']) {
            case '1': // Administrador
                $session->msg("s", "Hola ".$user['Usuario'].", Bienvenido a AyCS INV.");
                redirect('admin.php', false);
                break;
            case '2': // Usuario Especial
                $session->msg("s", "Hola ".$user['Usuario'].", Bienvenido a AyCS INV.");
                redirect('special_dashboard.php', false);
                break;
            case '3': // Usuario Normal
                $session->msg("s", "Hola ".$user['Usuario'].", Bienvenido a AyCS INV.");
                redirect('home.php', false);
                break;
            default:
                $session->msg("d", "Rol de usuario no válido");
                redirect('index.php', false);
        }
    else:
        $session->msg("d", "Usuario o contraseña incorrectos.");
        redirect('index.php', false);
    endif;
} else {
    $session->msg("d", $errors);
    redirect('index.php', false);
}
?>
