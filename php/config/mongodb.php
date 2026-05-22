<?php
class MongoDB_Connection {
    private $client;
    private $database;
    private $collection;

    public function __construct() {
        try {
            $this->client = new MongoDB\Driver\Manager("mongodb://localhost:27017");
            $this->database = 'user_profiles';
            $this->collection = 'profiles';
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'MongoDB connection failed: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    public function getManager() {
        return $this->client;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getCollection() {
        return $this->collection;
    }
}
?>
