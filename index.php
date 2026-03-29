<?php
require_once 'includes/db_connect.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';

$database = new Database($conn);
$prod = new Product($database);
$user = new User($database);

$category_filter = $_GET['category'] ?? null;
if ($category_filter) {
    // Basic filtering logic if needed, but for now getAll returns all
    $products = $prod->getAll(); // In a real app, I'd filter this
} else {
    $products = $prod->getAll();
}

$page_title = 'ShopPV — Snack Different';
require_once 'includes/header.php';
?>

<!-- ═══════════════════════════ HOME ═══════════════════════════ -->
<div class="page active" id="home">
  <section class="hero">
    <div class="hero-left">
      <div>
        <div class="hero-eyebrow">Season's Boldest Drop</div>
        <h1 class="hero-heading">
          SHOP<br>
          <em>PV Rewards</em>
        </h1>
        <p class="hero-sub">Shop with a purpose. Every purchase helps you earn Reward Points (PV) that can be converted directly into cash.</p>
        <div class="hero-actions">
          <a class="btn btn-terra" href="#featured">Shop the Range</a>
          <a class="btn btn-ghost" href="user/dashboard.php">My Wallet</a>
        </div>
      </div>
      <div class="hero-stats">
        <div><div class="stat-val">PV</div><div class="stat-label">Rewards</div></div>
        <div><div class="stat-val">SECURE</div><div class="stat-label">Withdrawals</div></div>
        <div><div class="stat-val">₹</div><div class="stat-label">Cashback</div></div>
      </div>
    </div>
    <div class="hero-right">
      <div class="hero-img-top">
        <img src="https://images.unsplash.com/photo-1621852004158-f3bc188aec74?auto=format&fit=crop&q=80&w=800" class="abs-img" alt="Hero Featured Product">
        <div class="hero-badge"><strong>Earn PV</strong>On Every Order</div>
      </div>
      <div class="hero-img-bottom">
        <div class="hero-thumb">
          <img src="https://images.unsplash.com/photo-1599490659213-e2b9527bd087?auto=format&fit=crop&q=80&w=800" class="abs-img" alt="Product 1">
        </div>
        <div class="hero-thumb">
          <img src="https://images.unsplash.com/photo-1582058091505-f87a2e55a40f?auto=format&fit=crop&q=80&w=800" class="abs-img" alt="Product 2">
        </div>
      </div>
    </div>
  </section>

  <!-- STRIP -->
  <div class="strip">
    <div class="strip-item">🌶️ Spicy</div>
    <div class="strip-item">🍬 Sweet</div>
    <div class="strip-item">💥 Crunchy</div>
    <div class="strip-item">🧀 Cheesy</div>
    <div class="strip-item">🍋 Tangy</div>
    <div class="strip-item">🌊 Salty</div>
    <div class="strip-item">🍫 Chocolate</div>
  </div>

  <!-- PRODUCTS GRID -->
  <div class="section" id="featured">
    <div class="section-header">
      <h2 class="section-title">The Full <em>Lineup</em></h2>
      <a class="section-link">View All</a>
    </div>
    <div class="prod-grid">
      <?php foreach ($products as $p):
          $images = $prod->getImages($p['id']);
          $primary_image = 'https://via.placeholder.com/200';
          foreach ($images as $img) {
              if ($img['is_primary']) {
                  $primary_image = $img['image_path'];
                  break;
              }
          }
      ?>
      <div class="prod-card" onclick="window.location.href='product_detail.php?id=<?php echo h($p['id']); ?>'">
        <div class="prod-card-img">
          <img src="<?php echo h($primary_image); ?>" class="abs-img" alt="<?php echo h($p['name']); ?>">
          <span class="prod-card-label hot">Earn <?php echo h($p['pv_value']); ?> PV</span>
        </div>
        <div class="prod-card-body">
            <div class="prod-card-brand"><?php echo h($p['brand']); ?></div>
            <div class="prod-card-name"><?php echo h($p['name']); ?></div>
            <div class="prod-card-meta">
                <span class="prod-card-price">₹<?php echo h($p['price']); ?></span>
                <button class="add-pill btn-sm">View Details</button>
            </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php require_once 'includes/footer.php'; ?>
</div>
