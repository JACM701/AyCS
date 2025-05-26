<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { 
    redirect('home.php', false);
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AYCS2</title>
    <!-- Incluir solo los estilos necesarios -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="libs/css/main.css">
    <link rel="stylesheet" href="libs/css/custom.css">
    
    <style>
        /* Asegurar que html y body ocupen toda la altura */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Centrar el contenido del body usando flexbox */
        body {
            background: linear-gradient(135deg, #283593 0%, #1a237e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh; /* Asegura que ocupe toda la altura de la ventana */
            padding: 20px; /* Añade padding al body para evitar que el contenido toque los bordes en pantallas pequeñas */
            box-sizing: border-box;
        }

        /* Estilos para el contenedor principal del login */
        .login-page {
            width: 100%; /* Permite que ocupe el ancho disponible hasta el max-width */
            max-width: 400px; /* Controla el ancho máximo del formulario */
        }

        /* Estilos para la caja del formulario */
        .login-box {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 30px; /* Espaciado interno de la caja */
            width: 100%; /* Asegura que la caja ocupe todo el ancho de su contenedor (.login-page) */
            box-sizing: border-box;
        }

        /* Estilos del logo */
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo img {
            max-width: 150px; /* Ajusta el tamaño del logo si es necesario */
            height: auto;
        }

        /* Estilos del encabezado del login */
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #283593;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        /* Estilos del formulario */
        .login-form .form-group {
            margin-bottom: 20px;
        }

        .login-form label {
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            display: block; /* Asegura que la etiqueta esté en su propia línea */
        }

        .login-form .input-group {
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            width: 100%; /* Asegura que el grupo de input ocupe todo el ancho disponible */
        }

        .login-form .input-group-addon {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #283593;
        }

        .login-form .form-control {
            border: 1px solid #e9ecef;
            padding: 12px; /* Ajusta el padding si es necesario */
            height: auto;
        }

        .login-form .form-control:focus {
            border-color: #283593;
            box-shadow: 0 0 0 0.2rem rgba(40, 53, 147, 0.25);
        }

        /* Estilos del botón de login */
        .btn-login {
            background: linear-gradient(135deg, #283593 0%, #1a237e 100%);
            border: none;
            padding: 12px; /* Ajusta el padding del botón si es necesario */
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px); /* Pequeño efecto hover */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-login i {
            margin-right: 8px;
        }

        /* Media query para pantallas pequeñas */
        @media (max-width: 480px) {
            .login-box {
                padding: 20px; /* Reduce el padding en pantallas pequeñas */
            }
            
            .login-logo img {
                max-width: 120px; /* Ajusta el tamaño del logo en móviles */
            }
            
            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="libs/images/logo.png" alt="Logo de la empresa" class="img-responsive">
        </div>
        <div class="login-header">
            <h1>Bienvenido</h1>
            <p>Iniciar sesión en el sistema</p>
        </div>
        <?php echo display_msg($msg); ?>
        <form method="post" action="auth.php" class="login-form">
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
                <button type="submit" class="btn btn-primary btn-block btn-login">
                    <i class="glyphicon glyphicon-log-in"></i> Iniciar Sesión
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Incluir solo los scripts necesarios si hay alguno para el login -->
<!-- Por ahora, no parece haber scripts específicos del login, pero si los hay, se agregarían aquí -->

</body>
</html>
