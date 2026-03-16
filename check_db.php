<?php
$db = new PDO('mysql:host=localhost;dbname=ventas_pos', 'root', '');
$res = $db->query('DESCRIBE productos')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($res);
?>
