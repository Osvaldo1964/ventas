<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class CajaController extends Controller {

    // ──────────────────────────────────────────────
    // CAJAS (catálogo de cajas de la tienda)
    // ──────────────────────────────────────────────

    // GET /api/cajas
    public function index() {
        $payload = AuthMiddleware::authenticate();
        $t = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare(
            "SELECT c.*, s.nombre AS sede_nombre
             FROM cajas c
             JOIN sedes s ON c.sede_id = s.id
             WHERE c.tenant_id = ?
             ORDER BY c.id ASC"
        );
        $stmt->execute([$t]);
        $this->jsonResponse(['error' => false, 'data' => $stmt->fetchAll()]);
    }

    // POST /api/cajas
    public function storeCaja() {
        $payload = AuthMiddleware::authenticate();
        $t = $payload['tenant_id'];
        $data = $this->getPostData();
        if (empty($data['nombre'])) {
            $this->jsonResponse(['error' => true, 'message' => 'El nombre de la caja es requerido.'], 422);
        }
        $db = (new Database())->getConnection();
        // Usar primera sede si no se envía sede_id
        if (empty($data['sede_id'])) {
            $s = $db->prepare("SELECT id FROM sedes WHERE tenant_id = ? LIMIT 1");
            $s->execute([$t]);
            $data['sede_id'] = $s->fetchColumn() ?: 0;
        }
        $stmt = $db->prepare("INSERT INTO cajas (tenant_id, sede_id, nombre) VALUES (?, ?, ?)");
        $stmt->execute([$t, $data['sede_id'], $data['nombre']]);
        $this->jsonResponse(['error' => false, 'message' => 'Caja creada.', 'id' => $db->lastInsertId()], 201);
    }

    // DELETE /api/cajas/{id}
    public function destroyCaja($id) {
        $payload = AuthMiddleware::authenticate();
        $t = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM cajas WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $t]);
        $this->jsonResponse(['error' => false, 'message' => 'Caja eliminada.']);
    }

    // ──────────────────────────────────────────────
    // SESIONES DE CAJA
    // ──────────────────────────────────────────────

    // GET /api/sesiones-caja  — sesiones del tenant
    public function sesiones() {
        $payload = AuthMiddleware::authenticate();
        $t = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare(
            "SELECT sc.*, c.nombre AS caja_nombre, u.nombre AS cajero_nombre
             FROM sesiones_caja sc
             JOIN cajas c ON sc.caja_id = c.id
             JOIN usuarios u ON sc.usuario_id = u.id
             WHERE sc.tenant_id = ?
             ORDER BY sc.fecha_apertura DESC
             LIMIT 50"
        );
        $stmt->execute([$t]);
        $this->jsonResponse(['error' => false, 'data' => $stmt->fetchAll()]);
    }

    // GET /api/sesiones-caja/activa  — sesión abierta del usuario actual
    public function sesionActiva() {
        $payload = AuthMiddleware::authenticate();
        $t  = $payload['tenant_id'];
        $uid = $payload['user_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare(
            "SELECT sc.*, c.nombre AS caja_nombre
             FROM sesiones_caja sc
             JOIN cajas c ON sc.caja_id = c.id
             WHERE sc.tenant_id = ? AND sc.usuario_id = ? AND sc.estado = 'Abierta'
             ORDER BY sc.fecha_apertura DESC LIMIT 1"
        );
        $stmt->execute([$t, $uid]);
        $sesion = $stmt->fetch();
        $this->jsonResponse(['error' => false, 'data' => $sesion ?: null]);
    }

    // POST /api/sesiones-caja/abrir
    public function abrirSesion() {
        $payload = AuthMiddleware::authenticate();
        $t   = $payload['tenant_id'];
        $uid = $payload['user_id'];
        $db  = (new Database())->getConnection();

        // Verificar que no tenga sesión abierta
        $check = $db->prepare(
            "SELECT id FROM sesiones_caja WHERE tenant_id = ? AND usuario_id = ? AND estado = 'Abierta'"
        );
        $check->execute([$t, $uid]);
        if ($check->fetch()) {
            $this->jsonResponse(['error' => true, 'message' => 'Ya tienes una sesión de caja abierta.'], 422);
        }

        $data = $this->getPostData();
        if (empty($data['caja_id'])) {
            $this->jsonResponse(['error' => true, 'message' => 'Debes seleccionar una caja.'], 422);
        }

        // Verificar que la caja pertenezca al tenant
        $cajCheck = $db->prepare("SELECT id FROM cajas WHERE id = ? AND tenant_id = ?");
        $cajCheck->execute([$data['caja_id'], $t]);
        if (!$cajCheck->fetch()) {
            $this->jsonResponse(['error' => true, 'message' => 'Caja no válida.'], 422);
        }

        $base = floatval($data['base_apertura'] ?? 0);
        $stmt = $db->prepare(
            "INSERT INTO sesiones_caja (tenant_id, caja_id, usuario_id, estado, base_apertura, fecha_apertura)
             VALUES (?, ?, ?, 'Abierta', ?, NOW())"
        );
        $stmt->execute([$t, $data['caja_id'], $uid, $base]);
        $sesionId = $db->lastInsertId();

        $this->jsonResponse([
            'error'    => false,
            'message'  => 'Sesión de caja abierta exitosamente.',
            'sesion_id' => $sesionId
        ], 201);
    }

    // POST /api/sesiones-caja/{id}/cerrar
    public function cerrarSesion($id) {
        $payload = AuthMiddleware::authenticate();
        $t   = $payload['tenant_id'];
        $uid = $payload['user_id'];
        $db  = (new Database())->getConnection();

        // Obtener sesión
        $stmt = $db->prepare(
            "SELECT * FROM sesiones_caja WHERE id = ? AND tenant_id = ? AND usuario_id = ? AND estado = 'Abierta'"
        );
        $stmt->execute([$id, $t, $uid]);
        $sesion = $stmt->fetch();
        if (!$sesion) {
            $this->jsonResponse(['error' => true, 'message' => 'Sesión no encontrada o ya cerrada.'], 404);
        }

        $data = $this->getPostData();
        $totalDeclarado = floatval($data['total_declarado'] ?? 0);

        // Calcular totales de movimientos_caja
        $movs = $db->prepare(
            "SELECT tipo, COALESCE(SUM(monto), 0) AS total FROM movimientos_caja WHERE sesion_caja_id = ? GROUP BY tipo"
        );
        $movs->execute([$id]);
        $totales = ['Ingreso' => 0, 'Gasto' => 0];
        foreach ($movs->fetchAll() as $row) {
            $totales[$row['tipo']] = floatval($row['total']);
        }

        $totalEsperado = floatval($sesion['base_apertura']) + $totales['Ingreso'] - $totales['Gasto'];
        $diferencia    = $totalDeclarado - $totalEsperado;

        $update = $db->prepare(
            "UPDATE sesiones_caja
             SET estado = 'Cerrada', fecha_cierre = NOW(),
                 total_ingresos = ?, total_gastos = ?,
                 total_declarado = ?, diferencia = ?
             WHERE id = ?"
        );
        $update->execute([$totales['Ingreso'], $totales['Gasto'], $totalDeclarado, $diferencia, $id]);

        $this->jsonResponse([
            'error'          => false,
            'message'        => 'Sesión cerrada correctamente.',
            'total_esperado' => $totalEsperado,
            'diferencia'     => $diferencia
        ]);
    }

    // GET /api/sesiones-caja/{id}/movimientos
    public function movimientosDeSesion($id) {
        $payload = AuthMiddleware::authenticate();
        $t = $payload['tenant_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare(
            "SELECT mc.*, cc.nombre AS concepto_nombre
             FROM movimientos_caja mc
             LEFT JOIN conceptos_caja cc ON mc.concepto_id = cc.id
             WHERE mc.sesion_caja_id = ? AND mc.tenant_id = ?
             ORDER BY mc.fecha DESC"
        );
        $stmt->execute([$id, $t]);
        $this->jsonResponse(['error' => false, 'data' => $stmt->fetchAll()]);
    }

    // POST /api/sesiones-caja/{id}/movimientos
    public function registrarMovimiento($id) {
        $payload = AuthMiddleware::authenticate();
        $t = $payload['tenant_id'];
        $db = (new Database())->getConnection();

        // Verificar que la sesión esté abierta
        $check = $db->prepare("SELECT id FROM sesiones_caja WHERE id = ? AND tenant_id = ? AND estado = 'Abierta'");
        $check->execute([$id, $t]);
        if (!$check->fetch()) {
            $this->jsonResponse(['error' => true, 'message' => 'Sesión no activa.'], 422);
        }

        $data = $this->getPostData();
        if (empty($data['tipo']) || empty($data['monto'])) {
            $this->jsonResponse(['error' => true, 'message' => 'Tipo y monto son requeridos.'], 422);
        }

        $stmt = $db->prepare(
            "INSERT INTO movimientos_caja (tenant_id, sesion_caja_id, concepto_id, tipo, monto, observacion)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $t, $id,
            $data['concepto_id'] ?? null,
            $data['tipo'], $data['monto'],
            $data['observacion'] ?? null
        ]);
        $this->jsonResponse(['error' => false, 'message' => 'Movimiento registrado.'], 201);
    }
}
