<?php
$page_title = 'Manage Products — Admin';
require_once '../includes/header.php';
require_once '../classes/Product.php';

$prod = new Product($database);

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
                    if ($size > 2 * 1024 * 1024) continue;

                    $mime_type = mime_content_type($tmp_name);
                    $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
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

$products_list = $prod->getAll();
$categories_list = $cat->getAll();
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Admin Panel</div>
    <div class="pg-hero-title">Manage <em>Products</em></div>
</div>

<div class="section">
    <?php if ($message): ?>
        <div style="background:var(--sage);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.9rem"><?php echo h($message); ?></div>
    <?php endif; ?>

    <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand);margin-bottom:40px">
        <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:20px"><?php echo $editing_prod ? 'Edit Product' : 'Add New Product'; ?></div>
        <form method="POST" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="<?php echo $editing_prod ? 'update' : 'create'; ?>">
            <?php if ($editing_prod): ?>
                <input type="hidden" name="id" value="<?php echo h($editing_prod['id']); ?>">
            <?php endif; ?>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
                <div class="form-field">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-input" value="<?php echo $editing_prod ? h($editing_prod['name']) : ''; ?>" required>
                </div>
                <div class="form-field">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-input" id="category_id" onchange="loadAttributes()" style="appearance:auto">
                        <option value="">Select Category</option>
                        <?php foreach ($categories_list as $c): ?>
                            <option value="<?php echo h($c['id']); ?>" data-attrs='<?php echo h($c['custom_attributes']); ?>' <?php echo ($editing_prod && $editing_prod['category_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo h($c['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label">Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-input" value="<?php echo $editing_prod ? h($editing_prod['price']) : ''; ?>" required>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
                <div class="form-field">
                    <label class="form-label">Margin Amount (₹)</label>
                    <input type="number" step="0.01" name="margin_amount" class="form-input" value="<?php echo $editing_prod ? h($editing_prod['margin_amount']) : ''; ?>" required>
                </div>
                <div class="form-field">
                    <label class="form-label">PV Value</label>
                    <input type="number" step="0.01" name="pv_value" class="form-input" value="<?php echo $editing_prod ? h($editing_prod['pv_value']) : ''; ?>" required>
                </div>
                <div class="form-field">
                    <label class="form-label">Images (Max 2MB)</label>
                    <input type="file" name="product_images[]" class="form-input" multiple style="padding:10px">
                </div>
            </div>
            <div id="dynamic-attributes" class="mb-3">
                <?php if ($editing_prod && !empty($editing_prod['attributes'])):
                    $attrs = json_decode($editing_prod['attributes'], true);
                    echo '<h5>Custom Attributes</h5><div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:20px">';
                    foreach ($attrs as $key => $val) {
                        echo '<div class="form-field">
                            <label class="form-label">'.h($key).'</label>
                            <input type="hidden" name="attr_key[]" value="'.h($key).'">
                            <input type="text" name="attr_val[]" class="form-input" value="'.h($val).'">
                        </div>';
                    }
                    echo '</div>';
                endif; ?>
            </div>
            <div class="form-field">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3"><?php echo $editing_prod ? h($editing_prod['description']) : ''; ?></textarea>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-terra"><?php echo $editing_prod ? 'Update' : 'Add'; ?></button>
                <?php if ($editing_prod): ?>
                    <a href="products.php" class="btn btn-outline">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem">
            <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                <tr>
                    <th style="padding:12px 24px;text-align:left">ID</th>
                    <th style="padding:12px 24px;text-align:left">Product</th>
                    <th style="padding:12px 24px;text-align:left">Category</th>
                    <th style="padding:12px 24px;text-align:left">Price</th>
                    <th style="padding:12px 24px;text-align:left">PV Value</th>
                    <th style="padding:12px 24px;text-align:left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products_list as $p): ?>
                <tr style="border-bottom:1px solid var(--sand)">
                    <td style="padding:12px 24px"><?php echo h($p['id']); ?></td>
                    <td style="padding:12px 24px;font-weight:700"><?php echo h($p['name']); ?></td>
                    <td style="padding:12px 24px"><?php echo h($p['category_name']); ?></td>
                    <td style="padding:12px 24px">₹<?php echo h($p['price']); ?></td>
                    <td style="padding:12px 24px"><?php echo h($p['pv_value']); ?> PV</td>
                    <td style="padding:12px 24px">
                        <div style="display:flex;gap:8px">
                            <a href="?edit=<?php echo h($p['id']); ?>" class="btn btn-ghost btn-sm" style="color:var(--sky)">Edit</a>
                            <form method="POST" onsubmit="return confirm('Are you sure?')">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo h($p['id']); ?>">
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

<script>
function loadAttributes() {
    const select = document.getElementById('category_id');
    const selectedOption = select.options[select.selectedIndex];
    const attrContainer = document.getElementById('dynamic-attributes');
    if (selectedOption.dataset.attrs) {
        const attrs = JSON.parse(selectedOption.dataset.attrs);
        if (attrs && attrs.length > 0) {
            let html = '<h5>Custom Attributes</h5><div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:20px">';
            attrs.forEach(attr => {
                html += `<div class="form-field">
                    <label class="form-label">${attr}</label>
                    <input type="hidden" name="attr_key[]" value="${attr}">
                    <input type="text" name="attr_val[]" class="form-input" placeholder="Value for ${attr}">
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

<?php require_once '../includes/footer.php'; ?>
