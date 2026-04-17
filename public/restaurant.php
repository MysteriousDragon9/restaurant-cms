<?php
require_once '../admin/db.php';
require_once 'helpers.php';

//restaurant ID
$id = (int)$_GET['id'];
if (!$id) {
    header('Location: index.php');
    exit;
}

//restaurant data with category
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

    //comments for this restaurant
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE restaurant_id = ? ORDER BY created_at DESC");
    $stmt->execute([$id]);
    $comments = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Process comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = sanitize($_POST['user_name']);
    $comment_text = sanitize($_POST['comment_text']);

    if (empty($user_name) || empty($comment_text)) {
        $error = "Name and comment are required";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (restaurant_id, user_name, comment_text, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$id, $user_name, $comment_text]);

            header("Location: restaurant.php?id=$id&success=comment");
            exit;
        } catch(PDOException $e) {
            $error = "Error submitting comment: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Winnipeg Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://picsum.photos/seed/<?php echo urlencode($restaurant['name']); ?>/1920/600.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .restaurant-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
        .comment-card {
            border-left: 4px solid #007bff;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            <h1 class="display-4"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($restaurant['category_name'] ?? 'Uncategorized'); ?></p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>About <?php echo htmlspecialchars($restaurant['name']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($restaurant['description'])); ?></p>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Contact Information</h5>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($restaurant['address']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($restaurant['phone']); ?></p>
                                    <?php if ($restaurant['website']): ?>
                                        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($restaurant['website']); ?>" target="_blank"><?php echo htmlspecialchars($restaurant['website']); ?></a></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h5>Details</h5>
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($restaurant['category_name'] ?? 'Uncategorized'); ?></p>
                                    <p><strong>Added:</strong> <?php echo date('F j, Y', strtotime($restaurant['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4>Comments</h4>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <?php if (isset($_GET['success']) && $_GET['success'] == 'comment'): ?>
                                <div class="alert alert-success">Your comment has been submitted!</div>
                            <?php endif; ?>

                            <form method="post" class="mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="user_name" class="form-label">Your Name</label>
                                            <input type="text" class="form-control" id="user_name" name="user_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                <label for="comment_text" class="form-label">Your Comment</label>
                                    <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
                                </div>

                                <!-- reCAPTCHA Widget -->
                                <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="6LdijLssAAAAAPXV5TJ-UAdnwCR4pSTAr9nC9aLF"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit Comment</button>
                                    </form>

                            <?php if (empty($comments)): ?>
                                <p class="text-muted">No comments yet. Be the first to comment!</p>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="card mb-3 comment-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($comment['user_name']); ?></h6>
                                                <small class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></small>
                                            </div>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Restaurant Image</h5>
                            <?php if ($restaurant['image_path']): ?>
                                <img src="../admin/uploads/images/<?php echo htmlspecialchars($restaurant['image_path']); ?>" class="restaurant-image" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                            <?php else: ?>
                                <img src="https://picsum.photos/seed/<?php echo urlencode($restaurant['name']); ?>/400/300.jpg" class="restaurant-image" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                            <?php endif; ?>

                            <div class="mt-3">
                                <a href="index.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Restaurants
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
</body>
</html>