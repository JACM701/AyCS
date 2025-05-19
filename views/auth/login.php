<?php
$title = "Iniciar Sesión";
?>

<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <img src="<?php echo APP_URL; ?>/libs/images/logo.png" alt="Logo">
        </div>
        
        <h2 class="text-center"><?php echo APP_NAME; ?></h2>
        
        <?php echo display_msg($msg); ?>
        
        <form method="post" action="index.php?controller=auth&action=login" class="clearfix">
            <div class="form-group">
                <label for="username" class="control-label">Usuario</label>
                <input type="text" class="form-control" name="username" placeholder="Usuario">
            </div>
            
            <div class="form-group">
                <label for="password" class="control-label">Contraseña</label>
                <input type="password" class="form-control" name="password" placeholder="Contraseña">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>

<style>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.login-box {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

.login-logo {
    text-align: center;
    margin-bottom: 20px;
}

.login-logo img {
    max-width: 150px;
    height: auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    height: 45px;
    border-radius: 4px;
}

.btn-primary {
    height: 45px;
    font-size: 16px;
    font-weight: 500;
}

.alert {
    margin-bottom: 20px;
}
</style> 