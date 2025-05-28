-- Insertar roles
INSERT INTO `rol` (`ID`, `Nombre`) VALUES
(1, 'admin'),
(2, 'especial'),
(3, 'usuario');

-- Insertar usuarios
INSERT INTO `usuario` (`ID`, `Usuario`, `Nombre`, `Contrasena`, `Id_Rol`) VALUES
(1, 'admin', 'Administrador', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1),
(2, 'especial', 'Usuario Especial', 'ba36b97a41e7faf742ab09bf88405ac04f99599a', 2),
(3, 'usuario', 'Usuario Normal', '12dea96fec20593566ab75692c9949596833adc9', 3); 