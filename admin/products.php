<?php
require_once '../includes/db_connect.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

$database = new Database($conn);
$user = new User($database);
$prod = new Product($database);
$cat = new Category($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = '';
$editing_prod = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $category_id = $_POST['category_id'] ?: null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $margin_amount = $_POST['margin_amount'] ?? 0;
        $pv_value = $_POST['pv_value'] ?? 0;
        $brand = $_POST['brand'] ?? '';
        $manufacturer = $_POST['manufacturer'] ?? '';
        $supplier = $_POST['supplier'] ?? '';

        $attributes = [];
        if (isset($_POST['attr_key']) && isset($_POST['attr_val'])) {
            foreach ($_POST['attr_key'] as $index => $key) {
                $attributes[$key] = $_POST['attr_val'][$index];
            }
        }

        if ($action === 'create') {
            $product_id = $prod->create($category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes);
            $message = 'Product created successfully!';
        } else {
            $product_id = $_POST['id'];
            $prod->update($product_id, $category_id, $name, $description, $price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes);
            $message = 'Product updated successfully!';
        }

        // Handle Image Uploads
        if (!empty($_FILES['product_images']['name'][0])) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            foreach ($_FILES['product_images']['name'] as $i => $filename) {
                if ($_FILES['product_images']['error'][$i] === 0) {
                    $tmp_name = $_FILES['product_images']['tmp_name'][$i];
                    $size = $_FILES['product_images']['size'][$i];

                    // Requirement: Restrict file sizes (e.g., 2MB)
                    if ($size > 2 * 1024 * 1024) {
                        continue; // Skip files larger than 2MB
                    }

                    $mime_type = mime_content_type($tmp_name);
                    $allowed_types = [
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/gif'  => 'gif',
                        'image/webp' => 'webp'
                    ];

                    if (array_key_exists($mime_type, $allowed_types)) {
                        $ext = $allowed_types[$mime_type];
                        $new_filename = uniqid() . '.' . $ext;
                        if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                            $prod->addImage($product_id, 'assets/uploads/' . $new_filename, $i === 0);
                        }
                    }
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $prod->delete($id);
        $message = 'Product deleted successfully!';
    }
}

if (isset($_GET['edit'])) {
    $editing_prod = $prod->getById($_GET['edit']);
}

$products = $prod->getAll();
$categories = $cat->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <div class="navbar-nav">
                <a class="nav-link" href="categories.php">Categories</a>
                <a class="nav-link active" href="products.php">Products</a>
                <a class="nav-link" href="pv_settings.php">PV Settings</a>
                <a class="nav-link" href="withdrawals.php">Withdrawals</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Manage Products</h2>
        <?php if ($message): ?>
            <div class="alert alert-success mt-3"><?php echo h($message); ?></div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header"><?php echo $editing_prod ? 'Edit Product' : 'Add New Product'; ?></div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="action" value="<?php echo $editing_prod ? 'update' : 'create'; ?>">
                    <?php if ($editing_prod): ?>
                        <input type="hidden" name="id" value="<?php echo h($editing_prod['id']); ?>">
                    <?php endif; ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" id="category_id" onchange="loadAttributes()">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo h($c['id']); ?>" data-attrs='<?php echo h($c['custom_attributes']); ?>' <?php echo ($editing_prod && $editing_prod['category_id'] == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo h($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price (Public)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['price']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Margin Amount (Internal)</label>
                            <input type="number" step="0.01" name="margin_amount" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['margin_amount']) : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PV Value</label>
                            <input type="number" step="0.01" name="pv_value" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['pv_value']) : ''; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Images (Max 2MB per file)</label>
                            <input type="file" name="product_images[]" class="form-control" multiple>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['brand']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Manufacturer</label>
                            <input type="text" name="manufacturer" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['manufacturer']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier</label>
                            <input type="text" name="supplier" class="form-control" value="<?php echo $editing_prod ? h($editing_prod['supplier']) : ''; ?>">
                        </div>
                    </div>
                    <div id="dynamic-attributes" class="mb-3">
                        <?php if ($editing_prod && !empty($editing_prod['attributes'])):
                            $attrs = json_decode($editing_prod['attributes'], true);
                            echo '<h5>Custom Attributes</h5><div class="row">';
                            foreach ($attrs as $key => $val) {
                                echo '<div class="col-md-3 mb-2">
                                    <label class="form-label">'.h($key).'</label>
                                    <input type="hidden" name="attr_key[]" value="'.h($key).'">
                                    <input type="text" name="attr_val[]" class="form-control" value="'.h($val).'">
                                </div>';
                            }
                            echo '</div>';
                        endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $editing_prod ? h($editing_prod['description']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $editing_prod ? 'Update Product' : 'Add Product'; ?></button>
                    <?php if ($editing_prod): ?>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>PV</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo h($p['id']); ?></td>
                    <td><?php echo h($p['name']); ?></td>
                    <td><?php echo h($p['category_name']); ?></td>
                    <td><?php echo h($p['price']); ?></td>
                    <td><?php echo h($p['pv_value']); ?></td>
                    <td>
                        <a href="?edit=<?php echo h($p['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                        <form method="POST" style="display:inline-block">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo h($p['id']); ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    function loadAttributes() {
        const select = document.getElementById('category_id');
        const selectedOption = select.options[select.selectedIndex];
        const attrContainer = document.getElementById('dynamic-attributes');
        if (selectedOption.dataset.attrs) {
            const attrs = JSON.parse(selectedOption.dataset.attrs);
            if (attrs && attrs.length > 0) {
                let html = '<h5>Custom Attributes</h5><div class="row">';
                attrs.forEach(attr => {
                    html += `<div class="col-md-3 mb-2">
                        <label class="form-label">${attr}</label>
                        <input type="hidden" name="attr_key[]" value="${attr}">
                        <input type="text" name="attr_val[]" class="form-control" placeholder="Value for ${attr}">
                    </div>`;
                });
                html += '</div>';
                attrContainer.innerHTML = html;
            } else {
                attrContainer.innerHTML = '';
            }
        } else {
            attrContainer.innerHTML = '';
        }
    }
    </script>
</body>
</html>
