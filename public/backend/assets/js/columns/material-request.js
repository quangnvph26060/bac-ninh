const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
        width: "5%",
    },
    {
        data: "code",
        name: "code",
        title: "Mã phiếu",
        width: "12%",
        render: (data, type, row) => {
            return `<a href="/admin/material-requests/edit/${row.id}" class="fw-bold">${data}</a>`;
        },
    },
    {
        data: "order_code",
        name: "order_code",
        title: "đơn hàng",
        render: (data, type, row) => {
            return `
                <a href="#" class="fw-bold">${row.order.order_code}</a>
                <span class="mb-0 text-muted d-block">${row.order.order_name}</span>
            `;
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "productInfo",
        name: "productInfo",
        title: "sản phẩm/biến thể",
        render: (data, type, row) => {
            return `
                <div class="d-flex flex-column">
                    <span>${row.order_item.product.name}</span>
                    <small>${row.order_item.product_variant.sku}</small>
                </div>
            `;
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "quantity",
        name: "quantity",
        title: "số lượng",
        render(h) {
            return h + " vật tư";
        },
    },
    {
        data: "status",
        name: "status",
        title: "Trạng thái",
        orderable: false,
        searchable: false,
        render: (data) => {
            let label = "";
            let cls = "";

            switch (data) {
                case "pending":
                    label = "Chờ duyệt";
                    cls = "badge bg-warning text-dark";
                    break;
                case "approved":
                    label = "Đã duyệt";
                    cls = "badge bg-success";
                    break;
                case "rejected":
                    label = "Từ chối";
                    cls = "badge bg-danger";
                    break;
                default:
                    label = "Không rõ";
                    cls = "badge bg-secondary";
            }

            return `<span class="${cls}">${label}</span>`;
        },
    },
    {
        data: "created_by",
        name: "created_by",
        title: "người tạo",
        orderable: false,
        searchable: false,
        render: (data, type, row) => {
            return row.creator.full_name;
        },
    },
    {
        data: "created_at",
        name: "created_at",
        title: "Ngày tạo",
        orderable: false,
        searchable: false,
    },
];
