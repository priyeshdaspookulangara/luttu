  <!-- FOOTER -->
  <footer>
    <div class="footer-top">
      <div class="footer-brand">
        <div class="footer-logo"><em>S</em>HOPPV</div>
        <p class="footer-tagline">Secure e-commerce platform with a rewarding PV wallet system. Shop more, earn more.</p>
        <div class="footer-socials">
          <div class="soc-btn">📸</div>
          <div class="soc-btn">▶</div>
          <div class="soc-btn">💬</div>
          <div class="soc-btn">𝕏</div>
        </div>
      </div>
      <div class="footer-links">
        <?php
        $current_dir = basename(getcwd());
        $rel_path = ($current_dir == 'admin' || $current_dir == 'user') ? '../' : './';
        ?>
        <div class="footer-col">
          <div class="footer-col-title">Shop</div>
          <ul>
            <li><a href="<?php echo $rel_path; ?>index.php">All Products</a></li>
            <li><a href="<?php echo $rel_path; ?>user/dashboard.php">My Wallet</a></li>
            <li><a href="<?php echo $rel_path; ?>admin/dashboard.php">Admin Panel</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <div class="footer-col-title">Help</div>
          <ul>
            <li><a href="#">Shipping Info</a></li>
            <li><a href="#">Returns & Refunds</a></li>
            <li><a href="#">FAQs</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 ShopPV Pvt. Ltd. · Secure E-Commerce & PV Wallet System</span>
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Use</a>
      </div>
    </div>
  </footer>

<script src="<?php echo $rel_path; ?>assets/js/scripts.js"></script>
</body>
</html>
