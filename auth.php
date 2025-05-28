<?php include_once('includes/load.php'); ?>
<?php
require_once('includes/load.php');

if(isset($_POST['login'])){
    $username = remove_junk($db->escape($_POST['username']));
    $password = remove_junk($db->escape($_POST['password']));

    // Debug: Mostrar los valores recibidos
    error_log("Intento de login - Usuario: " . $username);

    if(empty($username) || empty($password)){
        $session->msg("d", "Por favor ingrese usuario y contraseña");
        redirect('index.php', false);
    }

    // Intentar autenticar usando authenticate_v2
    $user = authenticate_v2($username, $password);
    
    if($user){
        // Debug: Mostrar información del usuario autenticado
        error_log("Usuario autenticado - ID: " . $user['ID'] . ", Rol: " . $user['Id_Rol']);
        
        $session->login($user['ID']);
        updateLastLogIn($user['ID']);
        
        if($user['Id_Rol'] === '1'){
            $session->msg("s", "Bienvenido al Panel de Administración");
            redirect('admin.php', false);
        } elseif($user['Id_Rol'] === '2'){
            $session->msg("s", "Bienvenido al Panel de Usuario Especial");
            redirect('special_dashboard.php', false);
        } else {
            $session->msg("s", "Bienvenido al Panel de Usuario");
            redirect('home.php', false);
        }
    } else {
        // Debug: Mostrar error de autenticación
        error_log("Error de autenticación para usuario: " . $username);
        $session->msg("d", "Usuario o contraseña incorrectos");
        redirect('index.php', false);
    }
}
?>
