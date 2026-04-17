<?php
require_once '../admin/db.php';
require_once 'helpers.php';

// Pagination settings
$perPage = 6;
$page = (int)($_GET['page'] ?? 1);
$offset = (int)(($page - 1) * $perPage);

// Get search parameters
$search = sanitize($_GET['search'] ?? '');
$category_id = (int)($_GET['category_id'] ?? 0);

// Build query conditions
$searchParams = [];
$whereClauses = [];

if (!empty($search)) {
    $whereClauses[] = "(r.name LIKE ? OR r.description LIKE ?)";
    $searchParams[] = "%$search%";
    $searchParams[] = "%$search%";
}

if ($category_id > 0) {
    $whereClauses[] = "r.category_id = ?";
    $searchParams[] = $category_id;
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    // 1. Get Total Count
    $countSql = "SELECT COUNT(*) as total FROM restaurants r $whereClause";
    $stmt = $pdo->prepare($countSql);
    if (!empty($searchParams)) {
        $stmt->execute($searchParams);
    } else {
        $stmt->execute();
    }
    $total = $stmt->fetch()['total'];
    $totalPages = ceil($total / $perPage);

    // 2. Get Restaurants with Pagination - FIX: Use explicit binding
    $sql = "
        SELECT r.*, c.name as category_name
        FROM restaurants r
        LEFT JOIN categories c ON r.category_id = c.id
        $whereClause
        ORDER BY r.name
        LIMIT ? OFFSET ?
    ";

    $stmt = $pdo->prepare($sql);

    // Bind search parameters (if any)
    $paramIndex = 1;
    foreach ($searchParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }

    // CRITICAL: Bind LIMIT and OFFSET as INT explicitly
    $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $restaurants = $stmt->fetchAll();

    // 3. Get Categories
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage() . "<br>SQL: " . $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Restaurants - Winnipeg Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://picsum.photos/seed/search/1920/400.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            text-align: center;
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
        .search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
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
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="search.php">Search</a>
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
            <h1 class="display-4">Search Restaurants</h1>
            <p class="lead">Find your perfect dining experience</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="search-form">
                <form method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Restaurant name or description">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h2>Search Results</h2>
                    <p class="text-muted">
                        <?php if (empty($search) && $category_id == 0): ?>
                            Showing all restaurants
                        <?php else: ?>
                            <?php
                            $searchTerms = [];
                            if (!empty($search)) $searchTerms[] = "matching '" . htmlspecialchars($search) . "'";
                            if ($category_id > 0) {
                                $categoryName = '';
                                foreach ($categories as $cat) {
                                    if ($cat['id'] == $category_id) {
                                        $categoryName = $cat['name'];
                                        break;
                                    }
                                }
                                $searchTerms[] = "in category '$categoryName'";
                            }
                            echo "Restaurants " . implode(' and ', $searchTerms);
                            ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <?php if (empty($restaurants)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No restaurants found matching your criteria.
                            <a href="search.php" class="alert-link">Try a different search</a>.
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
                                    <div class="mb-2">
                                        <span class="badge bg-info"><?php echo htmlspecialchars($restaurant['category_name'] ?? 'Uncategorized'); ?></span>
                                    </div>
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
        </div>
    </section>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Search results pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&page=<?php echo $page + 1; ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="text-center mt-2">
            <small class="text-muted">
                Showing <?php echo ($page - 1) * $perPage + 1; ?>-<?php echo min($page * $perPage, $total); ?>
                of <?php echo $total; ?> results
            </small>
        </div>
    <?php endif; ?>
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