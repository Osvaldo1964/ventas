<?php
$db = new PDO('mysql:host=localhost;dbname=ventas_pos', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    echo "Updating products table...\n";
    
    // Check which columns exist
    $cols = $db->query("DESCRIBE productos")->fetchAll(PDO::FETCH_COLUMN);
    
    $alter = "ALTER TABLE productos ";
    $changes = [];
    
    if (!in_array('codigo', $cols)) $changes[] = "ADD COLUMN codigo VARCHAR(100) AFTER categoria_id";
    if (!in_array('precio_costo', $cols)) $changes[] = "ADD COLUMN precio_costo DECIMAL(15,2) DEFAULT 0.00 AFTER descripcion";
    if (!in_array('permite_descuento', $cols)) $changes[] = "ADD COLUMN permite_descuento TINYINT(1) DEFAULT 1 AFTER precio_venta";
    if (!in_array('aplica_iva', $cols)) $changes[] = "ADD COLUMN aplica_iva TINYINT(1) DEFAULT 0 AFTER permite_descuento";
    if (!in_array('iva_porcentaje', $cols)) $changes[] = "ADD COLUMN iva_porcentaje DECIMAL(5,2) DEFAULT 0.00 AFTER aplica_iva";
    if (!in_array('activo', $cols)) $changes[] = "ADD COLUMN activo TINYINT(1) DEFAULT 1 AFTER iva_porcentaje";

    if (!empty($changes)) {
        $db->exec($alter . implode(", ", $changes));
        echo "Products table updated.\n";
    } else {
        echo "Products table already up to date.\n";
    }
} catch(Exception $e) {
    echo "Error updating products: " . $e->getMessage() . "\n";
}

try {
    echo "Updating categorias table...\n";
    $cols = $db->query("DESCRIBE categorias")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('imagen_url', $cols)) {
        $db->exec("ALTER TABLE categorias ADD COLUMN imagen_url VARCHAR(255)");
        echo "Added imagen_url to categorias.\n";
    } else {
        echo "Categorias already has imagen_url.\n";
    }
} catch(Exception $e) {
    echo "Error updating categorias: " . $e->getMessage() . "\n";
}

// Ensure uploads directories exist
$dirs = ['public/uploads/productos', 'public/uploads/categorias'];
foreach($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir\n";
    }
}
?>
