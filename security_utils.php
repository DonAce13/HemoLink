<?php
class SecurityUtils {
    private $database;
    private $logFile;

    public function __construct($database) {
        $this->database = $database;
        $this->logFile = __DIR__ . '/security_logs.txt';
    }

    // Sanitize input to prevent XSS
    public function sanitizeInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }

    // Validate email format
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }


    // Log security events
    public function logSecurityEvent($eventType, $details) {
        $logEntry = date('Y-m-d H:i:s') . " | $eventType | " . json_encode($details) . "\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    // Prepared statement wrapper for SELECT
    public function preparedSelect($query, $params = [], $paramTypes = '') {
        $stmt = $this->database->prepare($query);
        
        if ($params) {
            $stmt->bind_param($paramTypes, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Log the query for monitoring
        $this->logSecurityEvent('SELECT_QUERY', [
            'query' => $query,
            'params' => $params
        ]);
        
        return $result;
    }

    // Prepared statement wrapper for INSERT/UPDATE/DELETE
    public function preparedQuery($query, $params = [], $paramTypes = '') {
        $stmt = $this->database->prepare($query);
        
        if ($params) {
            $stmt->bind_param($paramTypes, ...$params);
        }
        
        $result = $stmt->execute();
        
        // Log the query for monitoring
        $this->logSecurityEvent('MODIFY_QUERY', [
            'query' => $query,
            'params' => $params,
            'success' => $result
        ]);
        
        return $result;
    }

    // Password strength checker
    public function checkPasswordStrength($password) {
        // At least 8 characters, one uppercase, one lowercase, one number
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
    }

    // Generate a secure random token
    public function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
}

// Global security utility initialization
$securityUtils = new SecurityUtils($database);
?>
