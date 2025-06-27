<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lend Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<style>
    .input-group-text {
        background-color: transparent !important;
    }

    .table .form-control {
        border: none;
        box-shadow: none;
    }

    .input-group .input-group-text {
        border-right: none;
        background-color: transparent;
    }

    .input-group .form-control {
        border-left: none;
        box-shadow: none !important;
        outline: none;
    }

    .input-group .form-control:focus {
        box-shadow: none;
        outline: none;
        border-color: #d1d7de;
    }

    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-radius: 0.375rem;
    }

    .input-group .form-select {
        border-left: none;
        box-shadow: none !important;
        outline: none;
    }

    .input-group .form-select:focus {
        box-shadow: none !important;
        outline: none;
        border-color: #d1d7de;
    }

    .table .form-control,
    .input-group .form-control,
    .input-group .form-select {
        background-color: transparent;
    }

    .table .form-control {
        border: none;
        box-shadow: none;
        color: black;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
        text-align: center !important;
    }

    .table input,
    .table select {
        text-align: center !important;
    }

    .flatpickr-day.past-date {
        opacity: 0.4;
        background: none !important;
        cursor: not-allowed;
        pointer-events: none;
        color: black !important;
    }

    .input-group.is-invalid {
        border: 1px solid #dc3545;
        border-radius: 0.375rem;
        padding: 0.25rem;
        background-color: #ffffff;
        position: relative;
    }

    .input-group.is-invalid .form-control,
    .input-group.is-invalid .form-select {
        border: none !important;
        box-shadow: none !important;
        background-color: transparent;
    }

    .input-group.is-invalid .input-group-text {
        background-color: transparent;
        border: none;
        padding-right: 0.5rem;
    }

    .input-group .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
        padding-left: 0.5rem;
    }

    /* Mã dạng phom */
    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: 12%;
    }

    /* Tên Phom */
    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 18%;
    }

    /* Loại */
    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 12%;
    }

    /* Chất liệu */
    .table th:nth-child(5),
    .table td:nth-child(5) {
        width: 12%;
    }

    /* Size */
    .table th:nth-child(6),
    .table td:nth-child(6) {
        width: 12%;
    }

    /* Tồn kho */
    .table th:nth-child(7),
    .table td:nth-child(7) {
        width: 12%;
    }

    /* Số lượng đăng ký */
    .table th:nth-child(8),
    .table td:nth-child(8) {
        width: 12%;
    }

    .input-group .add-item-code-btn,
    .input-group .remove-item-code-btn {
        border-left: 1px solid #ced4da;
        border-radius: 0 0.375rem 0.375rem 0;
    }

    .input-group .add-item-code-btn:focus,
    .input-group .remove-item-code-btn:focus {
        box-shadow: none;
    }

    .phom-table-section {
        border: 1px solid #dee2e6;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
    }

    .phom-table-section h5 {
        margin-bottom: 15px;
        color: #0d6efd;
    }
</style>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header">
                <div>
                    <?php include_once '../public/partials/header.php' ?>
                </div>
            </div>
            <div class="main-content" style="padding-top: 60px;">
                <form id="borrowForm" method="POST" action="submit_borrow.php">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Số thẻ:</strong></span>
                                    <input type="text" class="form-control" name="cardNumber" id="cardNumber">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Tên người mượn:</strong></span>
                                    <input type="text" class="form-control" name="borrowerName" id="borrowerName" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Đơn vị:</strong></span>
                                    <input type="text" class="form-control" id="unitSelect" name="unit" placeholder="" readonly>
                                    <div id="unitDropdown" class="dropdown-menu" style="display:none; max-height:200px; overflow:auto;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Số thẻ cán bộ:</strong></span>
                                    <input type="text" class="form-control" name="confirmOfficer" id="confirmOfficer">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Tên cán bộ:</strong></span>
                                    <input type="text" class="form-control" value="" name="confirmOfficerName" id="confirmOfficerName" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Ngày đăng ký mượn:</strong></span>
                                    <input type="text" class="form-control" name="borrowDate" id="borrowDate" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Ngày muốn nhận:</strong></span>
                                    <input type="text" class="form-control" name="expectedDate" id="expectedDate">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Tổng SL:</strong></span>
                                    <input type="number" class="form-control" name="totalQuantity" id="mainTotalQuantity" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="itemCodeInputsContainer" class="mt-3">
                            <div class="row mb-2 item-code-entry align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><strong>Mã dạng phom:</strong></span>
                                        <input type="text" class="form-control item-code-input" data-item-index="0" placeholder="">
                                        <button type="button" class="btn btn-outline-secondary add-item-code-btn" title="Thêm mã dạng phom">+</button>
                                        <button type="button" class="btn btn-outline-danger remove-item-code-btn" title="Xóa mã dạng phom" style="display:none;">-</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="phomTablesContainer" class="mt-4">
                            </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary px-4" id="submitBtn">Đăng ký mượn</button>
                        </div>
                </form>

            </div>


        </div>
    </div>

</body>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- 1. Lấy các phần tử DOM một lần và lưu vào biến ---
    const dom = {
        sidebar: document.getElementById('sidebar'),
        submitBtn: document.getElementById('submitBtn'),
        // Thay đổi để quản lý nhiều mã dạng phom
        itemCodeInputsContainer: document.getElementById('itemCodeInputsContainer'),
        phomTablesContainer: document.getElementById('phomTablesContainer'),
        mainTotalQuantity: document.getElementById('mainTotalQuantity'),
        cardNumber: document.getElementById('cardNumber'),
        borrowerName: document.getElementById('borrowerName'),
        unitSelect: document.getElementById('unitSelect'),
        unitDropdown: document.getElementById('unitDropdown'),
        confirmOfficer: document.getElementById('confirmOfficer'),
        confirmOfficerName: document.getElementById('confirmOfficerName'),
        borrowDate: document.querySelector('input[name="borrowDate"]'),
        expectedDate: document.querySelector('input[name="expectedDate"]'),
        loginModal: new bootstrap.Modal(document.getElementById('loginModal')),
    };

    // --- 2. Dữ liệu từ PHP và trạng thái của ứng dụng ---
    const appState = {
        allDepartments: [],
        currentUser: <?php echo isset($user) ? json_encode($user) : 'null'; ?>,
        companyName: <?= isset($_SESSION['user']['companyName']) ? json_encode($_SESSION['user']['companyName']) : 'null' ?>,
        itemCodeData: {}, // Lưu trữ dữ liệu phom theo từng mã dạng phom (key là itemIndex)
        nextItemIndex: 1, // Để tạo index duy nhất cho mỗi mã dạng phom được thêm
    };

    // --- 3. Các hàm tiện ích (Helper Functions) ---
    async function fetchAPI(url, options) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                ...options,
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error(`Fetch error for ${url}:`, error);
            alert(`Có lỗi xảy ra khi kết nối tới máy chủ. Vui lòng thử lại. Chi tiết: ${error.message}`);
            throw error; 
        }
    }
    
    function formatDate(date) {
        const dd = String(date.getDate()).padStart(2, '0');
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const yyyy = date.getFullYear();
        return `${dd}/${mm}/${yyyy}`;
    }

    function convertToDateTime(dateStr) {
        const [d, m, y] = dateStr.split('/');
        return `${y}-${m}-${d} 00:00:00`;
    }

    function showError(inputElement, isValid, message = '') {
        const inputGroup = inputElement.closest('.input-group');
        if (!inputGroup) return;

        inputGroup.classList.toggle('is-invalid', !isValid);
        let errorDiv = inputGroup.nextElementSibling;
        
        if (!isValid) {
            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                inputGroup.insertAdjacentElement('afterend', errorDiv);
            }
            errorDiv.textContent = message;
        } else {
            if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                errorDiv.remove();
            }
        }
    }

    // --- 4. Các hàm xử lý logic chính ---

    function initializeDatepickers() {
        const todayStr = formatDate(new Date());
        dom.borrowDate.value = todayStr;
        
        flatpickr(dom.expectedDate, {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            disableMobile: true,
            minDate: "today", 
        });
    }
    
    function toggleSidebar() {
        const isCollapsed = dom.sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed', isCollapsed);
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }

    function updateTotalQuantity() {
        let total = 0;
        dom.phomTablesContainer.querySelectorAll('.quantity-input').forEach(input => {
            total += (parseInt(input.value, 10) || 0);
        });
        dom.mainTotalQuantity.value = total;
        dom.submitBtn.disabled = getBorrowDetails().length === 0;
    }

    // Tạo một hàng mới cho bảng chi tiết phom
    function createPhomTableRow(item, itemIndex) {
        const fullName = item.LastName.trim();
        const splitIndex = fullName.indexOf('(');
        const maDangPhom = splitIndex !== -1 ? fullName.substring(0, splitIndex).trim() : fullName;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="d-none">${item.LastMatNo}</td>
            <td>${maDangPhom}</td>
            <td>${fullName}</td>
            <td>${item.LastType.trim()}</td>
            <td>${item.Material.trim()}</td>
            <td>${item.LastSize.trim()}</td>
            <td>${(item.SoLuongTonKho ?? '').toString().trim()}</td>
            <td><input type="number" name="quantity[${itemIndex}][]" class="form-control text-center quantity-input" value="0" min="0" data-item-index="${itemIndex}"></td>
        `;
        // Thêm event listener ngay khi tạo input
        tr.querySelector('.quantity-input').addEventListener('input', function() {
            const isPositive = parseInt(this.value, 10) > 0;
            this.style.backgroundColor = isPositive ? '#EEF594FF' : '';
            this.style.color = isPositive ? 'red' : '';
            updateTotalQuantity();
        });
        return tr;
    }
    
    // Thêm một ô input mã dạng phom mới
    const MAX_PHOM_CODES = 5;
    function addItemCodeInput() {
        const currentPhomCodeCount = dom.itemCodeInputsContainer.querySelectorAll('.item-code-entry').length;
        if (currentPhomCodeCount >= MAX_PHOM_CODES) {
            alert(`Bạn chỉ có thể thêm tối đa ${MAX_PHOM_CODES} mã dạng phom.`);
            return;
        }

        const newIndex = appState.nextItemIndex++;
        const newEntry = document.createElement('div');
        newEntry.classList.add('row', 'mb-2', 'item-code-entry', 'align-items-center');
        newEntry.innerHTML = `
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><strong>Mã dạng phom:</strong></span>
                    <input type="text" class="form-control item-code-input" data-item-index="${newIndex}" placeholder="">
                    <button type="button" class="btn btn-outline-secondary add-item-code-btn" title="Thêm mã dạng phom">+</button>
                    <button type="button" class="btn btn-outline-danger remove-item-code-btn" title="Xóa mã dạng phom">-</button>
                </div>
            </div>
        `;
        dom.itemCodeInputsContainer.appendChild(newEntry);

        updateRemoveButtonsVisibility();
    }

    // Xóa một ô input mã dạng phom và bảng liên quan
    function removeItemCodeInput(entryElement, itemIndex) {
        entryElement.remove(); 
        document.getElementById(`phom-table-section-${itemIndex}`)?.remove(); 
        delete appState.itemCodeData[itemIndex]; 
        updateTotalQuantity(); 
        updateRemoveButtonsVisibility(); 
    }

    // Cập nhật hiển thị nút xóa
    function updateRemoveButtonsVisibility() {
        const removeButtons = dom.itemCodeInputsContainer.querySelectorAll('.remove-item-code-btn');
        if (removeButtons.length > 1) {
            removeButtons.forEach(btn => btn.style.display = 'block');
        } else if (removeButtons.length === 1) {
            removeButtons[0].style.display = 'none'; 
        }
    }
    
    async function fetchPhomInfo(matNo, itemIndex) {
        const existingMatNos = new Set();
        dom.itemCodeInputsContainer.querySelectorAll('.item-code-input').forEach(input => {
            // Kiểm tra nếu mã không phải từ ô input hiện tại nhưng trùng với mã vừa nhập
            if (input.dataset.itemIndex !== String(itemIndex) && input.value.trim() === matNo) {
                existingMatNos.add(input.value.trim());
            }
        });

        if (existingMatNos.has(matNo)) {
            alert(`Mã dạng phom "${matNo}" đã được nhập. Vui lòng nhập mã khác.`);
            // Xóa giá trị trong ô input hiện tại
            const currentInput = dom.itemCodeInputsContainer.querySelector(`input[data-item-index="${itemIndex}"]`);
            if (currentInput) {
                currentInput.value = '';
            }
            // Xóa bảng chi tiết nếu nó đã tồn tại từ lần nhập trước
            document.getElementById(`phom-table-section-${itemIndex}`)?.remove();
            delete appState.itemCodeData[itemIndex];
            updateTotalQuantity();
            return; // Dừng hàm nếu mã bị trùng
        }


        const payload = { LastMatNo: matNo };
        const data = await fetchAPI('fetch_phom_info.php', { body: JSON.stringify(payload) });

        let phomTableSection = document.getElementById(`phom-table-section-${itemIndex}`);
        if (!phomTableSection) {
            phomTableSection = document.createElement('div');
            phomTableSection.id = `phom-table-section-${itemIndex}`;
            phomTableSection.classList.add('phom-table-section');
            dom.phomTablesContainer.appendChild(phomTableSection);
        }
        
        let tableBody = phomTableSection.querySelector('tbody');
        if (!tableBody) {
            // Nếu chưa có bảng, tạo cấu trúc bảng mới
            phomTableSection.innerHTML = `
                <h5>Chi tiết mã dạng phom: <strong>${matNo}</strong></h5>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead style="background-color: #bde0f6;">
                            <tr>
                                <th class="d-none">Mã vật tư</th>
                                <th>Mã dạng phom</th>
                                <th>Tên Phom</th>
                                <th>Loại</th>
                                <th>Chất liệu</th>
                                <th>Size</th>
                                <th>Tồn kho</th>
                                <th>Số lượng đăng ký</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            `;
            tableBody = phomTableSection.querySelector('tbody');
        } else {
            tableBody.innerHTML = ''; 
        }

        if (data.status === 'Success' && data.data?.jsonArray?.length > 0) {
            const sortedData = data.data.jsonArray.sort((a, b) => 
                a.LastSize.trim().localeCompare(b.LastSize.trim(), undefined, { numeric: true })
            );
            
            appState.itemCodeData[itemIndex] = sortedData; 

            sortedData.forEach(item => {
                const row = createPhomTableRow(item, itemIndex);
                tableBody.appendChild(row);
            });
        } else {
            alert(data.message || `Không tìm thấy dữ liệu phom cho mã: ${matNo}.`);
            phomTableSection.remove();
            delete appState.itemCodeData[itemIndex]; 
            const currentInput = dom.itemCodeInputsContainer.querySelector(`input[data-item-index="${itemIndex}"]`);
            if (currentInput) {
                currentInput.value = '';
            }
        }
        updateTotalQuantity(); // Cập nhật tổng số lượng sau khi fetch dữ liệu mới
    }

    async function updateUserInfo(inputId, nameId, unitId = null) {
        const id = inputId.value.trim();
        nameId.value = '';
        nameId.disabled = false;
        if (unitId) unitId.disabled = false;

        if (!id) return;

        const payload = { userID: id, companyName: appState.companyName };
        const data = await fetchAPI('get_user_info.php', { body: JSON.stringify(payload) });

        if (data.status === 200 && data.data?.USERNAME) {
            nameId.value = data.data.USERNAME;
            nameId.disabled = true;

            if (unitId) {
                const userDepName = data.data.DEPID.trim();
                const matchedDep = appState.allDepartments.find(dep => dep.DepName.trim() === userDepName);
                if (matchedDep) {
                    unitId.value = matchedDep.DepName;
                    unitId.dataset.id = matchedDep.ID;
                    unitId.disabled = true;
                } else {
                    unitId.value = '';
                    alert('Không tìm thấy đơn vị của người dùng.');
                }
            }
        } else {
            alert(data.message || `Không tìm thấy người dùng với mã: ${id}`);
        }
    }

    function getBorrowDetails() {
        const details = [];
        // Duyệt qua tất cả các hàng trong tất cả các bảng chi tiết
        dom.phomTablesContainer.querySelectorAll('.phom-table-section tbody tr').forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const quantity = parseInt(quantityInput?.value || 0, 10);

            if (quantity > 0) {
                const cells = row.cells;
                details.push({
                    LastMatNo: cells[0]?.textContent.trim(),
                    // LastName: cells[2]?.textContent.trim(), // Để lại tên đầy đủ
                    LastName: cells[1]?.textContent.trim(), // Lấy mã dạng phom từ cột thứ 2 (đã hiển thị)
                    LastSize: cells[5]?.textContent.trim(),
                    LastSum: quantity,
                });
            }
        });
        return details;
    }

    function validateForm() {
        let isValid = true;
        const checks = {
            cardNumber: /^\d{5}$/.test(dom.cardNumber.value),
            borrowerName: dom.borrowerName.value.trim() !== '',
            unitSelect: appState.allDepartments.some(dep => dep.DepName === dom.unitSelect.value.trim()),
            expectedDate: dom.expectedDate.value.trim() !== '',
            confirmOfficer: dom.confirmOfficer.value.trim() !== '',
            confirmOfficerName: dom.confirmOfficerName.value.trim() !== ''
        };

        const messages = {
            cardNumber: 'Số thẻ phải là 5 chữ số.',
            borrowerName: 'Tên người mượn không được để trống.',
            unitSelect: 'Đơn vị không hợp lệ.',
            expectedDate: 'Vui lòng chọn ngày muốn nhận.',
            confirmOfficer: 'Vui lòng nhập mã cán bộ xác nhận.',
            confirmOfficerName: 'Tên cán bộ xác nhận không hợp lệ.'
        };

        for (const [id, condition] of Object.entries(checks)) {
            showError(dom[id], condition, messages[id]);
            if (!condition) isValid = false;
        }
        
        // Kiểm tra xem có ít nhất một mã dạng phom được nhập và có dữ liệu không
        const itemCodeInputs = dom.itemCodeInputsContainer.querySelectorAll('.item-code-input');
        let hasValidItemCode = false;
        if (itemCodeInputs.length > 0) {
            // Kiểm tra từng input, không chỉ input đầu tiên
            let anyInputFilled = false;
            itemCodeInputs.forEach(input => {
                const itemIndex = input.dataset.itemIndex;
                if (input.value.trim() !== '' && appState.itemCodeData[itemIndex] && appState.itemCodeData[itemIndex].length > 0) {
                    anyInputFilled = true;
                    // Xóa lỗi nếu input này hợp lệ
                    showError(input, true); 
                } else if (input.value.trim() === '') {
                    // Nếu input trống, không đánh dấu là lỗi ngay, chỉ cần biết có ít nhất một cái được điền
                    showError(input, true); 
                } else {
                    // Nếu input có giá trị nhưng không tìm thấy dữ liệu
                    showError(input, false, `Không tìm thấy dữ liệu cho mã: ${input.value.trim()}`);
                }
            });
            hasValidItemCode = anyInputFilled;

            if (!hasValidItemCode) {
                // Nếu không có bất kỳ mã nào hợp lệ, hiển thị lỗi chung cho input đầu tiên
                showError(itemCodeInputs[0], false, 'Vui lòng nhập ít nhất một mã dạng phom hợp lệ và đã có dữ liệu.');
                isValid = false;
            }
        } else {
            isValid = false; 
        }


        // Kiểm tra ngày đặc biệt
        if (checks.expectedDate) {
            const borrowDate = new Date(convertToDateTime(dom.borrowDate.value).split(' ')[0]);
            const expectedDate = new Date(convertToDateTime(dom.expectedDate.value).split(' ')[0]);
            if (expectedDate < borrowDate) {
                showError(dom.expectedDate, false, 'Ngày nhận phải sau hoặc bằng ngày mượn.');
                isValid = false;
            }
        }

        // Kiểm tra tổng số lượng đăng ký
        if (parseInt(dom.mainTotalQuantity.value, 10) <= 0) {
            alert('Vui lòng nhập số lượng đăng ký lớn hơn 0 cho ít nhất một mặt hàng.');
            isValid = false;
        }

        return isValid;
    }

    async function handleFormSubmit() {
        if (!appState.currentUser) {
            alert('Vui lòng đăng nhập để thực hiện chức năng này!');
            dom.loginModal.show();
            return;
        }

        if (!validateForm()) {
            return; // Dừng lại nếu form không hợp lệ
        }

        const details = getBorrowDetails();
        if (details.length === 0) {
            alert("Vui lòng nhập số lượng cho ít nhất một size.");
            return;
        }
        
        // Lấy LastMatNo từ chi tiết đầu tiên nếu cần cho payload tổng thể
        const firstDetailMatNo = details.length > 0 ? details[0].LastMatNo : ''; 

        const payload = {
            UserID: dom.cardNumber.value.trim(),
            UserName: dom.borrowerName.value.trim(),
            DepID: dom.unitSelect.dataset.id,
            LastMatNo: firstDetailMatNo, 
            DateBorrow: convertToDateTime(dom.borrowDate.value),
            DateReceive: convertToDateTime(dom.expectedDate.value),
            OfficerId: dom.confirmOfficer.value.trim(),
            OfficerName: dom.confirmOfficerName.value.trim(),
            Details: details, // Đây là mảng chứa tất cả các chi tiết từ các bảng khác nhau
        };
        
        const data = await fetchAPI('submit_borrow.php', { body: JSON.stringify(payload) });
        
        if (data.status === 'Success') {
            alert("Bạn đã đăng ký mượn thành công!");
            location.reload();
        } else {
            alert(data.message || "Có lỗi xảy ra khi gửi dữ liệu.");
        }
    }


    // --- 5. Gắn các Event Listeners (Sử dụng Event Delegation) ---
    // Gắn sự kiện cho container cha và xử lý các sự kiện của các nút con
    dom.itemCodeInputsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-item-code-btn')) {
            addItemCodeInput();
        } else if (e.target.classList.contains('remove-item-code-btn')) {
            const entryElement = e.target.closest('.item-code-entry');
            const itemIndex = entryElement.querySelector('.item-code-input').dataset.itemIndex;
            removeItemCodeInput(entryElement, itemIndex);
        }
    });

    dom.itemCodeInputsContainer.addEventListener('blur', (e) => {
        if (e.target.classList.contains('item-code-input')) {
            const matNo = e.target.value.trim();
            const itemIndex = e.target.dataset.itemIndex;
            if (matNo) fetchPhomInfo(matNo, itemIndex);
        }
    }, true); // Use capture phase for blur event

    dom.cardNumber.addEventListener('blur', () => updateUserInfo(dom.cardNumber, dom.borrowerName, dom.unitSelect));
    dom.confirmOfficer.addEventListener('blur', () => updateUserInfo(dom.confirmOfficer, dom.confirmOfficerName));
    
    dom.submitBtn.addEventListener('click', handleFormSubmit);

    // Xử lý dropdown cho đơn vị
    dom.unitSelect.addEventListener('input', () => {
        const keyword = dom.unitSelect.value.toLowerCase();
        const filtered = appState.allDepartments.filter(dep => 
            `${dep.DepName} (${dep.ID})`.toLowerCase().includes(keyword)
        ).slice(0, 10);
        
        dom.unitDropdown.innerHTML = '';
        dom.unitDropdown.style.display = (filtered.length === 0 || keyword === '') ? 'none' : 'block';

        filtered.forEach(dep => {
            const div = document.createElement('div');
            div.className = 'dropdown-item';
            div.textContent = dep.DepName;
            div.addEventListener('click', () => {
                dom.unitSelect.value = dep.DepName;
                dom.unitSelect.dataset.id = dep.ID;
                dom.unitDropdown.style.display = 'none';
            });
            dom.unitDropdown.appendChild(div);
        });
    });
    
    document.addEventListener('click', (e) => {
        // Đóng dropdown nếu click ra ngoài
        if (!dom.unitSelect.contains(e.target) && !dom.unitDropdown.contains(e.target)) {
            dom.unitDropdown.style.display = 'none';
        }
    });


    // --- 6. Khởi tạo ---
    async function initializeApp() {
        initializeDatepickers();
        updateTotalQuantity(); 

        // Cập nhật trạng thái nút xóa ban đầu
        updateRemoveButtonsVisibility();
        
        try {
            const data = await fetchAPI('fetch_departments.php');
            if (data.status === 'Success' && data.data?.jsonArray) {
                appState.allDepartments = data.data.jsonArray;
            } else {
                console.error("Không tải được danh sách đơn vị.");
            }
        } catch (error) {
            console.error("Lỗi khi tải danh sách đơn vị:", error);
        }
    }

    initializeApp();
});
</script>
</html>