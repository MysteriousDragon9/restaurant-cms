<?php
require_once '../auth.php';
requireAdmin();

// Get all comments with restaurant names
try {
    $stmt = $pdo->query("
        SELECT c.*, r.name as restaurant_name
        FROM comments c
        JOIN restaurants r ON c.restaurant_id = r.id
        ORDER BY c.created_at DESC
    ");
    $comments = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments - Restaurant CMS</title>
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
        <a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="../restaurants/index.php"><i class="fas fa-utensils"></i> Restaurants</a>
        <a href="../categories/index.php"><i class="fas fa-tags"></i> Categories</a>
        <a href="index.php" class="active"><i class="fas fa-comments"></i> Comments</a>
        <a href="../users/index.php"><i class="fas fa-users"></i> Users</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Comment Management</h1>
        </div>

        <?php if (empty($comments)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No comments found yet.
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Restaurant</th>
                                    <th>User</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td>
                                            <a href="../restaurants/view.php?id=<?php echo $comment['restaurant_id']; ?>">
                                                <?php echo htmlspecialchars($comment['restaurant_name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($comment['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($comment['comment_text']); ?></td>
                                        <td><?php echo $comment['created_at']; ?></td>
                                        <td>
                                            <a href="delete.php?id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this comment?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>