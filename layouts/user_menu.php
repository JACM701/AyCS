<ul>
  <li>
    <a href="user_dashboard.php">
      <i class="glyphicon glyphicon-home"></i>
      <span>Inicio</span>
    </a>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-shopping-cart"></i>
      <span>Ventas</span>
    </a>
    <ul class="nav submenu">
      <li><a href="sales.php">Registro de ventas</a></li>
      <li><a href="add_sale.php">Nueva venta</a></li>
    </ul>
  </li>
  <li>
    <a href="profile.php?id=<?php echo (int)$user['id']; ?>">
      <i class="glyphicon glyphicon-user"></i>
      <span>Mi Perfil</span>
    </a>
  </li>
</ul>
