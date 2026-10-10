<?php
// ============================================================
// Session & Authentication Management
// Bure Town Tax Collection Management System
// ============================================================

require_once __DIR__ . '/database.php';

class Auth {
    private Database $db;
    private static ?Auth $instance = null;

    private function __construct() {
        $this->db = Database::getInstance();
        $this->startSession();
    }

    public static function getInstance(): Auth {
        if (self::$instance === null) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    private function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    /**
     * Attempt login with username/password
     * Uses 'role' text column directly (no JOIN needed)
     */
    public function login(string $username, string $password): array {
        $user = $this->db->fetchOne(
            "SELECT * FROM users
             WHERE (username = ? OR email = ?) AND status = 'active'",
            [$username, $username]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->logAudit(null, 'LOGIN_FAILED', 'auth', "Failed login attempt for: {$username}");
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        // Read role name directly from the 'role' column
        $roleName = $user['role'] ?? 'taxpayer';

        // Update last login
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        // Set session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['username']   = $user['username'];
        $_SESSION['full_name']  = $user['full_name'];
        $_SESSION['role']       = $roleName;
        $_SESSION['logged_in']  = true;
        $_SESSION['login_time'] = time();

        session_regenerate_id(true);

        $this->logAudit($user['id'], 'LOGIN_SUCCESS', 'auth', "User logged in: {$user['username']}");

        return ['success' => true, 'role' => $roleName, 'user' => $user];
    }

    /**
     * Logout the current user
     */
    public function logout(): void {
        if (isset($_SESSION['user_id'])) {
            $this->logAudit($_SESSION['user_id'], 'LOGOUT', 'auth', "User logged out: " . ($_SESSION['username'] ?? ''));
        }
        $_SESSION = [];
        session_destroy();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user role
     */
    public function getRole(): string {
        return $_SESSION['role'] ?? '';
    }

    /**
     * Get current user ID
     */
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user data (reads role directly from users table)
     */
    public function getUser(): ?array {
        if (!$this->isLoggedIn()) return null;
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
    }

    /**
     * Require a specific role(s) or redirect to login
     */
    public function requireRole(string|array $roles, string $redirect = '../login.php'): void {
        if (!$this->isLoggedIn()) {
            header("Location: {$redirect}");
            exit;
        }
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array($this->getRole(), $roles)) {
            header("Location: {$redirect}?error=unauthorized");
            exit;
        }
    }

    /**
     * Require login or redirect
     */
    public function requireLogin(string $redirect = '../login.php'): void {
        if (!$this->isLoggedIn()) {
            header("Location: {$redirect}");
            exit;
        }
    }

    /**
     * Log audit trail
     */
    public function logAudit(?int $userId, string $action, string $module, string $description = '', $old = null, $new = null): void {
        try {
            $this->db->insert('audit_logs', [
                'user_id'     => $userId,
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'old_value'   => $old ? json_encode($old) : null,
                'new_value'   => $new ? json_encode($new) : null,
            ]);
        } catch (Exception $e) {
            // Silently fail for audit log errors
        }
    }

    /**
     * Get dashboard URL by role name
     */
    public static function getDashboardUrl(string $role): string {
        return match($role) {
            'admin'    => 'admin/dashboard.php',
            'staff'    => 'staff/dashboard.php',
            'officer'  => 'officer/dashboard.php',
            'taxpayer' => 'taxpayer/dashboard.php',
            'bank'     => 'bank/dashboard.php',
            default    => 'login.php',
        };
    }
}

// Initialize Auth globally
$auth = Auth::getInstance();
