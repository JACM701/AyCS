<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php echo remove_junk($page_title); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="startbootstrap-sb-admin-gh-pages/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Estilos adicionales si son necesarios de tu tema original -->
         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="libs/css/main.css">
        <link rel="stylesheet" href="libs/css/custom.css">
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.css"/>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="home.php">AYCS2</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search (Optional) -->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <!-- Puedes agregar un campo de búsqueda aquí si lo necesitas -->
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i> <?php echo remove_junk(ucfirst($user['name'])); ?></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php?id=<?php echo (int)$user['id'];?>">Perfil</a></li>
                        <li><a class="dropdown-item" href="edit_account.php?id=<?php echo (int)$user['id'];?>">Configuración de Cuenta</a></li>
                         <li><a class="dropdown-item" href="change_password.php?id=<?php echo (int)$user['id'];?>">Cambiar Contraseña</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php">Salir</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                           <?php if($user['user_level'] === '1'): ?>
                            <!-- Contenido del menú para Admin -->
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="admin.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                             <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Configuración
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="group.php">Grupos de usuarios</a>
                                    <a class="nav-link" href="users.php">Usuarios</a>
                                     <a class="nav-link" href="categorie.php">Categorías</a>
                                </nav>
                            </div>
                             <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
                                <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                                Inventario
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseInventory" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="product.php">Productos</a>
                                    <a class="nav-link" href="add_product.php">Agregar producto</a>
                                </nav>
                            </div>
                               <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSales" aria-expanded="false" aria-controls="collapseSales">
                                <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                                Transacciones
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseSales" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="sales.php">Lista de ventas</a>
                                    <a class="nav-link" href="add_sale.php">Agregar venta</a>
                                     <a class="nav-link" href="quotes.php">Cotizaciones</a>
                                </nav>
                            </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Reportes
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                             <div class="collapse" id="collapseReports" aria-labelledby="headingFour" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="sales_report.php">Ventas por fecha</a>
                                    <a class="nav-link" href="monthly_sales.php">Ventas mensuales</a>
                                     <a class="nav-link" href="daily_sales.php">Ventas diarias</a>
                                </nav>
                            </div>
                             <?php elseif($user['user_level'] === '2'): ?>
                                 <!-- Contenido del menú para Special User -->
                               <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="special_dashboard.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard Especial
                            </a>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
                                <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                                Inventario
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseInventory" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="product.php">Productos</a>
                                    <a class="nav-link" href="add_product.php">Agregar producto</a>
                                </nav>
                            </div>
                               <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSales" aria-expanded="false" aria-controls="collapseSales">
                                <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                                Transacciones
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseSales" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="sales.php">Lista de ventas</a>
                                    <a class="nav-link" href="add_sale.php">Agregar venta</a>
                                     <a class="nav-link" href="quotes.php">Cotizaciones</a>
                                </nav>
                            </div>
                                 <?php elseif($user['user_level'] === '3'): ?>
                                  <!-- Contenido del menú para User -->
                                  <div class="sb-sidenav-menu-heading">Core</div>
                                    <a class="nav-link" href="home.php">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Dashboard Usuario
                                    </a>
                                       <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
                                        <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                                        Inventario
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="collapseInventory" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="product.php">Productos</a>
                                            <a class="nav-link" href="add_product.php">Agregar producto</a>
                                        </nav>
                                    </div>
                                       <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSales" aria-expanded="false" aria-controls="collapseSales">
                                        <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                                        Transacciones
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="collapseSales" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="sales.php">Lista de ventas</a>
                                            <a class="nav-link" href="add_sale.php">Agregar venta</a>
                                        </nav>
                                    </div>
                                 <?php endif; ?>
                        </div>
                    </div>
                     <div class="sb-sidenav-footer">
                        <div class="small">Conectado como:</div>
                        <?php echo remove_junk(ucfirst($user['username'])); ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <!-- El contenido específico de cada página irá aquí -->
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/simple-datatables.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html> 