<?php
$page_title = 'Manage Categories — Admin';
require_once '../includes/header.php';

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
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Admin Panel</div>
    <div class="pg-hero-title">Manage <em>Categories</em></div>
</div>

<div class="section">
    <?php if ($message): ?>
        <div style="background:var(--sage);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.9rem"><?php echo h($message); ?></div>
    <?php endif; ?>

    <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand);margin-bottom:40px">
        <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:20px"><?php echo $editing_cat ? 'Edit Category' : 'Add New Category'; ?></div>
        <form method="POST">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="<?php echo $editing_cat ? 'update' : 'create'; ?>">
            <?php if ($editing_cat): ?>
                <input type="hidden" name="id" value="<?php echo h($editing_cat['id']); ?>">
            <?php endif; ?>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
                <div class="form-field">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-input" value="<?php echo $editing_cat ? h($editing_cat['name']) : ''; ?>" required>
                </div>
                <div class="form-field">
                    <label class="form-label">Parent Category</label>
                    <select name="parent_id" class="form-input" style="appearance:auto">
                        <option value="">None</option>
                        <?php foreach ($categories_list as $c): ?>
                            <option value="<?php echo h($c['id']); ?>" <?php echo ($editing_cat && $editing_cat['parent_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo h($c['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label">Custom Attributes (comma separated)</label>
                    <input type="text" name="custom_attributes" class="form-input" placeholder="e.g. Color, Size" value="<?php
                        if ($editing_cat) {
                            $attrs = json_decode($editing_cat['custom_attributes'], true);
                            echo !empty($attrs) ? h(implode(', ', $attrs)) : '';
                        }
                    ?>">
                </div>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-terra"><?php echo $editing_cat ? 'Update' : 'Add'; ?></button>
                <?php if ($editing_cat): ?>
                    <a href="categories.php" class="btn btn-outline">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem">
            <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                <tr>
                    <th style="padding:12px 24px;text-align:left">ID</th>
                    <th style="padding:12px 24px;text-align:left">Name</th>
                    <th style="padding:12px 24px;text-align:left">Parent</th>
                    <th style="padding:12px 24px;text-align:left">Attributes</th>
                    <th style="padding:12px 24px;text-align:left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories_list as $c): ?>
                <tr style="border-bottom:1px solid var(--sand)">
                    <td style="padding:12px 24px"><?php echo h($c['id']); ?></td>
                    <td style="padding:12px 24px;font-weight:700"><?php echo h($c['name']); ?></td>
                    <td style="padding:12px 24px"><?php echo h($c['parent_id'] ?? '-'); ?></td>
                    <td style="padding:12px 24px;font-size:.75rem;color:#888"><?php
                        $attrs = json_decode($c['custom_attributes'], true);
                        echo !empty($attrs) ? h(implode(', ', $attrs)) : '-';
                    ?></td>
                    <td style="padding:12px 24px">
                        <div style="display:flex;gap:8px">
                            <a href="?edit=<?php echo h($c['id']); ?>" class="btn btn-ghost btn-sm" style="color:var(--sky)">Edit</a>
                            <form method="POST" onsubmit="return confirm('Are you sure?')">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo h($c['id']); ?>">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--terra)">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
