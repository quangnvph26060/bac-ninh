const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "employee_id",
        name: "employee_id",
        title: "Người thực hiện",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return row.employee.full_name + ` (${row.employee.employee_code })`;
        },
    },
    {
        data: "action",
        name: "action",
        title: "Hành động",
        orderable: false,
        searchable: false,
    },
    {
        data: "model_type",
        name: "model_type",
        title: "Loại dữ liệu",
        orderable: false,
        searchable: false,
    },
    {
        data: "model_id",
        name: "model_id",
        title: "ID Dữ liệu",
        orderable: false,
        searchable: false,
    },
    {
        data: "changes",
        name: "changes",
        title: "Thay đổi",
        orderable: false,
        searchable: false,
    },
    {
        data: "created_at",
        name: "created_at",
        title: "Thời gian",
        searchable: false,
    },
];
