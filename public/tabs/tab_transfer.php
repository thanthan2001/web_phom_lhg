<!-- tab trả -->
<div class="scan-controls mb-2">
    <div>
        <input type="checkbox" id="check-all-return" onclick="toggleAll(this, 'return')">
        <label for="check-all-return"><strong>Tất cả</strong></label>
    </div>
    <div class="search-box">
        <input type="text" placeholder="Mã vật tư">
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        <span class="text-primary">Đã chọn: <span id="count-return">0</span></span>
    </div>
</div>

<?php for ($k = 0; $k < 10; $k++): ?>
    <div class="d-flex align-items-start gap-2 mb-2">
        <!-- Checkbox -->
        <div style="padding-top: 10px;">
            <input type="checkbox" class="item-checkbox" data-tab="return" onchange="updateCount('return')">
        </div>

        <!-- Card -->
        <div class="w-100 border rounded p-2 bg-light">
            <div class="row fw-bold text-dark">
                <div class="col">Mã vật tư</div>
                <div class="col">Tên phom</div>
                <div class="col">Chất liệu</div>
                <div class="col">Size</div>
                <div class="col">Số lượng</div>
                <div class="col">Tồn kho</div>
            </div>
            <div class="row">
                <div class="col">MH<?= rand(100000, 999999) ?></div>
                <div class="col">Phom <?= chr(65 + $k) ?></div>
                <div class="col">Nhựa</div>
                <div class="col"><?= rand(36, 42) ?></div>
                <div class="col">20</div>
                <div class="col">200</div>
            </div>
        </div>
    </div>
<?php endfor; ?>

<button class="btn-create-lend">Tạo đơn trả</button>
