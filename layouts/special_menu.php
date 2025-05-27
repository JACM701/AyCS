<ul>
  <li>
    <a href="special_dashboard.php">
      <i class="glyphicon glyphicon-home"></i>
      <span>Inicio</span>
    </a>
  </li>
  <li>
    <a href="#" class="submenu-toggle">
      <i class="glyphicon glyphicon-th-large"></i>
      <span>Inventario</span>
    </a>
    <ul class="nav submenu">
      <li><a href="product.php">Productos</a></li>
      <li><a href="add_product.php">Agregar producto</a></li>
    </ul>
  </li>
  <li>
    <a href="profile.php?id=<?php echo (int)$user['id']; ?>">
      <i class="glyphicon glyphicon-user"></i>
      <span>Mi Perfil</span>
    </a>
  </li>
</ul>
