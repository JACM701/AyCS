<!-- Botón para abrir/cerrar el menú -->
<div class="sidebar-toggle">
  <i class="glyphicon glyphicon-menu-hamburger"></i>
</div>

<ul>
  <li>
    <a href="home.php">
      <i class="glyphicon glyphicon-home"></i>
      <span>Inicio</span>
    </a>
  </li>
  <li>
    <a href="admin.php">
      <i class="glyphicon glyphicon-dashboard"></i>
      <span>Panel de control</span>
    </a>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-cog"></i>
      <span>Configuración</span>
    </a>
    <ul class="nav submenu">
      <li><a href="group.php">Grupos de usuarios</a></li>
      <li><a href="users.php">Usuarios</a></li>
      <li><a href="categorie.php">Categorías</a></li>
    </ul>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-th-large"></i>
      <span>Inventario</span>
    </a>
    <ul class="nav submenu">
      <li><a href="product.php">Productos</a></li>
      <li><a href="add_product.php">Agregar producto</a></li>
      <li><a href="media.php">Galería de imágenes</a></li>
    </ul>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-list-alt"></i>
      <span>Transacciones</span>
    </a>
    <ul class="nav submenu">
      <li><a href="sales.php">Registro de ventas</a></li>
      <li><a href="add_sale.php">Nueva venta</a></li>
      <li><a href="quotes.php">Cotizaciones</a></li>
    </ul>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-signal"></i>
      <span>Reportes</span>
    </a>
    <ul class="nav submenu">
      <li><a href="sales_report.php">Ventas por fecha</a></li>
      <li><a href="monthly_sales.php">Ventas mensuales</a></li>
      <li><a href="daily_sales.php">Ventas diarias</a></li>
    </ul>
  </li>
</ul>

<!-- Información del usuario -->
<div class="user-info">
  <?php
    $user = current_user();
    $user_image = $user['image'] ?? 'no_image.jpg';
  ?>
  <div class="user-profile">
    <img src="uploads/users/<?php echo $user_image; ?>" alt="User Image" class="user-image">
    <div class="user-details">
      <span class="user-name"><?php echo remove_junk($user['name']); ?></span>
      <span class="user-role"><?php echo remove_junk($user['username']); ?></span>
    </div>
  </div>
</div>

<style>
.sidebar {
  transition: all 0.3s ease;
}

.sidebar.collapsed {
  width: 60px;
}

.sidebar.collapsed span,
.sidebar.collapsed .submenu {
  display: none;
}

.sidebar-toggle {
  position: absolute;
  right: -15px;
  top: 20px;
  background: #283593;
  color: white;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.user-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 15px;
  background: rgba(0,0,0,0.1);
  border-top: 1px solid rgba(255,255,255,0.1);
}

.user-profile {
  display: flex;
  align-items: center;
  color: #fff;
}

.user-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
  border: 2px solid rgba(255,255,255,0.2);
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
}

.user-role {
  font-size: 12px;
  opacity: 0.8;
}

.sidebar.collapsed .user-info {
  padding: 10px 5px;
}

.sidebar.collapsed .user-details {
  display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.querySelector('.sidebar');
  const toggleBtn = document.querySelector('.sidebar-toggle');
  
  toggleBtn.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
  });
});
</script>
