<?php
$role = $_SESSION['role'] ?? 'user';
$is_admin = ($role === 'admin');
$nav_dashboard_link = $is_admin ? 'app_dashboard.php' : 'dashboard.php';
$userName = $_SESSION['full_name'] ?? $_SESSION['user'] ?? 'User';
$userBalance = $_SESSION['balance'] ?? 0.00;
?>
<div class="global-navbar">
    <div class="nav-container">
        <!-- Brand & Menu -->
        <div class="nav-brand-container">
            <?php if (!$is_admin): ?>
            <span onclick="openSidebar()" class="menu-trigger">
                <i class="fas fa-bars"></i>
            </span>
            <?php endif; ?>
            <a href="<?php echo $nav_dashboard_link; ?>" class="nav-brand-container" style="text-decoration:none;">
                <i class="fas fa-parking brand-icon"></i>
                <div class="brand-stack">
                    <span class="nav-brand-text">Smart Parking</span>
                    <span class="page-title-indicator">
                        <?php echo $is_admin ? 'Admin Dashboard' : 'User Dashboard'; ?>
                    </span>
                </div>
            </a>
        </div>
        
        <!-- User Info & Controls -->
        <div class="nav-controls-group">
            <?php if (is_logged_in()): ?>
            <div class="user-status-pill d-none-mobile">
                <span class="user-name"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userName); ?></span>
                <span class="user-balance"><i class="fas fa-wallet"></i> <?php echo number_format($userBalance, 2); ?> <small>JOD</small></span>
            </div>
            <?php endif; ?>

            <div class="toggle-group">
                <!-- Theme Toggle -->
                <button type="button" onclick="toggleTheme(event)" class="icon-btn" title="تبديل الوضع">
                    <i class="fas fa-moon dark-icon"></i>
                    <i class="fas fa-sun light-icon"></i>
                </button>
                
                <!-- Lang Toggle -->
                <a href="?set_lang=<?php echo $current_lang === 'ar' ? 'en' : 'ar'; ?>" class="lang-toggle">
                    <?php echo $current_lang === 'ar' ? 'EN' : 'عربي'; ?>
                </a>

                <!-- Home/Back -->
                <a href="<?php echo $nav_dashboard_link; ?>" class="icon-btn d-none-mobile" title="الرئيسية">
                    <i class="fas fa-home"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div id="mySideNav" class="sidebar">
  <div class="sidebar-header">
      <div class="sidebar-logo">
          <i class="fas fa-parking"></i>
          <span>BIS Parking</span>
      </div>
      <a href="javascript:void(0)" class="closebtn" onclick="closeSidebar()">&times;</a>
  </div>
  
  <div class="sidebar-links">
      <a href="<?php echo $nav_dashboard_link; ?>"><i class="fas fa-home"></i> لوحة التحكم</a>
    
      <?php if ($is_admin): ?>
        <a href="admin_scanner.php"><i class="fas fa-qrcode"></i> QR Scanner</a>
        <a href="reports.php"><i class="fas fa-chart-pie"></i> تقارير</a>
        <a href="view_cars.php"><i class="fas fa-car"></i> السيارات</a>
        <a href="history.php"><i class="fas fa-history"></i> سجل العمليات</a>
      <?php else: ?>
        <a href="map_view.php"><i class="fas fa-map-marked-alt"></i> خريطة المواقف</a>
        <a href="wallet.php"><i class="fas fa-wallet"></i> المحفظة</a>
        <a href="reports.php"><i class="fas fa-file-alt"></i> تقارير</a>
        <a href="history.php"><i class="fas fa-history"></i> سجل</a>
      <?php endif; ?>
    
      <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
  </div>
</div>

<style>
.nav-controls-group { display: flex; align-items: center; gap: 15px; }
.user-status-pill {
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 50px;
    padding: 5px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
}
.user-name { border-right: 1px solid rgba(0,0,0,0.1); padding-right: 12px; }
html[dir="rtl"] .user-name { border-right: none; border-left: 1px solid rgba(255,255,255,0.1); padding-right: 0; padding-left: 12px; }
.user-balance { color: #10b981; }

.toggle-group { display: flex; align-items: center; gap: 8px; }
.icon-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-border);
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-main);
    cursor: pointer;
    transition: 0.3s;
}
.icon-btn:hover { background: var(--accent-color); color: white; transform: translateY(-2px); }

.lang-toggle {
    text-decoration: none;
    color: var(--text-main);
    font-weight: 800;
    font-size: 12px;
    padding: 8px 12px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-border);
    transition: 0.3s;
}
.lang-toggle:hover { background: var(--text-main); color: var(--nav-bg); }

.brand-stack { display: flex; flex-direction: column; line-height: 1.1; }
.nav-brand-text { font-family: 'Righteous', cursive; font-size: 18px; letter-spacing: 0.5px; color: var(--text-main); }
.page-title-indicator { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--accent-color); opacity: 0.8; }

.menu-trigger { font-size: 20px; cursor: pointer; color: var(--text-main); margin-right: 15px; }
html[dir="rtl"] .menu-trigger { margin-right: 0; margin-left: 15px; }

/* Theme Icon Logic */
[data-theme="dark"] .light-icon { display: block; }
[data-theme="dark"] .dark-icon { display: none; }
[data-theme="light"] .light-icon { display: none; }
[data-theme="light"] .dark-icon { display: block; }

/* Sidebar Premium */
.sidebar {
  height: 100%; width: 0; position: fixed; z-index: 10001; top: 0; right: 0;
  background: var(--nav-bg); backdrop-filter: blur(20px);
  overflow-x: hidden; transition: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.sidebar-header { padding: 30px 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; }
.sidebar-logo { display: flex; align-items: center; gap: 10px; color: var(--accent-color); font-weight: 900; font-size: 20px; }
.sidebar a { padding: 18px 25px; text-decoration: none; font-size: 16px; color: var(--text-main); display: block; transition: 0.3s; font-weight: 600; border-bottom: 1px solid var(--glass-border); }
.sidebar a:hover { background: rgba(99, 102, 241, 0.1); color: var(--accent-color); padding-right: 35px; }
.logout-link { color: #ff4d4d !important; margin-top: 30px; }

@media (max-width: 768px) {
    .d-none-mobile { display: none; }
}
</style>

<script>
function openSidebar()  { document.getElementById("mySideNav").style.width = "300px"; }
function closeSidebar() { document.getElementById("mySideNav").style.width = "0"; }
</script>
