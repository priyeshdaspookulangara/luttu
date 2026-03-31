<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Category.php';

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

$categories_list = $cat->getAll();

$page_title = 'Manage Categories — ShopPV Admin';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>
        <h1 class="m-0">Manage Categories</h1>
    </div>
</div>

<?php if ($message): ?>
    <div style="background:var(--success-color); color:#fff; padding:15px; border-radius:5px; margin-bottom:20px"><?php echo h($message); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white">
        <h4 class="card-header__title"><?php echo $editing_cat ? 'Edit Category' : 'Add New Category'; ?></h4>
    </div>
    <div class="card-body form-card">
        <form method="POST">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="<?php echo $editing_cat ? 'update' : 'create'; ?>">
            <?php if ($editing_cat): ?>
                <input type="hidden" name="id" value="<?php echo h($editing_cat['id']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" class="form-control-admin" value="<?php echo $editing_cat ? h($editing_cat['name']) : ''; ?>" required placeholder="e.g. Snacks">
            </div>
            <div class="form-group">
                <label>Parent Category</label>
                <select name="parent_id" class="form-control-admin" style="appearance:auto">
                    <option value="">None (Top Level)</option>
                    <?php foreach ($categories_list as $c): ?>
                        <option value="<?php echo h($c['id']); ?>" <?php echo ($editing_cat && $editing_cat['parent_id'] == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo h($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Custom Attributes (comma separated)</label>
                <input type="text" name="custom_attributes" class="form-control-admin" placeholder="e.g. Spice Level, Pack Weight" value="<?php
                    if ($editing_cat) {
                        $attrs = json_decode($editing_cat['custom_attributes'], true);
                        echo !empty($attrs) ? h(implode(', ', $attrs)) : '';
                    }
                ?>">
            </div>
            <div style="grid-column: 1 / -1; display:flex; gap:10px">
                <button type="submit" style="width:auto; padding: 10px 30px"><?php echo $editing_cat ? 'Update Category' : 'Register Category'; ?></button>
                <?php if ($editing_cat): ?>
                    <a href="categories.php" class="btn" style="background:#6c757d; color:#fff; padding:10px 30px; border-radius:5px; text-decoration:none">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h4 class="card-header__title">Existing Categories</h4>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Parent</th>
                        <th>Attributes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories_list as $c): ?>
                    <tr>
                        <td><?php echo h($c['id']); ?></td>
                        <td><strong><?php echo h($c['name']); ?></strong></td>
                        <td><?php echo h($c['parent_id'] ?? '-'); ?></td>
                        <td><span style="font-size:.8rem; color:#888"><?php
                            $attrs = json_decode($c['custom_attributes'], true);
                            echo !empty($attrs) ? h(implode(', ', $attrs)) : 'None';
                        ?></span></td>
                        <td>
                            <div style="display:flex; gap:15px">
                                <a href="?edit=<?php echo h($c['id']); ?>" style="color:var(--primary-color); text-decoration:none"><i class="fas fa-edit"></i> Edit</a>
                                <form method="POST" onsubmit="return confirm('Delete this category?')" style="display:inline">
                                    <?php csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo h($c['id']); ?>">
                                    <button type="submit" style="background:none; border:none; color:var(--danger-color); padding:0; font-size:inherit; cursor:pointer"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
