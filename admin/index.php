<?php

require_once 'auth.php';
requireAdmin();

//counts for dashboard

try {
    // Restaurant
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM restaurants");
    $restaurantCount = $stmt->fetch()['count'];

    // Category
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $categoryCount = $stmt->fetch()['count'];

    // Comment
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM comments");
    $commentCount = $stmt->fetch()['count'];

    // User
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];

    // Recent comments
    $stmt = $pdo->query("SELECT c.*, r.name as restaurant_name FROM comments c JOIN restaurants r ON c.restaurant_id = r.id ORDER BY c.created_at DESC LIMIT 5");
    $recentComments = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Restaurant CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            width: 250px;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .active {
            background-color: #007bff;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-white text-center mb-4">Admin Panel</h4>
        <a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="restaurants/index.php"><i class="fas fa-utensils"></i> Restaurants</a>
        <a href="categories/index.php"><i class="fas fa-tags"></i> Categories</a>
        <a href="comments/index.php"><i class="fas fa-comments"></i> Comments</a>
        <a href="users/index.php"><i class="fas fa-users"></i> Users</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Restaurant CMS Dashboard</h1>
            <div class="text-muted">Welcome, <?php echo $_SESSION['username']; ?></div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Restaurants</h5>
                        <h2 class="card-text"><?php echo $restaurantCount; ?></h2>
                        <a href="restaurants/index.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <h2 class="card-text"><?php echo $categoryCount; ?></h2>
                        <a href="categories/index.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Comments</h5>
                        <h2 class="card-text"><?php echo $commentCount; ?></h2>
                        <a href="comments/index.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <h2 class="card-text"><?php echo $userCount; ?></h2>
                        <a href="users/index.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Recent Comments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Restaurant</th>
                                <th>User</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentComments)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No comments found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentComments as $comment): ?>
                                    <tr>
                                        <td><?php echo $comment['restaurant_name']; ?></td>
                                        <td><?php echo $comment['user_name']; ?></td>
                                        <td><?php echo substr($comment['comment_text'], 0, 50) . '...'; ?></td>
                                        <td><?php echo formatDate($comment['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>