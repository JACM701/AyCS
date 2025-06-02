<?php
$page_title = 'Ver Cotización';
require_once('includes/load.php');
page_require_level(1);

// Obtener ID de la cotización
$quote_id = (int)$_GET['id'];

// Obtener información de la cotización
$sql = "SELECT c.*, cl.Nombre as client_name, cl.Correo as client_email, 
        cl.Numero_Telefono as client_phone, cl.Direccion as client_address
        FROM cotizacion c
        LEFT JOIN cliente cl ON c.Id_Cliente = cl.ID
        WHERE c.ID = '{$quote_id}'";
$quote = find_by_sql($sql);
$quote = $quote[0];

// Obtener detalles de la cotización
$sql = "SELECT dc.*, 
        p.Nombre as product_name, p.Precio as product_price,
        s.Nombre as service_name, s.Costo as service_cost
        FROM detalle_cotizacion dc
        LEFT JOIN producto p ON dc.Id_Producto = p.ID
        LEFT JOIN servicio s ON dc.Id_Servicio = s.ID
        WHERE dc.Id_Cotizacion = '{$quote_id}'";
$quote_items = find_by_sql($sql);
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Detalles de la Cotización</span>
                </strong>
                <div class="pull-right">
                    <a href="quotes.php" class="btn btn-default">Volver</a>
                    <a href="edit_quote.php?id=<?php echo (int)$quote['ID']; ?>" class="btn btn-warning">Editar</a>
                    <a href="delete_quote.php?id=<?php echo $quote_id; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar esta cotización?');">Eliminar</a>
                </div>
            </div>
            <div class="panel-body">
                <!-- Información del Cliente -->
                <div class="row">
                    <div class="col-md-6">
                        <h4>Información del Cliente</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>Nombre:</th>
                                <td><?php echo remove_junk($quote['client_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Correo:</th>
                                <td><?php echo remove_junk($quote['client_email']); ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td><?php echo remove_junk($quote['client_phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Dirección:</th>
                                <td><?php echo remove_junk($quote['client_address']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Información de la Cotización</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>Número:</th>
                                <td><?php echo remove_junk($quote['ID']); ?></td>
                            </tr>
                            <tr>
                                <th>Fecha:</th>
                                <td><?php echo date('d/m/Y', strtotime($quote['Fecha'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items de la Cotización -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Items de la Cotización</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Descripción</th>
                                    <th class="text-center" style="width: 100px;">Tipo</th>
                                    <th class="text-center" style="width: 100px;">Precio</th>
                                    <th class="text-center" style="width: 100px;">Descuento</th>
                                    <th class="text-center" style="width: 100px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach($quote_items as $item): 
                                    $item_price = !empty($item['product_price']) ? $item['product_price'] : $item['service_cost'];
                                    $item_name = !empty($item['product_name']) ? $item['product_name'] : $item['service_name'];
                                    $item_type = !empty($item['product_name']) ? 'Producto' : 'Servicio';
                                    $discount = $item['Descuento'] ?? 0;
                                    $item_total = $item_price - $discount;
                                    $total += $item_total;
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo count_id(); ?></td>
                                    <td><?php echo remove_junk($item_name); ?></td>
                                    <td class="text-center"><?php echo $item_type; ?></td>
                                    <td class="text-right">$<?php echo number_format($item_price, 2); ?></td>
                                    <td class="text-right">$<?php echo number_format($discount, 2); ?></td>
                                    <td class="text-right">$<?php echo number_format($item_total, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Total:</strong></td>
                                    <td class="text-right"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.panel {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}

.panel:hover {
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}

.table > tbody > tr > td {
    vertical-align: middle;
}

.btn {
    margin-left: 5px;
}

h4 {
    color: #283593;
    font-weight: 600;
    margin-bottom: 20px;
}
</style>

<?php include_once('layouts/footer.php'); ?> 