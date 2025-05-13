<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false);}
?>
<?php include_once('layouts/header.php'); ?>
<div class="main-container">
    <div class="login-container">
        <div class="login-page">
            <div class="text-center">
               <!-- Logo de la empresa -->
               <div class="logo-container">
                   <img src="libs/images/logo.png" alt="Logo de la empresa" class="img-responsive" style="max-width: 200px; margin: 0 auto 20px;">
               </div>
               <h1>Bienvenido</h1>
               <p>Iniciar sesión</p>
             </div>
             <?php echo display_msg($msg); ?>
              <form method="post" action="auth.php" class="clearfix">
                <div class="form-group">
                      <label for="username" class="control-label">Usuario</label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                          <input type="text" class="form-control" name="username" placeholder="Ingrese su usuario" required>
                      </div>
                </div>
                <div class="form-group">
                    <label for="Password" class="control-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
                    </div>
                </div>
                <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Sobrescribir estilos del main.css */
body {
    background-color: #1a237e !important; /* Azul marino oscuro */
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    min-height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

.main-container {
    width: 100% !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
}

.login-container {
    width: 100% !important;
    max-width: 400px !important;
    padding: 20px !important;
    margin: 0 auto !important;
}

.login-page {
    background: #fff !important;
    border-radius: 8px !important;
    box-shadow: 0 0 20px rgba(0,0,0,0.2) !important;
    padding: 30px !important;
    width: 100% !important;
    margin: 0 !important;
}

.logo-container {
    margin-bottom: 20px !important;
    text-align: center !important;
}

.form-group {
    margin-bottom: 20px !important;
}

.input-group {
    margin-bottom: 10px !important;
}

.input-group-addon {
    background-color: #283593 !important; /* Azul marino medio */
    border-color: #283593 !important;
    color: #fff !important;
}

.form-control {
    border-color: #e0e0e0 !important;
}

.form-control:focus {
    border-color: #283593 !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25) !important;
}

.btn-primary {
    background-color: #283593 !important; /* Azul marino medio */
    border-color: #283593 !important;
    padding: 10px !important;
    font-size: 16px !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
}

.btn-primary:hover {
    background-color: #1a237e !important; /* Azul marino oscuro */
    border-color: #1a237e !important;
    transform: translateY(-1px) !important;
}

.btn-block {
    margin-top: 20px !important;
}

.text-center h1 {
    color: #283593 !important; /* Azul marino medio */
    margin-bottom: 10px !important;
    font-weight: 600 !important;
}

.text-center p {
    color: #666 !important;
    margin-bottom: 20px !important;
}

.control-label {
    color: #283593 !important; /* Azul marino medio */
    font-weight: 500 !important;
}

/* Ocultar elementos innecesarios */
#header, .sidebar {
    display: none !important;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .login-container {
        width: 90% !important;
        margin: 0 auto !important;
    }
}
</style>

<?php include_once('layouts/footer.php'); ?>
