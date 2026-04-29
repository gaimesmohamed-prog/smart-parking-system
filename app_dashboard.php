<?php
require_once 'header.php';

// Prepare slot status map
$historySlotCol = parking_history_slot_column($conn);
$cRes = $conn->query(
    "SELECT c.car_id, c.plate_number, c.car_model, c.car_color, c.slot_id,
            c.status AS car_status, c.parking_guid, c.reserved_hours,
            (SELECT h.entry_time FROM parking_history h 
             WHERE h.plate_number = c.plate_number AND UPPER(h.{$historySlotCol}) = UPPER(c.slot_id) AND h.exit_time IS NULL 
             ORDER BY h.id DESC LIMIT 1) as entry_time
     FROM cars c
     WHERE c.status IN ('reserved','occupied')"
);
$carsMap = [];
if ($cRes) {
    while($row = $cRes->fetch_assoc()) {
        $carsMap[strtoupper($row['slot_id'])] = $row;
    }
}

$slotsData = parking_get_dashboard_slots($conn, [
    'A1','A2','A3','A4','A5','A6','A7','A8','A9','A10',
    'B1','B2','B3','B4','B5','B6','B7','B8','B9','B10',
    'C1','C2','C3','C4','C5','C6','C7','C8','C9','C10',
    'D1','D2','D3','D4','D5','D6','D7','D8','D9','D10',
    'E1','E2','E3','E4','E5','E6','E7','E8','E9','E10'
]);
$slots = $slotsData['slots'];
?>
<?php include 'navbar.php'; ?>

<div class="wrapper animate-fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin:0; color: var(--text-main);">
            <i class="fas fa-microchip"></i> Admin Dashboard
        </h2>
    </div>

    <div class="report-summary-grid" style="margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-value" id="free-count"><?php echo $slotsData['free_count']; ?></div>
            <div class="stat-label">Available Slots</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="used-count"><?php echo $slotsData['occupied_count']; ?></div>
            <div class="stat-label">Occupied/Reserved</div>
        </div>
    </div>

    <div class="glass-card" style="padding: 20px; margin-bottom: 30px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px;">
            <a href="add_car.php" class="nav-btn" style="background: var(--accent-color); padding: 15px;">
                <i class="fas fa-plus-circle"></i>
                <span>Entry Ticket</span>
            </a>
            <a href="admin_scanner.php" class="nav-btn" style="background: #00b894; padding: 15px;">
                <i class="fas fa-qrcode"></i>
                <span>Scanner</span>
            </a>
            <a href="reports.php" class="nav-btn" style="background: #fdcb6e; padding: 15px;">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="view_cars.php" class="nav-btn" style="background: #ff7675; padding: 15px;">
                <i class="fas fa-car"></i>
                <span>Vehicles</span>
            </a>
        </div>
    </div>

    <div class="glass-card" style="padding: 20px;">
        <h5 style="margin-top:0; color: var(--text-main);"><i class="fas fa-th"></i> Real-time Slot Map</h5>
        <div class="grid-container" id="slots-container">
            <?php foreach ($slots as $slot):
                $slotIdUpper = strtoupper($slot['id']);
                $slotData  = $carsMap[$slotIdUpper] ?? null;
                $entryTs   = ($slotData && $slotData['entry_time']) ? strtotime($slotData['entry_time']) : 0;
                
                // Unify status classes with dashboard.css
                $statusClass = 'free';
                if ($slot['status'] === 'occupied') $statusClass = 'busy';
                elseif ($slot['status'] === 'warning' || $slot['status'] === 'reserved') $statusClass = 'reserved';
            ?>
                <div class="slot-card <?php echo $statusClass; ?> <?php echo $slotData ? 'clickable' : ''; ?>"
                     id="slot-<?php echo $slot['id']; ?>"
                     data-slot="<?php echo htmlspecialchars($slot['id']); ?>"
                     data-status="<?php echo $slotData ? htmlspecialchars($slotData['car_status']) : 'available'; ?>"
                     <?php if($slotData): ?>
                        data-plate="<?php echo htmlspecialchars($slotData['plate_number']); ?>"
                        data-model="<?php echo htmlspecialchars($slotData['car_model']); ?>"
                        data-color="<?php echo htmlspecialchars($slotData['car_color'] ?? 'White'); ?>"
                        data-guid="<?php echo htmlspecialchars($slotData['parking_guid']); ?>"
                        data-car-id="<?php echo (int)$slotData['car_id']; ?>"
                        data-entry="<?php echo $entryTs; ?>"
                        data-reserved="<?php echo (int)($slotData['reserved_hours'] ?? 1); ?>"
                        onclick="openSlotModal(this)"
                     <?php endif; ?>>
                    <div class="slot-id"><?php echo $slot['id']; ?><span class="dot"></span></div>
                    <div class="slot-status" id="status-text-<?php echo $slot['id']; ?>"><?php echo $slot['text']; ?></div>
                    <div class="mini-timer" id="mini-timer-<?php echo $slot['id']; ?>" style="display: <?php echo ($slot['status'] === 'occupied' || $slot['status'] === 'warning') ? 'block' : 'none'; ?>">00:00:00</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

        <div style="text-align:center;margin-top:15px;">
            <a href="map_view.php" class="nav-btn" style="display: inline-flex; background: var(--primary); color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 13px;">
                <i class="fas fa-map-marked-alt"></i> خريطة المواقف
            </a>
        </div>
    </div>
</div>

<div class="modal-overlay" id="slotModal" onclick="closeIfOverlay(event)">
    <div class="modal">
        <div class="modal-header" id="modalHeader">
            <div class="modal-slot" id="modalSlot">—</div>
            <h3 id="modalTitle">تفاصيل المكان</h3>
        </div>

        <div class="modal-timer">
            <div class="timer-lbl">⏱ وقت الركن</div>
            <div class="timer-val waiting" id="modalTimer">⏳</div>
            <div class="cost-val" id="modalCost">—</div>
        </div>

        <div class="modal-body">
            <div class="car-row"><span class="cr-lbl">رقم اللوحة</span><span class="cr-val" id="mPlate">—</span></div>
            <div class="car-row"><span class="cr-lbl">نوع السيارة</span><span class="cr-val" id="mModel">—</span></div>
            <div class="car-row"><span class="cr-lbl">اللون</span>      <span class="cr-val" id="mColor">—</span></div>
            <div class="car-row"><span class="cr-lbl">مدة الحجز</span>  <span class="cr-val" id="mReserved">—</span></div>
            <div class="car-row"><span class="cr-lbl">وقت الدخول</span><span class="cr-val" id="mEntry">—</span></div>
        </div>

        <div class="modal-footer">
            <button class="mbtn mbtn-enter" id="btnEnter" style="display:none" onclick="doMarkEntry()">
                🚗 دخول السيارة — بدء التايمر
            </button>
            <a href="#" class="mbtn mbtn-out" id="btnCheckout" style="display:none">
                🚪 تسجيل الخروج وحساب الفاتورة
            </a>
            <button class="mbtn mbtn-close" onclick="closeModal()">&#x2715; إغلاق</button>
        </div>
    </div>
</div>

<script>
const RATE       = 10 / 3600;
const SERVER_NOW = <?php echo time(); ?>;
const CLIENT_NOW = Math.floor(Date.now() / 1000);
const TIME_OFFSET = SERVER_NOW - CLIENT_NOW;

function getNow() {
    return Math.floor(Date.now() / 1000) + TIME_OFFSET;
}
let timerInt  = null;
let activeGuid = '';
let activeCarId = '';

function fmtTime(sec) {
    sec = Math.max(0, Math.floor(sec));
    return [Math.floor(sec/3600), Math.floor((sec%3600)/60), sec%60]
           .map(v => String(v).padStart(2,'0')).join(':');
}

function startTimer(fromSec, reservedHours = 1) {
    if (timerInt) clearInterval(timerInt);
    const timerEl = document.getElementById('modalTimer');
    const costEl  = document.getElementById('modalCost');
    const titleEl = document.getElementById('modalTitle');
    const reservedSec = reservedHours * 3600;

    function tick() {
        const now = getNow();
        const elapsed = now - fromSec;
        timerEl.textContent = fmtTime(elapsed);
        
        if (elapsed > reservedSec) {
            timerEl.style.color = '#ff4d4d';
            titleEl.textContent = '🚨 تم تجاوز الوقت - تطبق غرامة';
            titleEl.style.color = '#ff4d4d';
            const baseCost = reservedHours * 10;
            const extraHours = Math.ceil((elapsed - reservedSec) / 3600);
            const extraCost = extraHours * 15;
            costEl.textContent = (baseCost + extraCost).toFixed(2) + ' ج.م (شامل الغرامة)';
        } else {
            timerEl.style.color = '#e74c3c';
            costEl.textContent  = (Math.ceil(elapsed / 3600) * 10).toFixed(2) + ' ج.م';
        }
    }
    tick();
    timerInt = setInterval(tick, 1000);
}

function openSlotModal(el) {
    const slot    = el.dataset.slot;
    const status  = el.dataset.status;
    const plate   = el.dataset.plate;
    const model   = el.dataset.model;
    const color   = el.dataset.color;
    const guid    = el.dataset.guid;
    const carId   = el.dataset.carId;
    const entryTs = parseInt(el.dataset.entry) || 0;
    const reservedHours = parseInt(el.dataset.reserved) || 1;
    activeGuid  = guid;
    activeCarId = carId;

    document.getElementById('modalSlot').textContent = slot;
    document.getElementById('mPlate').textContent   = plate;
    document.getElementById('mModel').textContent   = model;
    document.getElementById('mColor').textContent   = color;
    document.getElementById('mReserved').textContent = reservedHours + ' ساعة';

    setModalStatus(status, entryTs, reservedHours);
    document.getElementById('slotModal').classList.add('open');
}

function setModalStatus(status, entryTs, reservedHours = 1) {
    const header   = document.getElementById('modalHeader');
    const timerEl  = document.getElementById('modalTimer');
    const costEl   = document.getElementById('modalCost');
    const btnEnter = document.getElementById('btnEnter');
    const btnOut   = document.getElementById('btnCheckout');

    header.className = 'modal-header ' + status;
    document.getElementById('modalTitle').textContent =
        status === 'occupied' ? '🔴 مكان مشغول' : '🟡 محجوز — في انتظار الدخول';

    if (entryTs) {
        const d = new Date(entryTs * 1000);
        document.getElementById('mEntry').textContent = d.toLocaleTimeString('ar-EG', {hour:'2-digit', minute:'2-digit'});
    } else {
        document.getElementById('mEntry').textContent = '—';
    }

    if (timerInt) clearInterval(timerInt);

    if (status === 'occupied' && entryTs) {
        startTimer(entryTs, reservedHours);
        btnEnter.style.display = 'none';
        btnOut.style.display   = 'block';
        btnOut.href = 'checkout.php?car_id=' + activeCarId + '&guid=' + encodeURIComponent(activeGuid);
    } else {
        timerEl.className   = 'timer-val waiting';
        timerEl.textContent = '⏳ انتظار الدخول';
        costEl.textContent  = 'التايمر يبدأ عند الدخول';
        btnEnter.style.display = 'block';
        btnOut.style.display   = 'none';
    }
}

function doMarkEntry() {
    const btn = document.getElementById('btnEnter');
    btn.disabled = true;
    btn.textContent = '⏳ جاري التسجيل...';

    const fd = new FormData();
    fd.append('guid', activeGuid);

    fetch('api/mark_entry.php', {method:'POST', body: fd})
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                showToast(data.message, 'error');
                btn.disabled = false;
                btn.textContent = '🚗 دخول السيارة — بدء التايمر';
                return;
            }
            closeModal();
            showToast('تم تسجيل دخول السيارة بنجاح', 'success');
            const slotId = document.getElementById('modalSlot').textContent;
            const slotEl = document.getElementById('slot-' + slotId);
            if (slotEl) {
                slotEl.className = 'slot-card busy clickable';
                slotEl.dataset.status = 'occupied';
                slotEl.dataset.entry  = data.entry_ts;
                const statusTxt = document.getElementById('status-text-' + slotId);
                if (statusTxt) statusTxt.textContent = 'Occupied';
                const miniTimer = document.getElementById('mini-timer-' + slotId);
                if (miniTimer) miniTimer.style.display = 'block';
            }
        })
        .catch(() => {
            showToast('تعذر الاتصال بالخادم', 'error');
            btn.disabled = false;
        });
}

function closeModal() {
    document.getElementById('slotModal').classList.remove('open');
    if (timerInt) clearInterval(timerInt);
}

function closeIfOverlay(e) {
    if (e.target === document.getElementById('slotModal')) closeModal();
}

function globalTick() {
    document.querySelectorAll('.slot-card.occupied, .slot-card.warning').forEach(el => {
        const entryTs = parseInt(el.dataset.entry);
        const resHours = parseInt(el.dataset.reserved) || 1;
        const timerEl = document.getElementById('mini-timer-' + el.dataset.slot);
        if (!timerEl) return;

        if (el.dataset.status === 'occupied') {
            timerEl.style.display = 'block';
            const startTs = entryTs || getNow();
            const elapsed = getNow() - startTs;
            timerEl.textContent = fmtTime(elapsed);
            if (elapsed > (resHours * 3600)) timerEl.style.color = '#ff4d4d';
            else timerEl.style.color = '';
        } else {
            timerEl.style.display = 'block';
            timerEl.textContent = 'Wait';
        }
    });
}
setInterval(globalTick, 1000);

function updateDashboard() {
    fetch('get_slots_status.php')
        .then(r => r.json())
        .then(data => {
            if (!data || !data.slots) return;
            data.slots.forEach(s => {
                const el = document.getElementById('slot-' + s.id);
                if (!el) return;
                
                // Map status to UI classes
                const cls = s.status === 'occupied' ? 'busy' : s.status === 'reserved' ? 'reserved' : 'free';
                const clickable = (s.status !== 'available' && s.status !== 'not-occupied') ? ' clickable' : '';
                
                el.className = 'slot-card ' + cls + clickable;
                el.dataset.status = s.status;
                
                const statusTxt = document.getElementById('status-text-' + s.id);
                if (statusTxt) statusTxt.textContent = s.status === 'occupied' ? 'Occupied' : (s.status === 'reserved' ? 'Reserved' : 'Available');
                
                if (s.status === 'available' || s.status === 'not-occupied') {
                    el.dataset.entry = "0";
                    el.onclick = null;
                    const miniTimer = document.getElementById('mini-timer-' + s.id);
                    if (miniTimer) miniTimer.style.display = 'none';
                } else {
                    // If it's a new booking, we'd need more data to make it clickable. 
                    // For now, let's just update the status colors.
                }
            });
            const free = data.slots.filter(s => s.status === 'available' || s.status === 'not-occupied').length;
            const occ  = data.slots.filter(s => s.status !== 'available' && s.status !== 'not-occupied').length;
            document.getElementById('free-count').textContent = free;
            document.getElementById('used-count').textContent = occ;
        })
        .catch(() => {});
}
setInterval(updateDashboard, 5000);
</script>
</body>
</html>
