<?php
require_once __DIR__ . '/database.php';

/** Store every public enquiry in the admin Leads inbox. */
function cbd_save_lead(string $name, string $phone, string $business, string $message, string $source): int
{
    $pdo = cbd_database();
    if (!$pdo) {
        throw new RuntimeException('Database unavailable');
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(120) NOT NULL,
        phone      VARCHAR(20)  NOT NULL,
        business   VARCHAR(160) DEFAULT NULL,
        message    TEXT         DEFAULT NULL,
        source     VARCHAR(80)  DEFAULT NULL,
        ip         VARCHAR(45)  DEFAULT NULL,
        created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');
    $pdo->prepare("INSERT INTO leads (name, phone, business, message, source, ip) VALUES (?,?,?,?,?,?)")
        ->execute([$name, $phone, $business, $message, $source, $ip]);

    return (int)$pdo->lastInsertId();
}

function cbd_lead_recipients(bool $contactForm = false): array
{
    $recipients = ['chulbuldesign@gmail.com', 'sales@chulbuldesign.com'];
    if ($contactForm) $recipients[] = 'info@chulbuldesign.com';
    return $recipients;
}
