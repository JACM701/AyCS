<?php
require_once('includes/load.php');

// Verificar que se recibió el ID del kit
if(!isset($_GET['kit_id'])) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ID de kit']);
    exit;
}

$kit_id = (int)$_GET['kit_id'];

// Obtener los productos del kit
$sql = "SELECT p.ID as id, p.Nombre as nombre, p.Precio as precio, kp.Cantidad as cantidad 
        FROM kit_producto kp 
        JOIN producto p ON kp.Id_Producto = p.ID 
        WHERE kp.Id_Kit = '{$kit_id}'";

$products = find_by_sql($sql);

// Formatear la respuesta
$response = [
    'success' => true,
    'products' => array_map(function($product) {
        return [
            'id' => (int)$product['id'],
            'nombre' => $product['nombre'],
            'precio' => (float)$product['precio'],
            'cantidad' => (int)$product['cantidad']
        ];
    }, $products)
];

// Enviar respuesta
header('Content-Type: application/json');
echo json_encode($response); 