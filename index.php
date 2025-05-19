<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { 
    redirect('home.php', false);
  }
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

<?php include_once('layouts/footer.php'); ?>
