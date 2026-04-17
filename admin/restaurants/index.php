<?php
require_once '../auth.php';
requireAdmin();

// Sorting
$sortBy = $_GET['sort'] ?? 'name';
$allowedSorts = ['name', 'created_at', 'updated_at'];
if (!in_array($sortBy, $allowedSorts)) {
    $sortBy = 'name';
}

// Fetch all restaurants with category names
try {
    $stmt = $pdo->prepare("
        SELECT r.*, c.name as category_name
        FROM restaurants r
        LEFT JOIN categories c ON r.category_id = c.id
        ORDER BY r.$sortBy
    ");
    $stmt->execute();
    $restaurants = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - Restaurant CMS</title>
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
        <a href="index.php" class="active"><i class="fas fa-utensils"></i> Restaurants</a>
        <a href="../categories/index.php"><i class="fas fa-tags"></i> Categories</a>
        <a href="../comments/index.php"><i class="fas fa-comments"></i> Comments</a>
        <a href="../users/index.php"><i class="fas fa-users"></i> Users</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Restaurant Management</h1>
            <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Restaurant</a>
        </div>

        <!-- Sorting dropdown -->
        <form method="get" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort by:</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
                        <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Date Created</option>
                        <option value="updated_at" <?= $sortBy === 'updated_at' ? 'selected' : '' ?>>Last Updated</option>
                    </select>
                </div>
            </div>
        </form>

        <?php if (empty($restaurants)): ?>
            <div class="alert alert-info">No restaurants found. <a href="create.php">Add your first restaurant</a>.</div>
        <?php else: ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($restaurants as $restaurant): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
                                        <td><?php echo htmlspecialchars($restaurant['category_name'] ?? 'Uncategorized'); ?></td>
                                        <td><?php echo htmlspecialchars($restaurant['address']); ?></td>
                                        <td><?php echo htmlspecialchars($restaurant['phone']); ?></td>
                                        <td><?php echo formatDate($restaurant['created_at']); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="view.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                <a href="edit.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                <a href="delete.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-sm btn-danger delete-btn"><i class="fas fa-trash"></i></a>
                                            </div>
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