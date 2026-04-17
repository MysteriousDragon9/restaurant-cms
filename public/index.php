<?php
require_once '../admin/db.php';
require_once 'helpers.php';

// Get all restaurants with category names
try {
    $stmt = $pdo->query("
        SELECT r.*, c.name as category_name
        FROM restaurants r
        LEFT JOIN categories c ON r.category_id = c.id
        ORDER BY r.name
    ");
    $restaurants = $stmt->fetchAll();

    // Get categories for filter
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winnipeg Restaurants - Restaurant Guide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://picsum.photos/seed/winnipeg/1920/600.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
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

        #search-results {
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
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
            <h1 class="display-4">Discover Winnipeg's Best Restaurants</h1>
            <p class="lead">Find your next dining experience in the heart of Manitoba</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="ajax-search-input" placeholder="Search restaurants by name, cuisine, or description...">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <div id="ajax-search-results" class="mt-3"></div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h2>Featured Restaurants</h2>
                </div>
            </div>

            <div class="row" id="restaurant-list">
                <?php if (empty($restaurants)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No restaurants found.</div>
                </div>
                <?php else: ?>
                <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-md-4 mb-4 restaurant-item"
                     data-name="<?php echo strtolower($restaurant['name']); ?>"
                     data-category="<?php echo strtolower($restaurant['category_name'] ?? 'uncategorized'); ?>">
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
    <script>
        // AJAX Search functionality
        const searchInput = document.getElementById('ajax-search-input');
        const searchResults = document.getElementById('ajax-search-results');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            // Don't search for very short queries
            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            // Debounce: wait 300ms after user stops typing
            searchTimeout = setTimeout(function() {
                // Show loading
                searchResults.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

                // Fetch search results via AJAX
                fetch('ajax-search.php?q=' + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(data => {
                        searchResults.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div class="alert alert-danger">Search error. Please try again.</div>';
                    });
            }, 300);
        });

        // Clear results when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.innerHTML = '';
            }
        });

        // Enter key to search
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                // Trigger the search
                const query = this.value.trim();
                if (query.length >= 2) {
                    searchInput.dispatchEvent(new Event('input'));
                }
            }
        });
    </script>
</body>
</html>