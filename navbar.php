<?php $nav_dashboard_link = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'app_dashboard.php' : 'dashboard.php'; ?>
<div class="global-navbar">
    <div class="nav-container">
        <span onclick="openSidebar()" style="font-size:24px; cursor:pointer; color:white; margin-right: 15px;">☰</span>
        <a href="javascript:history.back()" class="nav-brand nav-action-btn">
            <i class="fas fa-arrow-right"></i>
            <span>رجوع</span>
        </a>
        <div class="nav-center">Smart Parking</div>
        <div style="display: flex; gap: 15px;">
            <a href="?lang=ar" class="lang-toggle">EN</a>
            <a href="?lang=en" class="lang-toggle">عربي</a>
            <a href="<?php echo $nav_dashboard_link; ?>" class="nav-action-btn">
                <i class="fas fa-home"></i> اللوحة
            </a>
        </div>
    </div>
</div>

<div id="mySideNav" class="sidebar">
  <a href="javascript:void(0)" class="closebtn" onclick="closeSidebar()">&times;</a>
  <a href="<?php echo $nav_dashboard_link; ?>"><i class="fas fa-home"></i> لوحة التحكم</a>
  <a href="map_view.php"><i class="fas fa-map-marked-alt"></i> خريطة المواقف</a>
  <a href="wallet.php"><i class="fas fa-wallet"></i> المحفظة</a>
  <a href="reports.php"><i class="fas fa-file-alt"></i> تقارير</a>
  <a href="history.php"><i class="fas fa-history"></i> سجل</a>
</div>

<style>
.sidebar {
  height: 100%; width: 0; position: fixed; z-index: 10000; top: 0; right: 0; background-color: #5D5FEF; overflow-x: hidden; transition: 0.4s;
  padding-top: 60px; box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}
.sidebar a { padding: 15px 25px; text-decoration: none; font-size: 18px; color: white; display: block; transition: 0.3s; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar a:hover { background-color: rgba(255,255,255,0.2); }
.sidebar .closebtn { position: absolute; top: 0; left: 15px; font-size: 36px; padding: 10px; border-bottom: none; }
</style>

<script>
function openSidebar() { document.getElementById("mySideNav").style.width = "300px"; }
function closeSidebar() { document.getElementById("mySideNav").style.width = "0"; }
</script>

