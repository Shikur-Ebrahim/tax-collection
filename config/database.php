<?php
// ============================================================
// Database Configuration
// Bure Town Tax Collection Management System
// ============================================================

// Parse connection details from Vercel Environment Variable if available
$dbUrl = getenv('DATABASE_URL');

if ($dbUrl) {
    $parsedUrl = parse_url($dbUrl);
    define('DB_HOST', $parsedUrl['host']);
    define('DB_PORT', $parsedUrl['port'] ?? '6543'); // Default pooler port
    define('DB_NAME', ltrim($parsedUrl['path'], '/'));
    define('DB_USER', $parsedUrl['user']);
    define('DB_PASS', $parsedUrl['pass']);
} else {
    // Fallback for local testing
    define('DB_HOST', 'db.bpgcpdzdwdxitmlwnshu.supabase.co');
    define('DB_PORT', '5432');
    define('DB_NAME', 'postgres');
    define('DB_USER', 'postgres');
    define('DB_PASS', 'Shikur@3828@');
}

// Application Settings
define('APP_NAME', 'Bure Town Tax Collection Management System');
define('APP_SHORT', 'BTCMS');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/tax collection management system/');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'bure_tcms_session');

// Security
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchOne(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): int {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$cols}) VALUES ({$placeholders}) RETURNING id";
        $stmt = $this->query($sql, array_values($data));
        return (int)$stmt->fetchColumn();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $setParts = array_map(fn($col) => "{$col} = ?", array_keys($data));
        $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$where}";
        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    public function count(string $table, string $where = '1=1', array $params = []): int {
        $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$where}";
        $row = $this->fetchOne($sql, $params);
        return (int)($row['cnt'] ?? 0);
    }
}
