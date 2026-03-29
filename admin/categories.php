<?php
require_once '../includes/db_connect.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Category.php';

$database = new Database($conn);
$user = new User($database);
$cat = new Category($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = '';
$editing_cat = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';
    $name = $_POST['name'] ?? '';
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $custom_attributes = $_POST['custom_attributes'] ?? '';
    $attributes_array = array_map('trim', explode(',', $custom_attributes));
    $attributes_array = array_filter($attributes_array);

    if ($action === 'create') {
        $cat->create($name, $parent_id, $attributes_array);
        $message = 'Category created successfully!';
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $cat->update($id, $name, $parent_id, $attributes_array);
        $message = 'Category updated successfully!';
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $cat->delete($id);
        $message = 'Category deleted successfully!';
    }
}

if (isset($_GET['edit'])) {
    $editing_cat = $cat->getById($_GET['edit']);
}

$categories = $cat->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="categories.php">Categories</a>
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link" href="pv_settings.php">PV Settings</a>
                <a class="nav-link" href="withdrawals.php">Withdrawals</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Manage Categories</h2>
        <?php if ($message): ?>
            <div class="alert alert-success mt-3"><?php echo h($message); ?></div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header"><?php echo $editing_cat ? 'Edit Category' : 'Add New Category'; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="action" value="<?php echo $editing_cat ? 'update' : 'create'; ?>">
                    <?php if ($editing_cat): ?>
                        <input type="hidden" name="id" value="<?php echo h($editing_cat['id']); ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $editing_cat ? h($editing_cat['name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Parent Category</label>
                            <select name="parent_id" class="form-select">
                                <option value="">None</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo h($c['id']); ?>" <?php echo ($editing_cat && $editing_cat['parent_id'] == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo h($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Custom Attributes (comma separated)</label>
                            <input type="text" name="custom_attributes" class="form-control" placeholder="e.g. Color, Size" value="<?php
                                if ($editing_cat) {
                                    $attrs = json_decode($editing_cat['custom_attributes'], true);
                                    echo !empty($attrs) ? h(implode(', ', $attrs)) : '';
                                }
                            ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><?php echo $editing_cat ? 'Update' : 'Add'; ?></button>
                            <?php if ($editing_cat): ?>
                                <a href="categories.php" class="btn btn-secondary ms-2">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent ID</th>
                    <th>Attributes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?php echo h($c['id']); ?></td>
                    <td><?php echo h($c['name']); ?></td>
                    <td><?php echo h($c['parent_id'] ?? '-'); ?></td>
                    <td><?php
                        $attrs = json_decode($c['custom_attributes'], true);
                        echo !empty($attrs) ? h(implode(', ', $attrs)) : '-';
                    ?></td>
                    <td>
                        <a href="?edit=<?php echo h($c['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                        <form method="POST" style="display:inline-block">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo h($c['id']); ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
