<?php
// Report all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db_connect.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';

$database = new Database($conn);
$prod = new Product($database);
$user = new User($database);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
echo "<!-- Debug: Product ID = $id -->";

if ($id <= 0) {
    echo "<h1>Invalid Product ID</h1>";
    exit;
}

try {
    $p = $prod->getById($id);
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

if (!$p) {
    echo "<h1>Product not found (ID: $id)</h1>";
    exit;
}

$images_res = $prod->getImages($p['id']);
$images = [];
if ($images_res) {
    while ($img = $images_res->fetch_assoc()) {
        $images[] = $img;
    }
}

$attributes = json_decode($p['attributes'], true);

$page_title = h($p['name']);
require_once 'includes/header.php';
?>

<!-- ═══════════════════════════ PDP ════════════════════════════ -->
<div class="page active" id="product">
  <div class="pg-hero">
    <div class="pg-hero-eyebrow">Product Details</div>
    <div class="pg-hero-title"><em><?php echo h($p['name']); ?></em></div>
  </div>
  <div class="pdp-wrap">
    <div class="pdp-grid">
      <div class="pdp-gallery">
        <div class="pdp-main">
          <?php
            $primary_image = 'https://via.placeholder.com/500x500';
            foreach ($images as $img) {
                if ($img['is_primary']) {
                    $primary_image = $img['image_path'];
                    break;
                }
            }
            if ($primary_image == 'https://via.placeholder.com/500x500' && !empty($images)) {
                $primary_image = $images[0]['image_path'];
            }
          ?>
          <img src="<?php echo h($primary_image); ?>" class="abs-img" id="pdpMainImg" alt="Product Image">
        </div>
        <div class="pdp-thumbs">
          <?php foreach ($images as $img): ?>
            <div class="pdp-thumb <?php echo ($img['image_path'] == $primary_image) ? 'active' : ''; ?>" onclick="setThumb(this,'<?php echo h($img['image_path']); ?>')">
              <img src="<?php echo h($img['image_path']); ?>" class="img-cover" alt="Thumb">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="pdp-info">
        <div class="pdp-breadcrumb"><a href="index.php">Home</a> · <a><?php echo h($p['category_name'] ?? 'General'); ?></a> · <?php echo h($p['name']); ?></div>
        <div class="pdp-tag">🔥 Earn <?php echo h($p['pv_value']); ?> PV</div>
        <h1 class="pdp-title"><?php echo h($p['name']); ?></h1>
        <p class="pdp-sub"><?php echo nl2br(h($p['description'])); ?></p>

        <div class="pdp-divider"></div>
        <div class="pdp-price-row">
          <span class="pdp-price">₹<?php echo h($p['price']); ?></span>
        </div>
        <div class="pdp-stock">In Stock — Reward Points: <?php echo h($p['pv_value']); ?> PV</div>

        <?php if (!empty($attributes)): ?>
        <div style="font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#888;margin-bottom:10px">Specifications</div>
        <div style="margin-bottom:24px">
            <table class="table table-sm" style="width:100%;font-size:.85rem;border-collapse:collapse">
                <?php foreach ($attributes as $key => $val): ?>
                <tr style="border-bottom:1px solid var(--sand)">
                    <th width="120" style="padding:8px 0;text-align:left"><?php echo h($key); ?>:</th>
                    <td style="padding:8px 0"><?php echo h($val); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST">
          <input type="hidden" name="product_id" value="<?php echo h($p['id']); ?>">
          <div class="qty-row">
            <span class="qty-label">Qty</span>
            <div class="qty-ctrl">
              <button type="button" class="qty-btn" onclick="qtyChange(-1)">−</button>
              <span class="qty-num" id="qtyNum">1</span>
              <button type="button" class="qty-btn" onclick="qtyChange(1)">+</button>
            </div>
          </div>
          <div class="pdp-cta">
            <button type="submit" class="btn btn-terra">Buy Now & Earn PV</button>
          </div>
        </form>
        <div class="pdp-trust">
          <div class="pdp-trust-item">🚚 Free delivery above ₹499</div>
          <div class="pdp-trust-item">↩️ 7-day returns</div>
          <div class="pdp-trust-item">✅ Reward System Verified</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
