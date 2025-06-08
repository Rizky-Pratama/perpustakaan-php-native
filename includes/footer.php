    </div>
    <footer class="text-center py-3 mt-5">
      <div class="container">
        <div class="row">
          <div class="col-md-6 text-md-start">
            <p class="mb-0">Sistem Perpustakaan &copy; <?php echo date('Y'); ?></p>
          </div>
          <div class="col-md-6 text-md-end">
            <?php if (isLoggedIn()): ?>
              <p class="mb-0">
                Login sebagai:
                <span class="badge <?php echo isAdmin() ? 'bg-danger' : 'bg-info'; ?>">
                  <?php echo $_SESSION['user_role']; ?>
                </span> |
                <a href="<?php echo BASE_URL; ?>auth/logout.php" class="text-decoration-none">Logout</a>
              </p>
            <?php else: ?>
              <p class="mb-0">
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="text-decoration-none">Login</a> |
                <a href="<?php echo BASE_URL; ?>auth/register.php" class="text-decoration-none">Daftar</a>
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo JS_URL; ?>script.js"></script>
    </body>

    </html>
