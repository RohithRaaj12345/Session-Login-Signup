<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class MongoDB_Connection {
    private $client;
    private $database;
    private $collection;

    public function __construct() { 
        try {
            $this->client = new MongoDB\Client("mongodb://localhost:27017");
            $this->database = $this->client->user_profiles;
            $this->collection = $this->database->profiles;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'MongoDB connection failed: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    public function getCollection() {
        return $this->collection;
    }
}
?>