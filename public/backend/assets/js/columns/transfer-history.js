const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "user",
        name: "user",
        title: "Khách hàng",
        orderable: false,
        searchable: false,
    },
    {
        data: "code",
        name: "code",
        title: "mã hóa đơn",
        width: "5%",
    },
    {
        data: "transaction_code",
        name: "transaction_code",
        title: "mã giao dịch",
        width: "5%",
    },
    {
        data: "amount",
        name: "amount",
        title: "số tiền",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return formatCurrency(data);
        },
    },
    {
        data: "method",
        name: "method",
        title: "Phương thức",
        orderable: false,
        searchable: false,
    },
    // {
    //     data: "balance_before",
    //     name: "balance_before",
    //     title: "số dư trước",
    //     orderable: false,
    //     searchable: false,
    //     render(data) {
    //         return formatCurrency(data);
    //     },
    // },
    {
        data: "balance_after",
        name: "balance_after",
        title: "số dư sau",
        orderable: false,
        searchable: false,
        render(data) {
            return formatCurrency(data);
        },
    },
    {
        data: "note",
        name: "note",
        title: "ghi chú",
        orderable: false,
        searchable: false,
        render(data) {
            return data ? data : "<span class='text-muted'>N/A</span>";
        },
    },
    {
        data: "status",
        name: "status",
        title: "trạng thái",
        orderable: false,
        searchable: false,
        render(data) {
            switch (data) {
                case "pending":
                    return "<span class='badge bg-warning'>chờ xác nhận</span>";
                case "complete":
                    return "<span class='badge bg-success'>thành công</span>";
                case "failure":
                    return "<span class='badge bg-danger'>thất bại</span>";
                default:
                    return "<span class='badge bg-secondary'>chưa xác định</span>";
            }
        },
    },
    {
        data: "proof",
        name: "proof",
        title: "Minh chứng",
        orderable: false,
        searchable: false,
        className: "review-zoom",
        render(data) {
            return `
                <a href="${data}" class="image-popup">
                    <img src="${data}" alt="Minh chứng" width="50">
                </a>
            `;
        },
    },
    {
        data: "created_at",
        name: "created_at",
        title: "ngày tạo",
        searchable: false,
    },
    {
        data: "action",
        name: "action",
        title: "hành động",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            // Kiểm tra nếu có lý do thì hiển thị, nếu không thì ẩn
            const reasonHtml = row.reason
                ? `<li><a class="dropdown-item btn-reason" href="#" data-id="${row.id}" data-reason="${row.reason}">Lý do hủy</a></li>`
                : "";

            return `
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item btn-confirm" href="#" data-id="${row.id}">Xác nhận</a></li>
                        <li><a class="dropdown-item btn-reject" href="#" data-id="${row.id}">Từ chối</a></li>
                        ${reasonHtml}
                    </ul>
                </div>
            `;
        },
    },
];
