<?php
require '/var/www/vendor/autoload.php';

$mongodb_user = getenv("MONGODB_USER");
$mongodb_pass = getenv("MONGODB_PASS");
$mongodb_host = getenv("MONGODB_HOST");
$mongodb_port = getenv("MONGODB_PORT");

$memcached_host = getenv("MEMCACHED_HOST");
$memcached_port = getenv("MEMCACHED_PORT");

$name = $_POST['name'];
$age = $_POST['age'];

// Test the post data
echo "<p>Name: $name and Age: $age</p>";

// Initialize Memcached
$memcached = new Memcached();
$memcached->addServer($memcached_host,$memcached_port); // Replace 'memcached' with your Memcached server address if different

// MongoDB connection
$connection = new MongoDB\Client("mongodb://{$mongodb_user}:{$mongodb_pass}@{$mongodb_host}:{$mongodb_port}");

$db = $connection->gettingstarted;
echo "db 'gettingstarted' selected<br><br>";
$col = $db->users;
echo "Collection $col selected<br><br>";

$doc = ["name" => $name, "age" => $age];

// Insert into MongoDB
$col->insertOne($doc);
echo "<p>User inserted successfully: ";

// Retrieve the inserted user
$record = $col->findOne(['name' => $name]);

if ($record) {
    // Store in Memcached
    $cacheKey = "user_" . $name;
    $memcached->set($cacheKey, $record, 3600); // Cache for 1 hour

    echo $record['name'] . ': ' . $record['age'] . "</p>";

    // Demonstrate Memcached retrieval
    $cachedUser = $memcached->get($cacheKey);
    if ($cachedUser) {
        echo "<p>User retrieved from cache: " . $cachedUser['name'] . ': ' . $cachedUser['age'] . "</p>";
    }
} else {
    echo "User not found.</p>";
}

// Example of using Memcached for a separate query
$cacheKey = "all_users";
$allUsers = $memcached->get($cacheKey);

if (!$allUsers) {
    // If not in cache, fetch from MongoDB
    $allUsers = $col->find()->toArray();
    $memcached->set($cacheKey, $allUsers, 3600); // Cache for 1 hour
    echo "<p>All users fetched from MongoDB and cached.</p>";
} else {
    echo "<p>All users fetched from Memcached cache.</p>";
}

echo "<p>All Users:</p>";
foreach ($allUsers as $user) {
    echo "<p>" . $user['name'] . ': ' . $user['age'] . "</p>";
}
?>
