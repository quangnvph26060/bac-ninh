

$(document).ready(() => {
    let productCounter = 0;
    let totalAmount = 0;

    // Khởi tạo ngày hiện tại
    $("#date").val(new Date().toISOString().split("T")[0]);

    // Tự động tạo mã phiếu nhập
    generateImportCode();

    // Xử lý tìm kiếm sản phẩm
    $("#searchProduct").on("input", function () {
        const searchTerm = $(this).val().toLowerCase();
        $("#existingProductsList tr").each(function () {
            const productName = $(this)
                .find("td:nth-child(3)")
                .text()
                .toLowerCase();
            const productCode = $(this)
                .find("td:nth-child(2)")
                .text()
                .toLowerCase();

            if (
                productName.includes(searchTerm) ||
                productCode.includes(searchTerm)
            ) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Xử lý chọn sản phẩm có sẵn
    $('input[name="selectedProduct"]').on("change", function () {
        if ($(this).is(":checked")) {
            const price = $(this).data("price");
            $("#selectedPrice").val(price);
            $("#selectedQuantity").val(1);
            calculateSelectedTotal();
        }
    });

    // Tính toán khi thay đổi số lượng hoặc đơn giá
    $("#selectedQuantity, #selectedPrice").on("input", calculateSelectedTotal);
    $("#newQuantity, #newPrice").on("input", calculateNewTotal);

    // Thêm sản phẩm vào phiếu
    $("#addProductBtn").on("click", () => {
        const activeTab = $(".tab-pane.active").attr("id");

        if (activeTab === "select-product") {
            addSelectedProduct();
        } else {
            addNewProduct();
        }
    });

    // Xử lý thay đổi số tiền đã trả
    $("#paid_amount").on("input", () => {
        calculateDebt();
        updatePaymentStatus();
    });

    // Xử lý xem trước
    $("#previewBtn").on("click", () => {
        generatePreview();
        $("#previewModal").modal("show");
    });

    // Xử lý submit form
    $("#materialImportForm").on("submit", (e) => {
        e.preventDefault();

        if (validateForm()) {
            saveImport();
        }
    });

    // Reset modal khi đóng
    $("#addProductModal").on("hidden.bs.modal", () => {
        resetProductModal();
    });

    function generateImportCode() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, "0");
        const day = String(now.getDate()).padStart(2, "0");
        const time =
            String(now.getHours()).padStart(2, "0") +
            String(now.getMinutes()).padStart(2, "0");

        const code = `PN${year}${month}${day}${time}`;
        $("#code").val(code);
    }

    function calculateSelectedTotal() {
        const quantity = Number.parseFloat($("#selectedQuantity").val()) || 0;
        const price = Number.parseFloat($("#selectedPrice").val()) || 0;
        const total = quantity * price;

        // Hiển thị tổng tiền (có thể thêm vào UI nếu cần)
        console.log("Tổng tiền sản phẩm đã chọn:", formatCurrency(total));
    }

    function calculateNewTotal() {
        const quantity = Number.parseFloat($("#newQuantity").val()) || 0;
        const price = Number.parseFloat($("#newPrice").val()) || 0;
        const total = quantity * price;

        console.log("Tổng tiền sản phẩm mới:", formatCurrency(total));
    }

    function addSelectedProduct() {
        const selectedRadio = $('input[name="selectedProduct"]:checked');

        if (!selectedRadio.length) {
            alert("Vui lòng chọn một sản phẩm!");
            return;
        }

        const quantity = Number.parseFloat($("#selectedQuantity").val());
        const price = Number.parseFloat($("#selectedPrice").val());
        const note = $("#selectedNote").val();

        if (!quantity || !price) {
            alert("Vui lòng nhập đầy đủ số lượng và đơn giá!");
            return;
        }

        const productData = {
            id: selectedRadio.val(),
            name: selectedRadio.data("name"),
            unit: selectedRadio.data("unit"),
            quantity: quantity,
            price: price,
            total: quantity * price,
            note: note,
        };

        addProductToTable(productData);
        $("#addProductModal").modal("hide");
    }

    function addNewProduct() {
        const code = $("#newProductCode").val().trim();
        const name = $("#newProductName").val().trim();
        const unit = $("#newProductUnit").val().trim();
        const quantity = Number.parseFloat($("#newQuantity").val());
        const price = Number.parseFloat($("#newPrice").val());
        const note = $("#newProductNote").val();

        if (!code || !name || !unit || !quantity || !price) {
            alert("Vui lòng nhập đầy đủ thông tin sản phẩm!");
            return;
        }

        const productData = {
            id: "new_" + Date.now(),
            code: code,
            name: name,
            unit: unit,
            quantity: quantity,
            price: price,
            total: quantity * price,
            note: note,
            isNew: true,
        };

        addProductToTable(productData);
        $("#addProductModal").modal("hide");
    }

    function addProductToTable(product) {
        // Xóa dòng "Chưa có sản phẩm" nếu có
        $(".no-products").remove();

        productCounter++;

        const row = `
            <tr class="product-row" data-product-id="${product.id}">
                <td class="text-center">${productCounter}</td>
                <td>
                    ${product.name}
                    ${
                        product.isNew
                            ? '<span class="badge bg-success ms-1">Mới</span>'
                            : ""
                    }
                    ${
                        product.code
                            ? `<br><small class="text-muted">Mã: ${product.code}</small>`
                            : ""
                    }
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm quantity-input"
                           value="${
                               product.quantity
                           }" min="1" step="0.01" data-original="${
            product.quantity
        }">
                    <small class="text-muted">${product.unit || ""}</small>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm price-input"
                           value="${
                               product.price
                           }" min="0" step="0.01" data-original="${
            product.price
        }">
                </td>
                <td class="total-cell fw-bold text-end">${formatCurrency(
                    product.total
                )}</td>
                <td>
                    <input type="text" class="form-control form-control-sm"
                           value="${
                               product.note || ""
                           }" placeholder="Ghi chú...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                            onclick="removeProduct(this)" title="Xóa sản phẩm">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $("#productTableBody").append(row);
        updateTotalAmount();

        // Thêm event listener cho các input mới
        attachProductEventListeners();
    }

    function attachProductEventListeners() {
        $(".quantity-input, .price-input")
            .off("input")
            .on("input", function () {
                const row = $(this).closest("tr");
                const quantity =
                    Number.parseFloat(row.find(".quantity-input").val()) || 0;
                const price =
                    Number.parseFloat(row.find(".price-input").val()) || 0;
                const total = quantity * price;

                row.find(".total-cell").text(formatCurrency(total));
                updateTotalAmount();
            });
    }

    window.removeProduct = (button) => {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            $(button).closest("tr").remove();
            updateRowNumbers();
            updateTotalAmount();

            // Hiển thị lại dòng "Chưa có sản phẩm" nếu không còn sản phẩm nào
            if ($("#productTableBody tr").length === 0) {
                $("#productTableBody").html(`
                    <tr class="no-products">
                        <td colspan="7" class="text-center text-muted">
                            Chưa có sản phẩm nào. Nhấn "Thêm sản phẩm" để bắt đầu.
                        </td>
                    </tr>
                `);
            }
        }
    };

    function updateRowNumbers() {
        $("#productTableBody tr:not(.no-products)").each(function (index) {
            $(this)
                .find("td:first")
                .text(index + 1);
        });
        productCounter = $("#productTableBody tr:not(.no-products)").length;
    }

    function updateTotalAmount() {
        totalAmount = 0;

        $("#productTableBody tr:not(.no-products)").each(function () {
            const quantity =
                Number.parseFloat($(this).find(".quantity-input").val()) || 0;
            const price =
                Number.parseFloat($(this).find(".price-input").val()) || 0;
            totalAmount += quantity * price;
        });

        $("#totalAmount").text(formatCurrency(totalAmount));
        $("#total_amount").val(totalAmount);
        calculateDebt();
    }

    function calculateDebt() {
        const paidAmount = Number.parseFloat($("#paid_amount").val()) || 0;
        const debtAmount = totalAmount - paidAmount;
        $("#debt_amount").val(debtAmount);
    }

    function updatePaymentStatus() {
        const paidAmount = Number.parseFloat($("#paid_amount").val()) || 0;

        if (paidAmount === 0) {
            $("#payment_status").val("unpaid");
        } else if (paidAmount >= totalAmount) {
            $("#payment_status").val("paid");
        } else {
            $("#payment_status").val("partial");
        }
    }

    function resetProductModal() {
        // Reset tab về tab đầu tiên
        $("#select-tab").tab("show");

        // Reset form chọn sản phẩm
        $('input[name="selectedProduct"]').prop("checked", false);
        $("#selectedQuantity, #selectedPrice, #selectedNote").val("");

        // Reset form tạo sản phẩm mới
        $(
            "#newProductCode, #newProductName, #newProductUnit, #newProductNote, #newProductDescription"
        ).val("");
        $("#newQuantity, #newPrice").val("");

        // Reset tìm kiếm
        $("#searchProduct").val("");
        $("#existingProductsList tr").show();
    }

    function generatePreview() {
        const formData = {
            code: $("#code").val(),
            date: $("#date").val(),
            supplier: $("#supplier_id option:selected").text(),
            createdBy: $("#created_by").val(),
            note: $("#note").val(),
            totalAmount: totalAmount,
            paidAmount: Number.parseFloat($("#paid_amount").val()) || 0,
            debtAmount:
                totalAmount - (Number.parseFloat($("#paid_amount").val()) || 0),
            paymentStatus: $("#payment_status option:selected").text(),
            paymentNote: $("#payment_note").val(),
        };

        let productsHtml = "";
        let stt = 1;

        $("#productTableBody tr:not(.no-products)").each(function () {
            const name = $(this)
                .find("td:nth-child(2)")
                .clone()
                .find(".badge")
                .remove()
                .end()
                .text()
                .trim();
            const quantity = $(this).find(".quantity-input").val();
            const price = Number.parseFloat($(this).find(".price-input").val());
            const total = quantity * price;
            const note = $(this).find("td:nth-child(6) input").val();

            productsHtml += `
                <tr>
                    <td class="text-center">${stt++}</td>
                    <td>${name}</td>
                    <td class="text-center">${quantity}</td>
                    <td class="text-end">${formatCurrency(price)}</td>
                    <td class="text-end fw-bold">${formatCurrency(total)}</td>
                    <td>${note || ""}</td>
                </tr>
            `;
        });

        const previewHtml = `
            <div class="preview-section">
                <div class="text-center mb-4">
                    <h3 class="text-primary">PHIẾU NHẬP HÀNG</h3>
                    <p class="mb-0">Mã phiếu: <strong>${
                        formData.code
                    }</strong></p>
                    <p>Ngày: <strong>${formatDate(formData.date)}</strong></p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Thông tin nhà cung cấp:</h6>
                        <p class="mb-1"><strong>Tên:</strong> ${
                            formData.supplier
                        }</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin phiếu:</h6>
                        <p class="mb-1"><strong>Người tạo:</strong> ${
                            formData.createdBy
                        }</p>
                        <p class="mb-1"><strong>Ghi chú:</strong> ${
                            formData.note || "Không có"
                        }</p>
                    </div>
                </div>

                <h6>Chi tiết sản phẩm:</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">STT</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Thành tiền</th>
                            <th class="text-center">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${productsHtml}
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                            <td class="text-end fw-bold">${formatCurrency(
                                formData.totalAmount
                            )}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Thông tin thanh toán:</h6>
                        <p class="mb-1"><strong>Tổng tiền:</strong> ${formatCurrency(
                            formData.totalAmount
                        )}</p>
                        <p class="mb-1"><strong>Đã thanh toán:</strong> ${formatCurrency(
                            formData.paidAmount
                        )}</p>
                        <p class="mb-1"><strong>Còn nợ:</strong> ${formatCurrency(
                            formData.debtAmount
                        )}</p>
                        <p class="mb-1"><strong>Trạng thái:</strong> ${
                            formData.paymentStatus
                        }</p>
                        ${
                            formData.paymentNote
                                ? `<p class="mb-1"><strong>Ghi chú TT:</strong> ${formData.paymentNote}</p>`
                                : ""
                        }
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <p class="mb-4"><strong>Chữ ký người tạo</strong></p>
                            <div style="height: 80px; border-bottom: 1px solid #000; margin-bottom: 10px;"></div>
                            <p class="mb-0">${formData.createdBy}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $("#previewContent").html(previewHtml);
    }

    function validateForm() {
        const requiredFields = ["code", "date", "supplier_id", "created_by"];
        let isValid = true;

        requiredFields.forEach((field) => {
            const value = $(`#${field}`).val().trim();
            if (!value) {
                $(`#${field}`).addClass("is-invalid");
                isValid = false;
            } else {
                $(`#${field}`).removeClass("is-invalid");
            }
        });

        if ($("#productTableBody tr:not(.no-products)").length === 0) {
            alert("Vui lòng thêm ít nhất một sản phẩm vào phiếu nhập!");
            isValid = false;
        }

        return isValid;
    }

    function saveImport() {
        // Tạo dữ liệu phiếu nhập
        const importData = {
            code: $("#code").val(),
            supplier_id: $("#supplier_id").val(),
            date: $("#date").val(),
            note: $("#note").val(),
            created_by: $("#created_by").val(),
            total_amount: totalAmount,
            products: [],
        };

        // Lấy dữ liệu sản phẩm
        $("#productTableBody tr:not(.no-products)").each(function () {
            const productId = $(this).data("product-id");
            const name = $(this)
                .find("td:nth-child(2)")
                .clone()
                .find(".badge")
                .remove()
                .end()
                .text()
                .trim();
            const quantity = Number.parseFloat(
                $(this).find(".quantity-input").val()
            );
            const price = Number.parseFloat($(this).find(".price-input").val());
            const note = $(this).find("td:nth-child(6) input").val();

            importData.products.push({
                material_id: productId,
                name: name,
                quantity: quantity,
                unit_price: price,
                total_price: quantity * price,
                note: note,
            });
        });

        // Thông tin thanh toán
        const paymentData = {
            total_amount: totalAmount,
            paid_amount: Number.parseFloat($("#paid_amount").val()) || 0,
            status: $("#payment_status").val(),
            note: $("#payment_note").val(),
        };

        // Hiển thị dữ liệu (trong thực tế sẽ gửi lên server)
        console.log("Dữ liệu phiếu nhập:", importData);
        console.log("Dữ liệu thanh toán:", paymentData);

        // Thông báo thành công
        alert(
            "Lưu phiếu nhập thành công!\n\nDữ liệu đã được in ra console để kiểm tra."
        );

        // Reset form nếu muốn
        if (confirm("Bạn có muốn tạo phiếu nhập mới?")) {
            resetForm();
        }
    }

    function resetForm() {
        $("#materialImportForm")[0].reset();
        $("#productTableBody").html(`
            <tr class="no-products">
                <td colspan="7" class="text-center text-muted">
                    Chưa có sản phẩm nào. Nhấn "Thêm sản phẩm" để bắt đầu.
                </td>
            </tr>
        `);
        productCounter = 0;
        totalAmount = 0;
        $("#totalAmount").text("0 VNĐ");
        $("#total_amount, #debt_amount").val("0");
        generateImportCode();
        $("#date").val(new Date().toISOString().split("T")[0]);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
        }).format(amount);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString("vi-VN");
    }
});
