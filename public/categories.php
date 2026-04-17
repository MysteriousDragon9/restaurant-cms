<?php
require_once '../admin/db.php';
require_once 'helpers.php';

// Get all categories with restaurant counts
try {
    $stmt = $pdo->query("
        SELECT c.*, COUNT(r.id) as restaurant_count
        FROM categories c
        LEFT JOIN restaurants r ON c.id = r.category_id
        GROUP BY c.id
        ORDER BY c.name
    ");
    $categories = $stmt->fetchAll();

    // Get category ID from URL if specified
    $category_id = (int)($_GET['id'] ?? 0);
    $category_name = '';
    $restaurants = [];

    if ($category_id > 0) {
        // Get category name
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $category = $stmt->fetch();

        if ($category) {
            $category_name = $category['name'];

            // Get restaurants in this category
            $stmt = $pdo->prepare("
                SELECT r.*
                FROM restaurants r
                WHERE r.category_id = ?
                ORDER BY r.name
            ");
            $stmt->execute([$category_id]);
            $restaurants = $stmt->fetchAll();
        }
    }

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Winnipeg Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://picsum.photos/seed/categories/1920/400.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .category-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .category-card:hover {
            transform: translateY(-5px);
        }
        .restaurant-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .restaurant-card:hover {
            transform: translateY(-5px);
        }
        .restaurant-image {
            height: 200px;
            object-fit: cover;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-utensils me-2"></i>Winnipeg Restaurants
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <h1 class="display-4">Restaurant Categories</h1>
            <p class="lead">Browse restaurants by cuisine type</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if ($category_id > 0 && !empty($category_name)): ?>
                <!-- Show restaurants in selected category -->
                <div class="mb-4">
                    <a href="categories.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Categories
                    </a>
                </div>

                <h2 class="mb-4">Restaurants in <?php echo htmlspecialchars($category_name); ?></h2>

                <div class="row">
                    <?php if (empty($restaurants)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No restaurants found in this category.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card restaurant-card">
                                    <?php if ($restaurant['image_path']): ?>
                                        <img src="../admin/uploads/images/<?php echo htmlspecialchars($restaurant['image_path']); ?>" class="card-img-top restaurant-image" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                                    <?php else: ?>
                                        <img src="https://picsum.photos/seed/<?php echo urlencode($restaurant['name']); ?>/400/200.jpg" class="card-img-top restaurant-image" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                                        <p class="card-text"><?php echo substr(htmlspecialchars($restaurant['description']), 0, 100) . '...'; ?></p>
                                        <div class="d-flex justify-content-between">
                                            <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-primary">View Details</a>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($restaurant['phone']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Show all categories -->
                <h2 class="mb-4">Browse by Category</h2>

                <div class="row">
                    <?php if (empty($categories)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">No categories found.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card category-card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-tag fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                                        <p class="card-text"><?php echo $category['restaurant_count']; ?> restaurant<?php echo $category['restaurant_count'] != 1 ? 's' : ''; ?></p>
                                        <a href="categories.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">View Restaurants</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Winnipeg Restaurants</h5>
                    <p>Your guide to the best dining experiences in Winnipeg, Manitoba.</p>
                </div>
                <div class="col-md-6 text-end">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="categories.php" class="text-white">Categories</a></li>
                        <li><a href="search.php" class="text-white">Search</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p>&copy; 2026 Winnipeg Restaurants. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>