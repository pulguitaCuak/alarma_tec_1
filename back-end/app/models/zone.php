<?php
require_once __DIR__ . '/../core/database.php';

class Zone {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Traer todas las zonas con el último estado de sensor
    public function getAllZonesWithStatus() {
        $sql = "
            SELECT 
                z.idZone,
                z.description AS zone_name,
                ss.description AS status_description,
                ss.dateTime AS last_update
            FROM zone z
            LEFT JOIN statussensor ss 
                ON z.idZone = ss.idZone
                AND ss.dateTime = (
                    SELECT MAX(dateTime) 
                    FROM statussensor 
                    WHERE idZone = z.idZone
                )
            ORDER BY z.idZone ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
