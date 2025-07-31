<?php
/**
 * Session Manager untuk menghindari tabrakan session
 * File: includes/session_manager.php
 */

class SessionManager {
    private static $timeout_duration = 900; // 15 menit
    private static $initialized = false;
    
    /**
     * Inisialisasi session dengan konfigurasi yang aman
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }
        
        // Konfigurasi session yang aman
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Start session hanya jika belum dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        self::$initialized = true;
        
        // Regenerate session ID secara berkala untuk keamanan
        self::regenerateSessionId();
        
        // Cek timeout
        self::checkTimeout();
    }
    
    /**
     * Cek timeout session
     */
    private static function checkTimeout() {
        if (isset($_SESSION['LAST_ACTIVITY']) && 
            (time() - $_SESSION['LAST_ACTIVITY']) > self::$timeout_duration) {
            self::destroy();
            self::redirectToLogin('timeout=true');
        }
        
        $_SESSION['LAST_ACTIVITY'] = time();
    }
    
    /**
     * Regenerate session ID untuk keamanan
     */
    private static function regenerateSessionId() {
        if (!isset($_SESSION['LAST_REGENERATE'])) {
            $_SESSION['LAST_REGENERATE'] = time();
        } else if (time() - $_SESSION['LAST_REGENERATE'] > 300) { // 5 menit
            session_regenerate_id(true);
            $_SESSION['LAST_REGENERATE'] = time();
        }
    }
    
    /**
     * Set session data dengan namespace
     */
    public static function set($key, $value, $namespace = 'default') {
        self::init();
        
        if (!isset($_SESSION['app_data'])) {
            $_SESSION['app_data'] = [];
        }
        
        if (!isset($_SESSION['app_data'][$namespace])) {
            $_SESSION['app_data'][$namespace] = [];
        }
        
        $_SESSION['app_data'][$namespace][$key] = $value;
    }
    
    /**
     * Get session data dengan namespace
     */
    public static function get($key, $namespace = 'default', $default = null) {
        self::init();
        
        if (!isset($_SESSION['app_data'][$namespace][$key])) {
            return $default;
        }
        
        return $_SESSION['app_data'][$namespace][$key];
    }
    
    /**
     * Hapus session data dengan namespace
     */
    public static function remove($key, $namespace = 'default') {
        self::init();
        
        if (isset($_SESSION['app_data'][$namespace][$key])) {
            unset($_SESSION['app_data'][$namespace][$key]);
        }
    }
    
    /**
     * Set user session (untuk autentikasi)
     */
    public static function setUser($username, $role, $nama) {
        self::set('username', $username, 'auth');
        self::set('role', $role, 'auth');
        self::set('nama', $nama, 'auth');
        self::set('login_time', time(), 'auth');
        self::set('user_ip', $_SERVER['REMOTE_ADDR'] ?? '', 'auth');
    }
    
    /**
     * Get user data
     */
    public static function getUser() {
        return [
            'username' => self::get('username', 'auth'),
            'role' => self::get('role', 'auth'),
            'nama' => self::get('nama', 'auth'),
            'login_time' => self::get('login_time', 'auth'),
            'user_ip' => self::get('user_ip', 'auth')
        ];
    }
    
    /**
     * Cek apakah user sudah login
     */
    public static function isLoggedIn() {
        $username = self::get('username', 'auth');
        return !empty($username);
    }
    
    /**
     * Cek role user
     */
    public static function hasRole($role) {
        $userRole = self::get('role', 'auth');
        if (is_array($role)) {
            return in_array($userRole, $role);
        }
        return $userRole === $role;
    }
    
    /**
     * Cek apakah user adalah admin
     */
    public static function isAdmin() {
        return self::hasRole(['admin', 'super-admin']);
    }
    
    /**
     * Cek apakah user adalah super admin
     */
    public static function isSuperAdmin() {
        return self::hasRole('super-admin');
    }
    
    /**
     * Destroy session
     */
    public static function destroy() {
        self::init();
        
        // Hapus semua session data
        $_SESSION = [];
        
        // Hapus session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::destroy();
        self::redirectToLogin('logout=true');
    }
    
    /**
     * Redirect ke halaman login
     */
    private static function redirectToLogin($query = '') {
        $loginUrl = self::getBaseUrl() . 'login.php';
        if ($query) {
            $loginUrl .= '?' . $query;
        }
        header("Location: $loginUrl");
        exit();
    }
    
    /**
     * Get base URL
     */
    private static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        $path = ($path === '/') ? '/' : $path . '/';
        
        return $protocol . $host . $path;
    }
    
    /**
     * Require login (untuk proteksi halaman)
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            self::redirectToLogin();
        }
    }
    
    /**
     * Require admin (untuk proteksi halaman admin)
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header("HTTP/1.0 403 Forbidden");
            die('Access denied. Admin role required.');
        }
    }
    
    /**
     * Require super admin
     */
    public static function requireSuperAdmin() {
        self::requireLogin();
        if (!self::isSuperAdmin()) {
            header("HTTP/1.0 403 Forbidden");
            die('Access denied. Super admin role required.');
        }
    }
    
    /**
     * Set flash message
     */
    public static function setFlash($type, $message) {
        self::set('flash_type', $type, 'flash');
        self::set('flash_message', $message, 'flash');
    }
    
    /**
     * Get flash message
     */
    public static function getFlash() {
        $type = self::get('flash_type', 'flash');
        $message = self::get('flash_message', 'flash');
        
        // Hapus flash message setelah dibaca
        self::remove('flash_type', 'flash');
        self::remove('flash_message', 'flash');
        
        return ['type' => $type, 'message' => $message];
    }
    
    /**
     * Get session info untuk debugging
     */
    public static function getSessionInfo() {
        return [
            'session_id' => session_id(),
            'session_name' => session_name(),
            'session_status' => session_status(),
            'last_activity' => self::get('LAST_ACTIVITY', 'system'),
            'last_regenerate' => self::get('LAST_REGENERATE', 'system'),
            'user_data' => self::getUser()
        ];
    }
}