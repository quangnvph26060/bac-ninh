const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "email",
        name: "email",
        title: "email",
    },
    {
        data: "status",
        name: "status",
        title: "trạng thái",
        render: function (data) {
            switch (data) {
                case "pending":
                    return "<span class='badge bg-warning text-dark'>Chờ xác nhận</span>";
                case "approved":
                    return "<span class='badge bg-success'>Đã xác nhận</span>";
                case "rejected":
                    return "<span class='badge bg-danger'>Đã từ chối</span>";
                default:
                    return "<span class='badge bg-secondary'>Không xác định</span>";
            }
        },
        searchable: false,
        orderable: false,
    },
    {
        data: "created_at",
        name: "created_at",
        title: "thời gian gửi yêu cầu",
        orderable: false,
        searchable: false,
    },
    {
        data: "updated_at",
        name: "updated_at",
        title: "thời gian xác nhận yêu cầu",
        searchable: false,
        orderable: false,
    },
    {
        data: "action",
        name: "action",
        title: "hành động",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return `
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item btn-confirm" href="#" data-id="${row.id}">Xác nhận</a></li>
                        <li><a class="dropdown-item btn-reject" href="#" data-id="${row.id}">Từ chối</a></li>
                    </ul>
                </div>
            `;
        },
    },
];
