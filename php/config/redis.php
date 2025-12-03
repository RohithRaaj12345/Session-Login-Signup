<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class Redis_Connection {
    private $redis;

    public function __construct() {
        try {
            $this->redis = new Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            $this->redis->ping();
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