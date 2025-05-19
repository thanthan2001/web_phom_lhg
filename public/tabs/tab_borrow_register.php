<div class="scan-controls mb-2">
    <div class="search-box">
        <input type="text" placeholder="Nhập để tìm kiếm...">
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    </div>
</div>

<?php
$totalItems = 16;
$itemsPerPage = 3;

$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$startIndex = ($currentPage - 1) * $itemsPerPage;
$totalPages = ceil($totalItems / $itemsPerPage);

ob_start();
for ($i = $startIndex; $i < min($startIndex + $itemsPerPage, $totalItems); $i++):
?>
    <div class="lend-card">
        <div class="info-grid">
            <div><strong>Số thẻ:</strong> 123<?= $i ?></div>
            <div><strong>Tên người mượn:</strong> Nguyễn Văn <?= chr(65 + $i % 26) ?></div>
            <div><strong>Đơn vị:</strong> JHGSG</div>
            <div><strong>Liên:</strong> ASSNHN</div>
        </div>

        <div class="info-grid">
            <div><strong>Ngày mượn:</strong> 10/05/2025</div>
            <div><strong>Ngày nhận:</strong> 10/05/2025</div>
            <div><strong>Cán bộ xác nhận:</strong> Nguyễn Văn B</div>
        </div>

        <div class="info-grid">
            <div><strong>Mã vật tư:</strong> HGSAHJK</div>
            <div><strong>Tổng số lượng :</strong> 20</div>
        </div>

        <table class="lend-table">
            <thead>
                <tr>
                    <th>Mã vật tư</th>
                    <th>Tên Phom</th>
                    <th>Size</th>
                    <th>Chất liệu</th>
                    <th>Tồn kho</th>
                    <th>Số lượng đăng ký</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($j = 0; $j < 5; $j++): ?>
                    <tr>
                        <td>MH<?= rand(100000, 999999) ?></td>
                        <td>Phom <?= chr(65 + $j) ?></td>
                        <td><?= rand(36, 42) ?></td>
                        <td>Nhựa</td>
                        <td><?= rand(100, 300) ?></td>
                        <td><?= rand(5, 20) ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
<?php endfor; ?>

<nav aria-label="Page navigation">
  <ul class="pagination justify-content-center modern-pagination">
    <!-- Nút quay lại -->
    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
      <a class="page-link borrow-page-link" href="#" data-page="<?= max(1, $currentPage - 1) ?>">&laquo;</a>
    </li>

    <?php
    $totalPagesToShow = 5;
    $half = floor($totalPagesToShow / 2);

    $start = max(1, $currentPage - $half);
    $end = min($totalPages, $currentPage + $half);

    if ($start > 1) {
        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($page = $start; $page <= $end; $page++) {
        $activeClass = $page == $currentPage ? 'active' : '';
        echo '<li class="page-item ' . $activeClass . '">';
        echo '<a class="page-link borrow-page-link" href="#" data-page="' . $page . '">' . $page . '</a>';
        echo '</li>';
    }

    if ($end < $totalPages) {
        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
    ?>

    <!-- Nút tới -->
    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
      <a class="page-link borrow-page-link" href="#" data-page="<?= min($totalPages, $currentPage + 1) ?>">&raquo;</a>
    </li>
  </ul>
</nav>


<?php
echo ob_get_clean();
?>
