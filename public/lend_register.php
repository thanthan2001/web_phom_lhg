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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
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

    /* CSS cho lỗi trực quan hơn */
    .input-group.is-invalid {
        border: 1px solid #dc3545;
        border-radius: 0.375rem;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    .input-group.is-invalid .form-control,
    .input-group.is-invalid .form-select {
        border: none !important;
    }

    .input-group .invalid-feedback {
        display: none;
        /* Ẩn mặc định */
    }

    /* Hiển thị lỗi bên dưới input group */
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: .875em;
        color: #dc3545;
    }

    .input-group.is-invalid+.invalid-feedback {
        display: block;
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
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Tên người mượn:</strong></span>
                                    <input type="text" class="form-control" name="borrowerName" id="borrowerName"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Đơn vị:</strong></span>
                                    <input type="text" class="form-control" id="unitSelect" name="unit" placeholder=""
                                        readonly>
                                    <div id="unitDropdown" class="dropdown-menu"
                                        style="display:none; max-height:200px; overflow:auto;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Số thẻ cán bộ:</strong></span>
                                    <input type="text" class="form-control" name="confirmOfficer" id="confirmOfficer">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Tên cán bộ:</strong></span>
                                    <input type="text" class="form-control" value="" name="confirmOfficerName"
                                        id="confirmOfficerName" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Đơn vị cán bộ:</strong></span>
                                    <input type="text" class="form-control" id="unitOfficerSelect" name="unitOfficer"
                                        placeholder="" readonly>
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
                                    <input type="number" class="form-control" name="totalQuantity"
                                        id="mainTotalQuantity" readonly>
                                </div>
                            </div>
                        </div>

                        <div id="itemCodeInputsContainer" class="mt-3">
                            <div class="row mb-2 item-code-entry align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><strong>Mã dạng phom:</strong></span>
                                        <input type="text" class="form-control item-code-input" data-item-index="0"
                                            placeholder="">
                                        <button type="button" class="btn btn-outline-secondary add-item-code-btn"
                                            title="Thêm mã dạng phom">+</button>
                                        <button type="button" class="btn btn-outline-danger remove-item-code-btn"
                                            title="Xóa mã dạng phom" style="display:none;">-</button>
                                    </div>
                                    <div class="invalid-feedback"></div>
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
        // --- 1. DOM Elements ---
        const dom = {
            sidebar: document.getElementById('sidebar'),
            submitBtn: document.getElementById('submitBtn'),
            itemCodeInputsContainer: document.getElementById('itemCodeInputsContainer'),
            phomTablesContainer: document.getElementById('phomTablesContainer'),
            mainTotalQuantity: document.getElementById('mainTotalQuantity'),
            cardNumber: document.getElementById('cardNumber'),
            borrowerName: document.getElementById('borrowerName'),
            unitSelect: document.getElementById('unitSelect'),
            unitDropdown: document.getElementById('unitDropdown'),
            confirmOfficer: document.getElementById('confirmOfficer'),
            confirmOfficerName: document.getElementById('confirmOfficerName'),
            unitOfficerSelect: document.getElementById('unitOfficerSelect'),
            borrowDate: document.querySelector('input[name="borrowDate"]'),
            expectedDate: document.querySelector('input[name="expectedDate"]'),
            loginModal: new bootstrap.Modal(document.getElementById('loginModal')),
        };

        // --- 2. Application State ---
        const appState = {
            // *** CHANGE: Removed allDepartments as it's no longer needed for validation ***
            currentUser: <?php echo isset($user) ? json_encode($user) : 'null'; ?>,
            companyName: <?= isset($_SESSION['user']['companyName']) ? json_encode($_SESSION['user']['companyName']) : 'null' ?>,
            itemCodeData: {},
            nextItemIndex: 1,
        };

        // --- 3. Helper Functions ---
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
                // Don't alert here to avoid loops
                return { status: 'Error', message: `Lỗi kết nối: ${error.message}` };
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

            const errorDiv = inputGroup.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                console.error('Could not find invalid-feedback div for', inputElement);
                return;
            }

            inputGroup.classList.toggle('is-invalid', !isValid);
            errorDiv.textContent = message;
            errorDiv.style.display = isValid ? 'none' : 'block';
        }


        // --- 4. Core Logic Functions ---

        function initializeDatepickers() {
            dom.borrowDate.value = formatDate(new Date());
            flatpickr(dom.expectedDate, {
                dateFormat: "d/m/Y",
                defaultDate: "today",
                minDate: "today",
            });
        }

        function updateTotalQuantity() {
            let total = 0;
            dom.phomTablesContainer.querySelectorAll('.quantity-input').forEach(input => {
                total += (parseInt(input.value, 10) || 0);
            });
            dom.mainTotalQuantity.value = total;
            dom.submitBtn.disabled = getBorrowDetails().length === 0;
        }

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
            <td><input type="number" class="form-control text-center quantity-input" value="0" min="0" data-item-index="${itemIndex}"></td>
        `;
            tr.querySelector('.quantity-input').addEventListener('input', function () {
                const isPositive = parseInt(this.value, 10) > 0;
                this.style.backgroundColor = isPositive ? '#EEF594FF' : '';
                this.style.color = isPositive ? 'red' : '';
                updateTotalQuantity();
            });
            return tr;
        }

        const MAX_PHOM_CODES = 5;
        function addItemCodeInput() {
            if (dom.itemCodeInputsContainer.querySelectorAll('.item-code-entry').length >= MAX_PHOM_CODES) {
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
                <div class="invalid-feedback"></div>
            </div>
        `;
            dom.itemCodeInputsContainer.appendChild(newEntry);
            updateRemoveButtonsVisibility();
        }

        function removeItemCodeInput(entryElement, itemIndex) {
            entryElement.remove();
            document.getElementById(`phom-table-section-${itemIndex}`)?.remove();
            delete appState.itemCodeData[itemIndex];
            updateTotalQuantity();
            updateRemoveButtonsVisibility();
        }

        function updateRemoveButtonsVisibility() {
            const removeButtons = dom.itemCodeInputsContainer.querySelectorAll('.remove-item-code-btn');
            removeButtons.forEach(btn => btn.style.display = (removeButtons.length > 1) ? 'inline-block' : 'none');
        }

        async function fetchPhomInfo(matNo, itemIndex) {
            const currentInput = dom.itemCodeInputsContainer.querySelector(`input[data-item-index="${itemIndex}"]`);

            const existingMatNos = [...dom.itemCodeInputsContainer.querySelectorAll('.item-code-input')]
                .filter(input => input !== currentInput)
                .map(input => input.value.trim());

            if (existingMatNos.includes(matNo)) {
                showError(currentInput, false, `Mã "${matNo}" đã được nhập.`);
                document.getElementById(`phom-table-section-${itemIndex}`)?.remove();
                delete appState.itemCodeData[itemIndex];
                updateTotalQuantity();
                return;
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

            if (data.status === 'Success' && data.data?.jsonArray?.length > 0) {
                showError(currentInput, true);
                phomTableSection.innerHTML = `
                <h5>Chi tiết mã dạng phom: <strong>${matNo}</strong></h5>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead style="background-color: #bde0f6;">
                            <tr>
                                <th class="d-none">Mã vật tư</th><th>Mã dạng phom</th><th>Tên Phom</th><th>Loại</th><th>Chất liệu</th><th>Size</th><th>Tồn kho</th><th>Số lượng đăng ký</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            `;
                const tableBody = phomTableSection.querySelector('tbody');
                const sortedData = data.data.jsonArray.sort((a, b) => a.LastSize.trim().localeCompare(b.LastSize.trim(), undefined, { numeric: true }));
                appState.itemCodeData[itemIndex] = sortedData;
                sortedData.forEach(item => tableBody.appendChild(createPhomTableRow(item, itemIndex)));
            } else {
                showError(currentInput, false, data.message || `Không tìm thấy dữ liệu cho mã: ${matNo}.`);
                phomTableSection.remove();
                delete appState.itemCodeData[itemIndex];
            }
            updateTotalQuantity();
        }

        // *** CHANGE: Updated function to store Department ID ***
        async function updateOfficerInfo(inputId, nameId, unitId) {
            const id = inputId.value.trim();
            // Clear previous data
            nameId.value = '';
            unitId.value = '';
            unitId.dataset.id = ''; // Clear the stored ID
            showError(inputId, true); // Reset error state

            if (!id) return;

            const payload = { userID: id, companyName: appState.companyName };
            const data = await fetchAPI('get_officer_info.php', { body: JSON.stringify(payload) });

            if (data.statusCode === 200 && data.data?.PERSON_NAME) {
                inputId.value = data.data.PERSON_ID;
                nameId.value = data.data.PERSON_NAME;
                unitId.value = data.data.DEPARTMENT_NAME ? data.data.DEPARTMENT_NAME.trim() : '';
                // Here is the key change: store the DEPARTMENT_ID in the element's dataset
                unitId.dataset.id = data.data.DEPARTMENT_ID || '';
            } else {
                showError(inputId, false, data.message || `Không tìm thấy cán bộ với thông tin: ${id}`);
            }
        }

        function getBorrowDetails() {
            const details = [];
            dom.phomTablesContainer.querySelectorAll('.phom-table-section tbody tr').forEach(row => {
                const quantity = parseInt(row.querySelector('.quantity-input')?.value || 0, 10);
                if (quantity > 0) {
                    details.push({
                        LastMatNo: row.cells[0]?.textContent.trim(),
                        LastName: row.cells[2]?.textContent.trim(),
                        LastSize: row.cells[5]?.textContent.trim(),
                        LastSum: quantity,
                    });
                }
            });
            return details;
        }

        function validateForm() {
            let isValid = true;
            // *** CHANGE: Updated validation to use the stored department ID ***
            const checks = {
                cardNumber: /^\d{5}$/.test(dom.cardNumber.value),
                borrowerName: dom.borrowerName.value.trim() !== '',
                unitSelect: dom.unitSelect.dataset.id && dom.unitSelect.dataset.id.trim() !== '',
                expectedDate: dom.expectedDate.value.trim() !== '',
                confirmOfficer: dom.confirmOfficer.value.trim() !== '',
                confirmOfficerName: dom.confirmOfficerName.value.trim() !== ''
            };

            const messages = {
                cardNumber: 'Số thẻ phải là 5 chữ số.',
                borrowerName: 'Tên người mượn không được để trống.',
                unitSelect: 'Đơn vị không hợp lệ. Vui lòng kiểm tra lại số thẻ người mượn.',
                expectedDate: 'Vui lòng chọn ngày muốn nhận.',
                confirmOfficer: 'Vui lòng nhập mã cán bộ xác nhận.',
                confirmOfficerName: 'Tên cán bộ xác nhận không hợp lệ.'
            };

            for (const [id, condition] of Object.entries(checks)) {
                showError(dom[id], condition, messages[id]);
                if (!condition) isValid = false;
            }

            const itemCodeInputs = dom.itemCodeInputsContainer.querySelectorAll('.item-code-input');
            let hasValidItemCode = false;
            if (itemCodeInputs.length > 0) {
                let anyInputFilled = false;
                itemCodeInputs.forEach(input => {
                    const itemIndex = input.dataset.itemIndex;
                    if (input.value.trim() !== '' && appState.itemCodeData[itemIndex] && appState.itemCodeData[itemIndex].length > 0) {
                        anyInputFilled = true;
                        showError(input, true);
                    } else if (input.value.trim() === '') {
                        showError(input, true);
                    } else {
                        showError(input, false, `Không tìm thấy dữ liệu cho mã: ${input.value.trim()}`);
                    }
                });
                hasValidItemCode = anyInputFilled;

                if (!hasValidItemCode) {
                    showError(itemCodeInputs[0], false, 'Vui lòng nhập ít nhất một mã dạng phom hợp lệ và đã có dữ liệu.');
                    isValid = false;
                }
            } else {
                isValid = false;
            }

            if (checks.expectedDate) {
                const borrowDate = new Date(convertToDateTime(dom.borrowDate.value).split(' ')[0]);
                const expectedDate = new Date(convertToDateTime(dom.expectedDate.value).split(' ')[0]);
                if (expectedDate < borrowDate) {
                    showError(dom.expectedDate, false, 'Ngày nhận phải sau hoặc bằng ngày mượn.');
                    isValid = false;
                }
            }

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
                return;
            }

            const details = getBorrowDetails();
            if (details.length === 0) {
                alert("Vui lòng nhập số lượng cho ít nhất một size.");
                return;
            }

            const firstDetailMatNo = details.length > 0 ? details[0].LastMatNo : '';

            const payload = {
                UserID: dom.cardNumber.value.trim(),
                UserName: dom.borrowerName.value.trim(),
                DepID: dom.unitSelect.dataset.id, // This part was already correct and now works with the new logic
                LastMatNo: firstDetailMatNo,
                DateBorrow: convertToDateTime(dom.borrowDate.value),
                DateReceive: convertToDateTime(dom.expectedDate.value),
                OfficerId: dom.confirmOfficer.value.trim(),
                OfficerName: dom.confirmOfficerName.value.trim(),
                Details: details,
            };

            const data = await fetchAPI('submit_borrow.php', { body: JSON.stringify(payload) });

            if (data.status === 'Success') {
                alert("Bạn đã đăng ký mượn thành công!");
                location.reload();
            } else {
                alert(data.message || "Có lỗi xảy ra khi gửi dữ liệu.");
            }
        }

        dom.itemCodeInputsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-item-code-btn')) addItemCodeInput();
            if (e.target.classList.contains('remove-item-code-btn')) {
                const entryElement = e.target.closest('.item-code-entry');
                removeItemCodeInput(entryElement, entryElement.querySelector('.item-code-input').dataset.itemIndex);
            }
        });

        dom.itemCodeInputsContainer.addEventListener('blur', (e) => {
            if (e.target.classList.contains('item-code-input')) {
                const matNo = e.target.value.trim();
                if (matNo) fetchPhomInfo(matNo, e.target.dataset.itemIndex);
            }
        }, true);

        dom.cardNumber.addEventListener('blur', () => updateOfficerInfo(dom.cardNumber, dom.borrowerName, dom.unitSelect));
        dom.confirmOfficer.addEventListener('blur', () => updateOfficerInfo(dom.confirmOfficer, dom.confirmOfficerName, dom.unitOfficerSelect));

        dom.submitBtn.addEventListener('click', handleFormSubmit);

        // *** CHANGE: Simplified initialization ***
        function initializeApp() {
            initializeDatepickers();
            updateTotalQuantity();
            updateRemoveButtonsVisibility();
            // The fetch_departments call has been removed.
        }

        initializeApp();
    });
</script>

</html>