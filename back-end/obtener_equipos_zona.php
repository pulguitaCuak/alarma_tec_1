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
    // Verificar si se proporcionó el ID de la zona
    if (!isset($_GET['id_zona']) || empty($_GET['id_zona'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de zona no proporcionado"]);
        exit;
    }

    $id_zona = filter_var($_GET['id_zona'], FILTER_VALIDATE_INT);
    
    if ($id_zona === false || $id_zona <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID de zona inválido"]);
        exit;
    }

    // Consulta para obtener los equipos asignados a la zona
    $sql = "
        SELECT 
            e.id_equipo,
            e.nombre,
            e.descripcion,
            e.estado,
            ez.fecha_vinculo,
            est.descripcion as estado_descripcion
        FROM equipo_zona ez
        INNER JOIN equipo e ON ez.id_equipo = e.id_equipo
        LEFT JOIN estado est ON e.estado = est.id_estado
        WHERE ez.id_zona = ? 
        AND e.estado != 2  -- Excluir equipos eliminados
        ORDER BY e.nombre ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_zona]);
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear la respuesta
    $equiposFormateados = array_map(function($equipo) {
        return [
            'id_equipo' => (int)$equipo['id_equipo'],
            'nombre' => $equipo['nombre'],
            'descripcion' => $equipo['descripcion'] ?? '',
            'estado' => (int)$equipo['estado'],
            'estado_descripcion' => $equipo['estado_descripcion'] ?? 'Desconocido',
            'fecha_vinculo' => $equipo['fecha_vinculo']
        ];
    }, $equipos);

    echo json_encode($equiposFormateados);

} catch (PDOException $e) {
    error_log("Error en obtener_equipos_zona.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error en el servidor al obtener los equipos de la zona",
        "error" => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error general en obtener_equipos_zona.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Error inesperado en el servidor"
    ]);
}
?>