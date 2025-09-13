<?php
require_once __DIR__ . '/../app/models/Zone.php';

header('Content-Type: application/json');

$zoneModel = new Zone();
echo json_encode($zoneModel->getAllZones());
