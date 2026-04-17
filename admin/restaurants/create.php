<?php
require_once '../auth.php';
requireAdmin();

//categories for dropdown
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

//form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $category_id = (int)$_POST['category_id'];
    $image_path = '';

   // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed extensions
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowed)) {
            if ($fileSize < 5000000) { // 5MB max
                // Create unique filename
                $fileNameNew = uniqid('restaurant_', true) . "." . $fileExt;
                $fileDestination = '../uploads/images/' . $fileNameNew;

                // Move file to uploads folder
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $image_path = $fileNameNew;
                } else {
                    $error = "Error uploading file. Please try again.";
                }
            } else {
                $error = "File size too large. Maximum size is 5MB.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Basic validation
    if (empty($name) || empty($description)) {
        $error = "Name and description are required";
    } else if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO restaurants (name, description, address, phone, category_id, image_path, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$name, $description, $address, $phone, $category_id, $image_path]);

            header('Location: index.php?success=created');
            exit;
        } catch(PDOException $e) {
            $error = "Error creating restaurant: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Restaurant - Restaurant CMS</title>
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
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>

    <script src="https://cdn.tiny.cloud/1/vimtjseaajqzkuzva67qophezkwns9v9d6ezja93srrhgjn1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#description',
        height: 300,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
    });
    </script>
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
            <h1>Add New Restaurant</h1>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Restaurants</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Restaurant Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="0">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Restaurant Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Accepted formats: JPG, JPEG, PNG, GIF. Maximum size: 5MB</div>
                        <img id="image-preview" class="image-preview" alt="Image preview">
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="index.php" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Restaurant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>