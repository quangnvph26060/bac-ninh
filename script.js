const $ = window.$;
const bootstrap = window.bootstrap;

$(document).ready(() => {
    // Global variables to store state
    let selectedOrder = null;
    let selectedProduct = null;
    let materials = [];
    let bomExists = null;
    let existingMaterials = [];
    let filteredMaterials = [];
    let selectedExistingMaterial = null;

    // Bootstrap modals
    const addMaterialModal = new bootstrap.Modal(
        document.getElementById("addMaterialModal")
    );
    const successModal = new bootstrap.Modal(
        document.getElementById("successModal")
    );

    // Initialize by loading orders
    loadOrders();

    // Event listeners
    $("#orderSelect").on("change", handleOrderChange);
    $("#productSelect").on("change", handleProductChange);
    $("#addMaterialBtn").on("click", openAddMaterialModal);
    $("#saveMaterialBtn").on("click", handleAddMaterial);
    $("#submitRequestBtn").on("click", handleSubmitRequest);
    $("#successModalCloseBtn").on("click", resetForm);
    $("#materialTabs button").on("click", function (e) {
        e.preventDefault();
        $(this).tab("show");

        // Reset selected material when switching tabs
        selectedExistingMaterial = null;

        // Load existing materials when switching to that tab
        if ($(this).attr("id") === "existing-tab") {
            loadExistingMaterials();
        }

        // Update the save button text based on active tab
        updateSaveButtonText();
    });

    $("#searchMaterial").on("keyup", filterExistingMaterials);
    $("#searchMaterialBtn").on("click", filterExistingMaterials);

    // Load orders from API (simulated)
    function loadOrders() {
        $("#orderLoading").removeClass("d-none");

        // Simulate API call with timeout
        setTimeout(() => {
            const orders = [
                {
                    id: "1",
                    orderNumber: "ORD-2023-001",
                    customerName: "Công ty ABC",
                    date: "2023-06-10",
                },
                {
                    id: "2",
                    orderNumber: "ORD-2023-002",
                    customerName: "Công ty XYZ",
                    date: "2023-06-12",
                },
                {
                    id: "3",
                    orderNumber: "ORD-2023-003",
                    customerName: "Công ty 123",
                    date: "2023-06-13",
                },
            ];

            // Clear and populate the order select
            $("#orderSelect option:not(:first)").remove();

            orders.forEach((order) => {
                $("#orderSelect").append(
                    `<option value="${order.id}">${order.orderNumber} - ${order.customerName} (${order.date})</option>`
                );
            });

            $("#orderLoading").addClass("d-none");
        }, 500);
    }

    // Handle order selection change
    function handleOrderChange() {
        const orderId = $(this).val();

        if (!orderId) {
            // Hide product selection and materials if no order is selected
            $("#productSelectionContainer").addClass("d-none");
            $("#materialsContainer").addClass("d-none");
            selectedOrder = null;
            return;
        }

        // Store selected order
        selectedOrder = {
            id: orderId,
            orderNumber: $("#orderSelect option:selected").text(),
        };

        // Reset product and materials
        selectedProduct = null;
        materials = [];
        bomExists = null;

        // Show product selection and load products
        $("#productSelectionContainer").removeClass("d-none");
        $("#materialsContainer").addClass("d-none");
        $("#productSelect").val("");
        loadProducts(orderId);
    }

    // Load products for selected order (simulated)
    function loadProducts(orderId) {
        $("#productLoading").removeClass("d-none");

        // Simulate API call with timeout
        setTimeout(() => {
            const products = [
                { id: "1", name: "Sản phẩm A", sku: "SKU-A", quantity: 10 },
                { id: "2", name: "Sản phẩm B", sku: "SKU-B", quantity: 5 },
                { id: "3", name: "Sản phẩm C", sku: "SKU-C", quantity: 8 },
            ];

            // Clear and populate the product select
            $("#productSelect option:not(:first)").remove();

            products.forEach((product) => {
                $("#productSelect").append(
                    `<option value="${product.id}" data-quantity="${product.quantity}">${product.name} - SKU: ${product.sku} (SL: ${product.quantity})</option>`
                );
            });

            $("#productLoading").addClass("d-none");
        }, 500);
    }

    // Handle product selection change
    function handleProductChange() {
        const productId = $(this).val();

        if (!productId) {
            // Hide materials if no product is selected
            $("#materialsContainer").addClass("d-none");
            selectedProduct = null;
            return;
        }

        // Store selected product
        selectedProduct = {
            id: productId,
            name: $("#productSelect option:selected").text(),
            quantity: Number.parseInt(
                $("#productSelect option:selected").data("quantity")
            ),
        };

        // Reset materials
        materials = [];

        // Show materials container and load BOM
        $("#materialsContainer").removeClass("d-none");
        loadBOM(productId);
    }

    // Load BOM for selected product (simulated)
    function loadBOM(productId) {
        // Show loading indicator (could add a spinner here)

        // Simulate API call with timeout
        setTimeout(() => {
            // Simulate whether BOM exists for this product
            const hasBom = productId === "1" || productId === "2";
            bomExists = hasBom;

            // Show appropriate alert
            if (hasBom) {
                $("#bomExistsAlert").removeClass("d-none");
                $("#bomNotExistsAlert").addClass("d-none");

                // If BOM exists, get materials from BOM and multiply by product quantity
                const bomMaterials = [
                    {
                        id: "1",
                        name: "Vải cotton",
                        code: "M001",
                        unit: "m",
                        quantity: 2,
                        inBom: true,
                    },
                    {
                        id: "2",
                        name: "Cúc áo",
                        code: "M002",
                        unit: "cái",
                        quantity: 5,
                        inBom: true,
                    },
                    {
                        id: "3",
                        name: "Chỉ may",
                        code: "M003",
                        unit: "cuộn",
                        quantity: 1,
                        inBom: true,
                    },
                ];

                // Multiply quantities by the product quantity
                const productQty = selectedProduct.quantity || 1;
                materials = bomMaterials.map((material) => ({
                    ...material,
                    quantity: material.quantity * productQty,
                }));
            } else {
                $("#bomExistsAlert").addClass("d-none");
                $("#bomNotExistsAlert").removeClass("d-none");
                materials = [];
            }

            // Update materials table
            updateMaterialsTable();

            // Update submit button state
            updateSubmitButtonState();
        }, 500);
    }

    // Update materials table with current materials
    function updateMaterialsTable() {
        const $tbody = $("#materialsTable tbody");
        $tbody.empty();

        if (materials.length === 0) {
            $("#materialsTable").addClass("d-none");
            $("#emptyMaterialsMessage").removeClass("d-none");
            return;
        }

        $("#materialsTable").removeClass("d-none");
        $("#emptyMaterialsMessage").addClass("d-none");

        materials.forEach((material) => {
            const $row = $(`
        <tr ${material.inBom ? 'class="material-from-bom"' : ""}>
          <td>${material.code}</td>
          <td>${material.name}</td>
          <td>${material.unit}</td>
          <td>
            <input type="number" class="form-control form-control-sm quantity-input"
                  value="${material.quantity}" min="1"
                  data-material-id="${material.id}">
          </td>
          <td>
            <button class="btn btn-sm btn-outline-danger remove-material"
                    data-material-id="${material.id}"
                    ${
                        material.inBom
                            ? 'disabled title="Không thể xóa vật tư từ BOM"'
                            : 'title="Xóa vật tư"'
                    }>
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `);

            $tbody.append($row);
        });

        // Add event listeners for quantity changes and remove buttons
        $(".quantity-input").on("change", handleQuantityChange);
        $(".remove-material").on("click", handleRemoveMaterial);
    }

    // Handle quantity change for a material
    function handleQuantityChange() {
        const materialId = $(this).data("material-id");
        const newQuantity = Number.parseInt($(this).val());

        if (newQuantity < 1) {
            $(this).val(1);
            return;
        }

        // Update material quantity in the array
        materials = materials.map((material) =>
            material.id === materialId
                ? { ...material, quantity: newQuantity }
                : material
        );

        // Update submit button state
        updateSubmitButtonState();
    }

    // Handle remove material button click
    function handleRemoveMaterial() {
        const materialId = $(this).data("material-id");

        // Remove material from the array
        materials = materials.filter((material) => material.id !== materialId);

        // Update materials table
        updateMaterialsTable();

        // Update submit button state
        updateSubmitButtonState();
    }

    // Open add material modal
    function openAddMaterialModal() {
        // Reset form
        $("#addMaterialForm")[0].reset();
        $("#materialUnit").val("cái");
        $("#materialQuantity").val(1);

        // Reset search and selection
        $("#searchMaterial").val("");
        selectedExistingMaterial = null;

        // Default to existing materials tab
        $("#existing-tab").tab("show");
        loadExistingMaterials();

        // Update save button text
        updateSaveButtonText();

        // Open modal
        addMaterialModal.show();
    }

    // Handle add material form submission
    function handleAddMaterial() {
        // Check which tab is active
        const isExistingTab = $("#existing-tab").hasClass("active");

        if (isExistingTab) {
            // Handle adding existing material
            if (!selectedExistingMaterial) {
                alert("Vui lòng chọn một vật tư từ danh sách!");
                return;
            }

            // Get the quantity from the input in the selected row
            const $selectedRow = $(
                `.material-selectable[data-material-id="${selectedExistingMaterial.id}"]`
            );
            const quantity = Number.parseInt(
                $selectedRow.find(".material-quantity-input").val()
            );

            if (isNaN(quantity) || quantity < 1) {
                alert("Vui lòng nhập số lượng hợp lệ!");
                return;
            }

            // Create new material from the selected existing material
            const newMaterial = {
                id: selectedExistingMaterial.id,
                code: selectedExistingMaterial.code,
                name: selectedExistingMaterial.name,
                unit: selectedExistingMaterial.unit,
                quantity: quantity,
                inBom: false,
            };

            // Check if this material already exists in the list
            const existingIndex = materials.findIndex(
                (m) => m.id === newMaterial.id
            );

            if (existingIndex >= 0) {
                // If material already exists, just update the quantity
                materials[existingIndex].quantity += quantity;
            } else {
                // Otherwise add as new material
                materials.push(newMaterial);
            }
        } else {
            // Handle adding new material (existing code)
            // Get form values
            const code = $("#materialCode").val().trim();
            const name = $("#materialName").val().trim();
            const unit = $("#materialUnit").val().trim();
            const quantity = Number.parseInt($("#materialQuantity").val());

            // Validate form
            if (!code || !name || !unit || isNaN(quantity) || quantity < 1) {
                alert("Vui lòng điền đầy đủ thông tin vật tư!");
                return;
            }

            // Create new material
            const newMaterial = {
                id: `new-${Date.now()}`,
                code,
                name,
                unit,
                quantity,
                inBom: false,
            };

            // Add to materials array
            materials.push(newMaterial);
        }

        // Update materials table
        updateMaterialsTable();

        // Highlight the new row
        const $newRow = $("#materialsTable tbody tr:last-child");
        $newRow.addClass("highlight-new");

        // Close modal
        addMaterialModal.hide();

        // Update submit button state
        updateSubmitButtonState();
    }

    // Update submit button state
    function updateSubmitButtonState() {
        if (selectedOrder && selectedProduct && materials.length > 0) {
            $("#submitRequestBtn").prop("disabled", false);
        } else {
            $("#submitRequestBtn").prop("disabled", true);
        }
    }

    // Handle submit request button click
    function handleSubmitRequest() {
        // Disable button and show loading state
        const $btn = $("#submitRequestBtn");
        const originalText = $btn.html();
        $btn.prop("disabled", true).html(
            '<i class="fas fa-spinner fa-pulse"></i> Đang gửi...'
        );

        // Prepare data to submit
        const requestData = {
            orderId: selectedOrder.id,
            productId: selectedProduct.id,
            materials: materials.map((m) => ({
                materialId: m.id,
                quantity: m.quantity,
            })),
        };

        // Simulate API call with timeout
        setTimeout(() => {
            console.log("Submitting request:", requestData);

            // Reset button state
            $btn.html(originalText);

            // Show success modal
            successModal.show();
        }, 1000);
    }

    // Reset form after successful submission
    function resetForm() {
        // Reset selects
        $("#orderSelect").val("");
        $("#productSelectionContainer").addClass("d-none");
        $("#materialsContainer").addClass("d-none");

        // Reset state
        selectedOrder = null;
        selectedProduct = null;
        materials = [];
        bomExists = null;

        // Reset submit button
        $("#submitRequestBtn").prop("disabled", true);
    }

    // Add this function to update the save button text based on active tab
    function updateSaveButtonText() {
        if ($("#existing-tab").hasClass("active")) {
            $("#saveMaterialBtn").text("Thêm vật tư đã chọn");
        } else {
            $("#saveMaterialBtn").text("Thêm vật tư mới");
        }
    }

    // Add this function to load existing materials
    function loadExistingMaterials() {
        // Show loading indicator
        $("#existingMaterialsTable").addClass("d-none");
        $("#noExistingMaterialsFound").addClass("d-none");
        $("#existingMaterialsLoading").removeClass("d-none");

        // Simulate API call with timeout
        setTimeout(() => {
            // Sample data - in a real app, this would come from an API
            existingMaterials = [
                {
                    id: "m001",
                    code: "VT001",
                    name: "Vải cotton trắng",
                    unit: "m",
                    availableQuantity: 500,
                },
                {
                    id: "m002",
                    code: "VT002",
                    name: "Vải cotton đen",
                    unit: "m",
                    availableQuantity: 350,
                },
                {
                    id: "m003",
                    code: "VT003",
                    name: "Vải lụa",
                    unit: "m",
                    availableQuantity: 200,
                },
                {
                    id: "m004",
                    code: "VT004",
                    name: "Cúc áo nhỏ",
                    unit: "cái",
                    availableQuantity: 1000,
                },
                {
                    id: "m005",
                    code: "VT005",
                    name: "Cúc áo lớn",
                    unit: "cái",
                    availableQuantity: 800,
                },
                {
                    id: "m006",
                    code: "VT006",
                    name: "Khóa kéo 15cm",
                    unit: "cái",
                    availableQuantity: 600,
                },
                {
                    id: "m007",
                    code: "VT007",
                    name: "Khóa kéo 20cm",
                    unit: "cái",
                    availableQuantity: 450,
                },
                {
                    id: "m008",
                    code: "VT008",
                    name: "Chỉ may trắng",
                    unit: "cuộn",
                    availableQuantity: 300,
                },
                {
                    id: "m009",
                    code: "VT009",
                    name: "Chỉ may đen",
                    unit: "cuộn",
                    availableQuantity: 280,
                },
                {
                    id: "m010",
                    code: "VT010",
                    name: "Nhãn mác",
                    unit: "cái",
                    availableQuantity: 2000,
                },
            ];

            // Set filtered materials to all materials initially
            filteredMaterials = [...existingMaterials];

            // Update the table
            updateExistingMaterialsTable();

            // Hide loading indicator
            $("#existingMaterialsLoading").addClass("d-none");
            $("#existingMaterialsTable").removeClass("d-none");
        }, 700);
    }

    // Add this function to filter existing materials based on search input
    function filterExistingMaterials() {
        const searchTerm = $("#searchMaterial").val().toLowerCase().trim();

        if (!searchTerm) {
            filteredMaterials = [...existingMaterials];
        } else {
            filteredMaterials = existingMaterials.filter(
                (material) =>
                    material.code.toLowerCase().includes(searchTerm) ||
                    material.name.toLowerCase().includes(searchTerm)
            );
        }

        updateExistingMaterialsTable();
    }

    // Add this function to update the existing materials table
    function updateExistingMaterialsTable() {
        const $tbody = $("#existingMaterialsTable tbody");
        $tbody.empty();

        if (filteredMaterials.length === 0) {
            $("#existingMaterialsTable").addClass("d-none");
            $("#noExistingMaterialsFound").removeClass("d-none");
            return;
        }

        $("#existingMaterialsTable").removeClass("d-none");
        $("#noExistingMaterialsFound").addClass("d-none");

        filteredMaterials.forEach((material) => {
            const $row = $(`
        <tr class="material-selectable" data-material-id="${material.id}">
          <td>${material.code}</td>
          <td>${material.name}</td>
          <td>${material.unit}</td>
          <td>
            <input type="number" class="form-control form-control-sm material-quantity-input"
                  value="1" min="1" max="${material.availableQuantity}">
          </td>
          <td>
            <button class="btn btn-sm btn-outline-primary select-material" data-material-id="${material.id}">
              Chọn
            </button>
          </td>
        </tr>
      `);

            $tbody.append($row);
        });

        // Add event listeners for selecting materials
        $(".select-material").on("click", function (e) {
            e.stopPropagation();
            const materialId = $(this).data("material-id");
            selectExistingMaterial(materialId);
        });

        $(".material-selectable").on("click", function () {
            const materialId = $(this).data("material-id");
            selectExistingMaterial(materialId);
        });
    }

    // Add this function to handle selecting an existing material
    function selectExistingMaterial(materialId) {
        // Remove selection from all rows
        $(".material-selectable").removeClass("material-selected");

        // Add selection to the clicked row
        $(`.material-selectable[data-material-id="${materialId}"]`).addClass(
            "material-selected"
        );

        // Store the selected material
        selectedExistingMaterial = filteredMaterials.find(
            (m) => m.id === materialId
        );
    }
});
