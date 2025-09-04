<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="../../assets/logo.png" alt="Logo" width="40" class="me-2">
      SIPANDU
    </a>
    <div class="d-flex align-items-center">
      <div class="dropdown">
        <a href="#" class="text-white fw-bold text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <?= htmlspecialchars($_SESSION['username']); ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li>
            <a class="dropdown-item" href="../../auth/ganti_password.php">
              <i class="bi bi-key me-2"></i>Ganti Password
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item text-danger" href="../../auth/logout.php">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>