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
        background-color: transparent !important;
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
                        <!-- Dòng 1 -->
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
                                    <input type="text" class="form-control" name="borrowerName" id="borrowerName">
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

                        <!-- Dòng 2 -->
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

                        <!-- Dòng 3 -->
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
                        </div>

                        <!-- Dòng 4 -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Mã dạng phom:</strong></span>
                                    <input type="text" class="form-control" name="itemCode" id="mainItemCode">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><strong>Tổng SL:</strong></span>
                                    <input type="number" class="form-control" name="totalQuantity" id="mainTotalQuantity" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Bảng -->
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered text-center align-middle">
                                <thead style="background-color: #bde0f6;">
                                    <tr>
                                        <th class="d-none">Mã vật tư</th>
                                        <th>Mã dạng phom</th>
                                        <th>Size</th>
                                        <th>Loại</th>
                                        <th>Chất liệu</th>
                                        <th>Tồn kho</th>
                                        <th>Số lượng đăng ký</th>
                                    </tr>
                                </thead>
                                <tbody id="phomTableBody">
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">Đăng ký mượn</button>
                        </div>
                </form>

            </div>


        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function toggleSubmitButton() {
            const details = getBorrowDetails();
            document.getElementById('submitBtn').disabled = details.length === 0;
        }

        window.addEventListener('DOMContentLoaded', (event) => {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${yyyy}-${mm}-${dd}`;

            document.querySelector('input[name="borrowDate"]').value = formattedDate;
            document.querySelector('input[name="expectedDate"]').value = formattedDate;
        });

        window.addEventListener('DOMContentLoaded', (event) => {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${dd}/${mm}/${yyyy}`;

            document.querySelector('input[name="borrowDate"]').value = formattedDate;
            document.querySelector('input[name="expectedDate"]').value = formattedDate;

            flatpickr("input[name='expectedDate']", {
                dateFormat: "d/m/Y",
                defaultDate: "today",
                disableMobile: true,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const date = dayElem.dateObj;

                    if (date < today) {
                        dayElem.classList.add("flatpickr-disabled", "past-date");
                        dayElem.removeAttribute("tabindex");
                    }
                }
            });
        });


        document.getElementById('mainItemCode').addEventListener('blur', function(e) {
            const matNo = e.target.value.trim();
            if (!matNo) return;

            fetch('fetch_phom_info.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        LastMatNo: matNo
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'Success' && data.data?.jsonArray?.length > 0) {
                        const tbody = document.getElementById('phomTableBody');
                        tbody.innerHTML = '';

                        const sorted = data.data.jsonArray.sort((a, b) => {
                            const sizeA = a.LastSize.trim().toUpperCase();
                            const sizeB = b.LastSize.trim().toUpperCase();
                            return sizeA.localeCompare(sizeB, undefined, {
                                numeric: true
                            });
                        });

                        sorted.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                    <td class="d-none">${item.LastMatNo}</td>
                    <td>${item.LastName.trim()}</td>
                    <td>${item.LastSize.trim()}</td>
                    <td>${item.LastType.trim()}</td>
                    <td>${item.Material.trim()}</td>
                    <td>${(item.SoLuongTonKho ?? '').toString().trim()}</td>
                    <td><input type="number" name="quantity[]" class="form-control text-center quantity-input" value="0" min="0"></td>
                `;
                            tbody.appendChild(tr);
                        });

                        updateTotalQuantity();
                    } else {
                        alert(data.message || "Không tìm thấy dữ liệu phom.");
                        document.getElementById('phomTableBody').innerHTML = '';
                        document.getElementById('mainTotalQuantity').value = 0;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Có lỗi xảy ra khi gọi API.");
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const cardNumberInput = document.getElementById('cardNumber');
            const borrowerNameInput = document.getElementById('borrowerName');

            cardNumberInput.addEventListener('blur', function() {
                const userID = cardNumberInput.value.trim();
                const companyName = <?= json_encode($_SESSION['user']['companyName']) ?>;

                if (!userID) {
                    borrowerNameInput.value = '';
                    borrowerNameInput.disabled = false;
                    return;
                }

                fetch('get_user_info.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            userID,
                            companyName
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 200 && data.data?.USERNAME) {
                            borrowerNameInput.value = data.data.USERNAME;
                            borrowerNameInput.disabled = true;


                            const userDepName = data.data.DEPID;
                            const matchedDep = allDepartments.find(dep => dep.DepName.trim() === userDepName.trim());
                            const unitInput = document.getElementById('unitSelect');
                            console.log("userDepID:", userDepName);

                            if (matchedDep) {
                                unitInput.value = matchedDep.DepName;
                                unitInput.dataset.id = matchedDep.ID;
                                unitInput.disabled = true;
                            } else {
                                unitInput.value = '';
                                unitInput.disabled = false;
                                alert('Không tìm thấy đơn vị của người dùng.');
                            }

                        } else {
                            borrowerNameInput.value = '';
                            borrowerNameInput.disabled = false;
                            alert(data.message || 'Không tìm thấy người dùng.');
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi lấy thông tin người dùng:', error);
                        alert('Đã xảy ra lỗi khi lấy thông tin người dùng.');
                    });
            });
        });

        const confirmOfficerInput = document.getElementById('confirmOfficer');
        const confirmOfficerNameInput = document.getElementById('confirmOfficerName');

        confirmOfficerInput.addEventListener('blur', function() {
            const officerID = confirmOfficerInput.value.trim();
            const companyName = <?= json_encode($_SESSION['user']['companyName']) ?>;

            if (!officerID) {
                confirmOfficerNameInput.value = '';
                confirmOfficerNameInput.disabled = false;
                return;
            }

            fetch('get_user_info.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userID: officerID,
                        companyName
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 200 && data.data?.USERNAME) {
                        confirmOfficerNameInput.value = data.data.USERNAME.toString();
                        console.log("Officer Name:", data.data.USERNAME);
                        confirmOfficerNameInput.disabled = true;
                    } else {
                        confirmOfficerNameInput.value = '';
                        confirmOfficerNameInput.disabled = false;
                        alert(data.message || 'Không tìm thấy cán bộ.');
                    }
                })
                .catch(error => {
                    console.error('Lỗi lấy thông tin cán bộ:', error);
                    alert('Đã xảy ra lỗi khi lấy thông tin cán bộ.');
                });
        });


        function updateTotalQuantity() {
            const inputs = document.querySelectorAll('.quantity-input');
            const total = Array.from(inputs).reduce((sum, input) => {
                return sum + (parseInt(input.value) || 0);
            }, 0);
            document.getElementById('mainTotalQuantity').value = total;
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                updateTotalQuantity();
            }
        });

        function showError(id, isValid, message) {
            const input = document.getElementById(id);
            if (!input) return;

            const inputGroup = input.closest('.input-group');
            let errorDiv = inputGroup.nextElementSibling;

            // Tạo lỗi nếu chưa có
            if ((!errorDiv || !errorDiv.classList.contains('invalid-feedback')) && !isValid) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                inputGroup.insertAdjacentElement('afterend', errorDiv);
            }

            // Hiển thị hoặc ẩn lỗi
            if (!isValid) {
                inputGroup.classList.add('is-invalid');
                if (errorDiv) errorDiv.textContent = message;
            } else {
                inputGroup.classList.remove('is-invalid');
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            }
        }


        let allDepartments = [];

        document.addEventListener('DOMContentLoaded', () => {
            fetch('fetch_departments.php', {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'Success' && data.data?.jsonArray?.length > 0) {
                        allDepartments = data.data.jsonArray;
                    } else {
                        alert("Không thể tải danh sách đơn vị.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Lỗi khi tải danh sách đơn vị.");
                });

            const input = document.getElementById('unitSelect');
            const dropdown = document.getElementById('unitDropdown');

            input.addEventListener('input', () => {
                const keyword = input.value.toLowerCase();
                const filtered = allDepartments.filter(dep =>
                    `${dep.DepName} (${dep.ID})`.toLowerCase().includes(keyword)
                ).slice(0, 10); // hiển thị tối đa 10 dòng

                dropdown.innerHTML = '';
                if (filtered.length === 0 || keyword === '') {
                    dropdown.style.display = 'none';
                    return;
                }

                filtered.forEach(dep => {
                    const div = document.createElement('div');
                    div.className = 'dropdown-item';
                    div.textContent = `${dep.DepName}`;
                    div.dataset.id = dep.ID;
                    div.addEventListener('click', () => {
                        input.value = dep.DepName;
                        input.dataset.id = dep.ID;
                        dropdown.style.display = 'none';
                    });
                    dropdown.appendChild(div);
                });

                dropdown.style.display = 'block';
            });

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        });


        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('borrowForm').addEventListener('submit', handleFormSubmit);
        });

        function handleFormSubmit(e) {
            e.preventDefault();
            console.log("Submit event triggered.");

            const formData = getFormData();
            console.log("Form data:", formData);
            const validation = validateForm(formData);
            console.log("Validation result:", validation);

            if (!validation.isValid) return;

            const details = getBorrowDetails();
            if (details.length === 0) {
                alert("Vui lòng nhập ít nhất một dòng có số lượng mượn.");
                return;
            }

            const payload = {
                UserID: formData.cardNumber,
                UserName: formData.borrowerName,
                DepID: formData.depID,
                LastMatNo: formData.matNo,
                DateBorrow: convertToDateTime(formData.borrowDate),
                DateReceive: convertToDateTime(formData.expectedDate),
                OfficerId: formData.confirmOfficer,
                OfficerName: confirmOfficerNameInput.value.trim(),
                Details: details
            };

            submitData(payload);
        }

        function getFormData() {
            const cardNumber = document.getElementById('cardNumber').value.trim();
            const borrowerName = document.getElementById('borrowerName').value.trim();
            const unitRaw = document.getElementById('unitSelect').value.trim();
            const borrowDate = document.getElementById('borrowDate').value.trim();
            const expectedDate = document.getElementById('expectedDate').value.trim();
            // const matNo = document.getElementById('mainItemCode').value.trim();
            const depID = document.getElementById('unitSelect').dataset.id || '';
            const confirmOfficer = document.getElementById('confirmOfficer').value.trim();
            const confirmOfficerName = document.getElementById('confirmOfficerName').value.trim();
            const firstRow = document.querySelector('#phomTableBody tr');
            const matNo = firstRow ? firstRow.cells[0].textContent.trim() : '';

            return {
                cardNumber,
                borrowerName,
                unitRaw,
                borrowDate,
                expectedDate,
                matNo,
                depID,
                confirmOfficer,
                confirmOfficerName
            };
        }

        function validateForm({
            cardNumber,
            borrowerName,
            unitRaw,
            borrowDate,
            expectedDate,
            matNo,
            confirmOfficer,
        }) {
            let isValid = true;

            if (!cardNumber || !/^\d{5}$/.test(cardNumber)) {
                showError('cardNumber', false, 'Số thẻ phải hợp lệ!');
                isValid = false;
            } else {
                showError('cardNumber', true);
            }

            if (!borrowerName) {
                showError('borrowerName', false, 'Tên người mượn phải hợp lệ.');
                isValid = false;
            } else {
                showError('borrowerName', true);
            }

            const matchedDepartment = allDepartments.find(dep => dep.DepName === unitRaw);

            if (!matchedDepartment) {
                showError('unitSelect', false, 'Đơn vị không hợp lệ hoặc không tồn tại.');
                isValid = false;
            } else {
                showError('unitSelect', true);
                document.getElementById('unitSelect').dataset.id = matchedDepartment.ID;
            }


            if (!expectedDate) {
                showError('expectedDate', false, 'Vui lòng chọn ngày muốn nhận.');
                isValid = false;
            } else {
                showError('expectedDate', true);
            }

            const [d1, m1, y1] = borrowDate.split('/');
            const [d2, m2, y2] = expectedDate.split('/');

            const date1 = new Date(`${y1}-${m1}-${d1}`);
            const date2 = new Date(`${y2}-${m2}-${d2}`);

            if (date2 < date1) {
                showError('expectedDate', false, 'Ngày muốn nhận phải sau hoặc bằng ngày mượn.');
                isValid = false;
            } else {
                showError('expectedDate', true);
            }

            if (!matNo) {
                showError('mainItemCode', false, 'Vui lòng nhập mã dạng phom.');
                isValid = false;
            } else {
                showError('mainItemCode', true);
            }

            if (!confirmOfficer) {
                showError('confirmOfficer', false, 'Vui lòng nhập cán bộ xác nhận.');
                isValid = false;
            } else {
                showError('confirmOfficer', true);
            }

            if (confirmOfficerNameInput.value.trim() === '') {
                showError('confirmOfficerName', false, 'Tên cán bộ xác nhận không hợp lệ.');
                isValid = false;
            } else {
                showError('confirmOfficerName', true);
            }

            if (!isValid) {
                return;
            }

            return {
                isValid
            };
        }

        function getBorrowDetails() {
            const rows = document.querySelectorAll('#phomTableBody tr');
            const details = [];

            rows.forEach(row => {
                const [matNo, name, sizeCell] = row.cells;
                const quantityInput = row.querySelector('.quantity-input');
                const size = sizeCell.textContent.trim();
                const quantity = parseInt(quantityInput?.value || 0, 10);

                if (quantity > 0) {
                    details.push({
                        LastMatNo: matNo.textContent.trim(),
                        LastName: name.textContent.trim(),
                        LastSize: size,
                        LastSum: quantity
                    });
                }
            });

            return details;
        }

        function convertToDateTime(dateStr, timeStr = '00:00:00') {
            const [d, m, y] = dateStr.split('/');
            return `${y}-${m}-${d} ${timeStr}`;
        }

        function submitData(payload) {
            fetch('submit_borrow.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'Success') {
                        alert("Bạn đã đăng ký mượn thành công!");
                        location.reload();
                        console.log("Payload đã gửi:", payload);
                        console.log("Phản hồi từ server:", data);
                    } else {
                        alert(data.message || "Có lỗi xảy ra.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Lỗi gửi đăng ký.");
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const inputs = ['cardNumber', 'borrowerName', 'unitSelect', 'expectedDate', 'mainItemCode', 'confirmOfficer'];

            inputs.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('focus', () => {
                        // Khi focus vào ô nào, kiểm tra các ô khác
                        inputs.forEach(checkId => {
                            // if (checkId === id) return; // bỏ qua chính nó

                            const otherInput = document.getElementById(checkId);
                            if (!otherInput) return;

                            const value = otherInput.value.trim();

                            // Chỉ kiểm tra nếu đang có class is-invalid
                            if (otherInput.closest('.input-group').classList.contains('is-invalid')) {
                                switch (checkId) {
                                    case 'cardNumber':
                                        if (value && !isNaN(value)) showError('cardNumber', true);
                                        break;
                                    case 'borrowerName':
                                        if (value) showError('borrowerName', true);
                                        break;
                                    case 'unitSelect':
                                        const matched = allDepartments.find(dep => dep.DepName === value);
                                        if (matched) {
                                            showError('unitSelect', true);
                                            otherInput.dataset.id = matched.ID;
                                        }
                                        break;
                                    case 'expectedDate':
                                        const borrowDate = document.getElementById('borrowDate').value.trim();
                                        if (borrowDate && value) {
                                            const [d1, m1, y1] = borrowDate.split('/');
                                            const [d2, m2, y2] = value.split('/');
                                            const date1 = new Date(`${y1}-${m1}-${d1}`);
                                            const date2 = new Date(`${y2}-${m2}-${d2}`);
                                            if (date2 >= date1) showError('expectedDate', true);
                                        }
                                        break;
                                    case 'mainItemCode':
                                        if (value) showError('mainItemCode', true);
                                        break;
                                    case 'confirmOfficer':
                                        if (value) showError('confirmOfficer', true);
                                        break;
                                }
                            }
                        });
                    });
                }
            });
        });
    </script>
</body>

</html>