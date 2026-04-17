<?php
require_once '../auth.php';
requireAdmin();

// Get restaurant ID x2
$id = (int)$_GET['id'];
if (!$id) {
    header('Location: index.php');
    exit;
}


// restaurant data with category again?
try {
    $stmt = $pdo->prepare("
        SELECT r.*, c.name as category_name
        FROM restaurants r
        LEFT JOIN categories c ON r.category_id = c.id
        WHERE r.id = ?
    ");
    $stmt->execute([$id]);
    $restaurant = $stmt->fetch();

    if (!$restaurant) {
        header('Location: index.php');
        exit;
    }

    // Get comments for this restaurant
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE restaurant_id = ? ORDER BY created_at DESC");
    $stmt->execute([$id]);
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
    <title>View Restaurant - Restaurant CMS</title>
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
        .restaurant-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-white text-center mb-4">Admin Panel</h4>
        <a href="../index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="index.php" class="active"><i class="fas fa-utensils"></i> Restaurants</a>
        <a href="../categories/index.php"><i class="fas fa-tags"></i> Categories</a>
        <a href="../comments/index.php"><i class="fas fa-comments"></i> Comments</a>
        <a href="../users/index.php"><i class="fas fa-users"></i> Users</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Restaurant Details</h1>
            <div>
                <a href="edit.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Edit</a>
                <a href="delete.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-danger delete-btn"><i class="fas fa-trash"></i> Delete</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2><?php echo htmlspecialchars($restaurant['name']); ?></h2>

                        <div class="mb-3">
                            <span class="badge bg-info"><?php echo htmlspecialchars($restaurant['category_name'] ?? 'Uncategorized'); ?></span>
                        </div>

                        <div class="mb-4">
                            <h5>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($restaurant['description'])); ?></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Contact Information</h5>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($restaurant['address']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($restaurant['phone']); ?></p>
                                <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($restaurant['website']); ?>" target="_blank"><?php echo htmlspecialchars($restaurant['website']); ?></a></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Timestamps</h5>
                                <p><strong>Created:</strong> <?php echo formatDate($restaurant['created_at']); ?></p>
                                <p><strong>Updated:</strong> <?php echo formatDate($restaurant['updated_at']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Restaurant Image</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($restaurant['image_path']): ?>
                            <img src="../uploads/images/<?php echo htmlspecialchars($restaurant['image_path']); ?>" class="restaurant-image" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-image fa-3x mb-3"></i>
                                <p>No image uploaded</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Comments (<?php echo count($comments); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($comments)): ?>
                    <p class="text-muted">No comments yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($comment['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($comment['comment_text']); ?></td>
                                        <td><?php echo formatDate($comment['created_at']); ?></td>
                                        <td>
                                            <a href="../comments/delete.php?id=<?php echo $comment['id']; ?>&redirect=<?php echo urlencode('restaurants/view.php?id=' . $restaurant['id']); ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '.delete-btn', function(e) {
            if (!confirm('Are you sure you want to delete this restaurant?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>