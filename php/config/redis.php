<?php
class Redis_Connection {
    private $redis;

    public function __construct() {
        try {
            $this->redis = new Redis();
            $this->redis->connect('127.0.0.1', 6379);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Redis connection failed: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    public function getClient() {
        return $this->redis;
    }
}
?>
