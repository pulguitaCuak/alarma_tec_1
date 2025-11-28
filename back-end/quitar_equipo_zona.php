<?php
session_start();
if (!isset($_SESSION["id_user"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No autorizado"]);
    exit;
}

require_once "db.php";

header('Content-Type: application/json');

try {
    // Verificar que la solicitud sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido"]);
        exit;
    }

    // Verificar parámetros requeridos
    if (!isset($_POST['id_zona']) || !isset($_POST['id_equipo'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Parámetros incompletos"]);
        exit;
    }

    $id_zona = filter_var($_POST['id_zona'], FILTER_VALIDATE_INT);
    $id_equipo = filter_var($_POST['id_equipo'], FILTER_VALIDATE_INT);
    
    // Validar IDs
    if ($id_zona === false || $id_zona <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de zona inválido"]);
        exit;
    }

    if ($id_equipo === false || $id_equipo <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de equipo inválido"]);
        exit;
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    try {
        // Verificar que exista la relación antes de eliminarla
        $sqlCheck = "
            SELECT id_equipo_zona 
            FROM equipo_zona 
            WHERE id_zona = ? AND id_equipo = ?
        ";
        
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id_zona, $id_equipo]);
        $relacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$relacion) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "El equipo no está asignado a esta zona"]);
            exit;
        }

        // Eliminar la relación
        $sqlDelete = "
            DELETE FROM equipo_zona 
            WHERE id_zona = ? AND id_equipo = ?
        ";
        
        $stmtDelete = $pdo->prepare($sqlDelete);
        $resultado = $stmtDelete->execute([$id_zona, $id_equipo]);

        if (!$resultado) {
            $pdo->rollBack();
            throw new Exception("Error al ejecutar la eliminación");
        }

        // Confirmar transacción
        $pdo->commit();

        // Obtener información del equipo y zona para el mensaje de respuesta
        $sqlInfo = "
            SELECT 
                e.nombre as equipo_nombre,
                z.descripcion as zona_nombre
            FROM equipo e, zonas z
            WHERE e.id_equipo = ? AND z.id_zona = ?
        ";
        
        $stmtInfo = $pdo->prepare($sqlInfo);
        $stmtInfo->execute([$id_equipo, $id_zona]);
        $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true, 
            "message" => "Equipo '{$info['equipo_nombre']}' removido correctamente de la zona '{$info['zona_nombre']}'",
            "data" => [
                "id_equipo" => $id_equipo,
                "id_zona" => $id_zona,
                "equipo_nombre" => $info['equipo_nombre'],
                "zona_nombre" => $info['zona_nombre']
            ]
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Error en quitar_equipo_zona.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error en la base de datos al remover el equipo",
        "error" => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error general en quitar_equipo_zona.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error inesperado al remover el equipo"
    ]);
}
?>