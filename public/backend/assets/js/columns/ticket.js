const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
    },
    {
        data: "code",
        name: "code",
        title: "mã",
        width: "12%",
        render(data, type, row) {
            return `
                <a href="/admin/tickets/reply/${row.id}" class="fw-bold">${row.code}</a>
            `;
        },
    },
    {
        data: "customer",
        name: "customer",
        title: "Khách hàng",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return `
            <p class="mb-0">${row.user.name}</p>
            <p class="mb-0">${row.user.email}</p>
            `;
        },
    },
    {
        data: "order",
        name: "order",
        title: "đơn hàng",
        render(data, type, row) {
            return `
                <a href="/admin/orders/edit/${row.order.id}">${row.order.order_code}</a>
            `;
        },
    },
    {
        data: "subject",
        name: "subject",
        title: "chủ thể",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return row.subject.title;
        },
    },
    {
        data: "reason",
        name: "reason",
        title: "lý do đóng",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return data || "N/A";
        },
    },
    {
        data: "created_at",
        name: "created_at",
        title: "ngày tạo",
        searchable: false,
    },
    {
        data: "status",
        name: "status",
        title: "Trạng thái",
        render: function (data) {
            switch (data) {
                case "open":
                    return `<span class="badge" style="background-color: #0d6efd;">Chưa giải quyết</span>`;
                case "resolving":
                    return `<span class="badge" style="background-color: #ffc107; color: black;">Đang giải quyết</span>`;
                case "resolved":
                    return `<span class="badge" style="background-color: #20c997;">Đã giải quyết</span>`;
                case "closed":
                    return `<span class="badge" style="background-color: #6c757d;">Đã đóng</span>`;
                default:
                    return `<span class="badge bg-secondary">Không rõ</span>`;
            }
        },
        searchable: false,
    },
];
