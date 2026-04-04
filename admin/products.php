<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Wallet.php';

$database = new Database($conn);
$user = new User($database);
$prod = new Product($database);
$cat = new Category($database);
$wallet = new Wallet($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = '';
$error_msg = '';
$editing_prod = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $list_price = (float)($_POST['list_price'] ?? 0);
        $margin_amount = (float)($_POST['margin_amount'] ?? 0);
        $pv_value = (float)($_POST['pv_value'] ?? 0);
        $brand = $_POST['brand'] ?? '';
        $manufacturer = $_POST['manufacturer'] ?? '';
        $supplier = $_POST['supplier'] ?? '';

        $attributes = [];
        if (isset($_POST['attr_key']) && isset($_POST['attr_val'])) {
            foreach ($_POST['attr_key'] as $index => $key) {
                if (!empty($key)) {
                    $attributes[$key] = $_POST['attr_val'][$index] ?? '';
                }
            }
        }

        try {
            if ($action === 'create') {
                $product_id = $prod->create($category_id, $name, $description, $price, $list_price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes);
                $message = 'Product created successfully!';
            } else {
                $product_id = (int)$_POST['id'];
                $prod->update($product_id, $category_id, $name, $description, $price, $list_price, $margin_amount, $pv_value, $brand, $manufacturer, $supplier, $attributes);
                $message = 'Product updated successfully!';
            }

            // Image Upload
            if (!empty($_FILES['product_images']['name'][0])) {
                $upload_dir = '../assets/uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                foreach ($_FILES['product_images']['name'] as $i => $filename) {
                    if ($_FILES['product_images']['error'][$i] === 0) {
                        $tmp_name = $_FILES['product_images']['tmp_name'][$i];
                        $size = $_FILES['product_images']['size'][$i];
                        if ($size > 2 * 1024 * 1024) continue;
                        $mime_type = mime_content_type($tmp_name);
                        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                        if (array_key_exists($mime_type, $allowed)) {
                            $new_fn = uniqid() . '.' . $allowed[$mime_type];
                            if (move_uploaded_file($tmp_name, $upload_dir . $new_fn)) {
                                $prod->addImage($product_id, 'assets/uploads/' . $new_fn, $i === 0);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $error_msg = 'Error: ' . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $prod->delete($id);
        $message = 'Product deleted successfully!';
    }
}

if (isset($_GET['edit'])) {
    $editing_prod = $prod->getById((int)$_GET['edit']);
}

$products_list = $prod->getAll();
$categories_list = $cat->getAll();

// Get current PV settings for auto-calculation
$pv_settings = $wallet->getCurrentPVSettings();
$cash_per_pv = $pv_settings ? (float)$pv_settings['cash_per_pv'] : 0;

$page_title = 'Manage Products — ShopPV Admin';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
        <h1 class="m-0">Inventory Management</h1>
    </div>
</div>

<?php if ($message): ?>
    <div style="background:var(--success-color); color:#fff; padding:15px; border-radius:5px; margin-bottom:20px"><?php echo h($message); ?></div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div style="background:var(--danger-color); color:#fff; padding:15px; border-radius:5px; margin-bottom:20px"><?php echo h($error_msg); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header card-header-large bg-white">
        <h4 class="card-header__title"><?php echo $editing_prod ? 'Edit Product' : 'Register New Product'; ?></h4>
    </div>
    <div class="card-body form-card">
        <form method="POST" enctype="multipart/form-data">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="<?php echo $editing_prod ? 'update' : 'create'; ?>">
            <?php if ($editing_prod): ?>
                <input type="hidden" name="id" value="<?php echo h($editing_prod['id']); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['name']) : ''; ?>" required placeholder="Product name">
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control-admin" id="category_id" onchange="loadAttributes()" style="appearance:auto">
                    <option value="">Select Category</option>
                    <?php foreach ($categories_list as $c): ?>
                        <option value="<?php echo h($c['id']); ?>" data-attrs='<?php echo h($c['custom_attributes']); ?>' <?php echo ($editing_prod && $editing_prod['category_id'] == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo h($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>M.R.P. (₹)</label>
                <input type="number" step="0.01" name="list_price" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['list_price']) : ''; ?>" required placeholder="Maximum Retail Price">
            </div>

            <div class="form-group">
                <label>S.P. (Selling Price ₹)</label>
                <input type="number" step="0.01" name="price" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['price']) : ''; ?>" required placeholder="Discounted Price">
            </div>

            <div class="form-group">
                <label>Internal Margin (₹)</label>
                <input type="number" step="0.01" id="margin_amount" name="margin_amount" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['margin_amount']) : ''; ?>" required oninput="calculatePV()">
                <small class="text-muted">Used to auto-calculate PV based on current rate (₹<?php echo $cash_per_pv; ?>/PV)</small>
            </div>

            <div class="form-group">
                <label>PV Value</label>
                <input type="number" step="0.01" id="pv_value" name="pv_value" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['pv_value']) : ''; ?>" required>
                <small class="text-info" id="pv_calc_hint"></small>
            </div>

            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['brand']) : ''; ?>" placeholder="Brand">
            </div>

            <div class="form-group">
                <label>Manufacturer</label>
                <input type="text" name="manufacturer" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['manufacturer']) : ''; ?>" placeholder="Manufacturer">
            </div>

            <div class="form-group">
                <label>Supplier</label>
                <input type="text" name="supplier" class="form-control-admin" value="<?php echo $editing_prod ? h($editing_prod['supplier']) : ''; ?>" placeholder="Supplier">
            </div>

            <div class="form-group">
                <label>Images (Max 2MB)</label>
                <input type="file" name="product_images[]" class="form-control-admin" multiple style="padding:7px">
            </div>

            <div id="dynamic-attributes" style="grid-column: 1 / -1; display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <?php if ($editing_prod && !empty($editing_prod['attributes'])):
                    $attrs = json_decode($editing_prod['attributes'], true);
                    if ($attrs):
                        foreach ($attrs as $key => $val): ?>
                            <div class="form-group">
                                <label><?php echo h($key); ?></label>
                                <input type="hidden" name="attr_key[]" value="<?php echo h($key); ?>">
                                <input type="text" name="attr_val[]" class="form-control-admin" value="<?php echo h($val); ?>">
                            </div>
                        <?php endforeach;
                    endif;
                endif; ?>
            </div>

            <div class="form-group" style="grid-column: 1 / -1">
                <label>Description</label>
                <textarea name="description" class="form-control-admin" rows="3" placeholder="Description"><?php echo $editing_prod ? h($editing_prod['description']) : ''; ?></textarea>
            </div>

            <div style="grid-column: 1 / -1; display:flex; gap:10px">
                <button type="submit" style="width:auto; padding: 10px 40px"><?php echo $editing_prod ? 'Update Product' : 'Register Product'; ?></button>
                <?php if ($editing_prod): ?>
                    <a href="products.php" class="btn" style="background:#6c757d; color:#fff; padding:10px 30px; border-radius:5px; text-decoration:none">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-large bg-white">
        <h4 class="card-header__title">Active Inventory</h4>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>M.R.P / S.P.</th>
                        <th>PV Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products_list)): ?>
                        <?php foreach ($products_list as $p): ?>
                        <tr>
                            <td>#<?php echo h($p['id']); ?></td>
                            <td><strong><?php echo h($p['name']); ?></strong></td>
                            <td><?php echo h($p['category_name']); ?></td>
                            <td>
                                <span style="text-decoration:line-through; color:#999; font-size:0.85em">₹<?php echo h($p['list_price']); ?></span><br>
                                <strong>₹<?php echo h($p['price']); ?></strong>
                            </td>
                            <td><span style="color:var(--primary-color); font-weight:600"><?php echo h($p['pv_value']); ?> PV</span></td>
                            <td>
                                <div style="display:flex; gap:15px">
                                    <a href="?edit=<?php echo h($p['id']); ?>" style="color:var(--primary-color); text-decoration:none"><i class="fas fa-edit"></i> Edit</a>
                                    <form method="POST" onsubmit="return confirm('Delete product?')" style="display:inline">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo h($p['id']); ?>">
                                        <button type="submit" style="background:none; border:none; color:var(--danger-color); padding:0; font-size:inherit; cursor:pointer"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:30px; color:#aaa">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const cashPerPv = <?php echo $cash_per_pv; ?>;

function calculatePV() {
    const margin = parseFloat(document.getElementById('margin_amount').value);
    const pvInput = document.getElementById('pv_value');
    const hint = document.getElementById('pv_calc_hint');

    if (!isNaN(margin) && cashPerPv > 0) {
        const calculatedPv = (margin / cashPerPv).toFixed(2);
        pvInput.value = calculatedPv;
        hint.textContent = `Auto-calculated: ${margin} / ${cashPerPv} = ${calculatedPv} PV`;
    } else {
        hint.textContent = "";
    }
}

function loadAttributes() {
    const select = document.getElementById('category_id');
    const selectedOption = select.options[select.selectedIndex];
    const attrContainer = document.getElementById('dynamic-attributes');
    attrContainer.innerHTML = '';
    const attrData = selectedOption.getAttribute('data-attrs');
    if (attrData && attrData !== 'null' && attrData !== '') {
        try {
            const attrs = JSON.parse(attrData);
            if (Array.isArray(attrs)) {
                attrs.forEach(attr => {
                    const div = document.createElement('div');
                    div.className = 'form-group';
                    div.innerHTML = `<label>${attr}</label><input type="hidden" name="attr_key[]" value="${attr}"><input type="text" name="attr_val[]" class="form-control-admin" placeholder="Value for ${attr}">`;
                    attrContainer.appendChild(div);
                });
            }
        } catch (e) {}
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
