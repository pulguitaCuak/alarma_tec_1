<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/app/models/Zone.php";

try {
    $zoneModel = new Zone();
    $zones = $zoneModel->getAllZonesWithStatus();

    // Construir array de cards con iconos según el estado
    $cards = [];
    foreach ($zones as $zone) {
        $icon = "";
        $iconColor = "";

        if ($zone['status_description']) {
            $desc = strtolower($zone['status_description']);
            if (str_contains($desc, 'alerta')) {
                $icon = "bi-exclamation-triangle";
                $iconColor = "text-warning";
            } else if (str_contains($desc, 'error')) {
                $icon = "bi-exclamation-triangle-fill";
                $iconColor = "text-danger";
            } else if (str_contains($desc, 'offline')) {
                $icon = "bi-wifi-off";
                $iconColor = "text-dark";
            }
        }

        $cards[] = [
            "titulo" => $zone['zone_name'],
            "descripcion" => $zone['status_description'] ?? "Sin estado",
            "icon" => $icon,
            "iconColor" => $iconColor
        ];
    }

    echo json_encode($cards, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
