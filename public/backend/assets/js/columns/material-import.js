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
        title: "Mã phiếu nhập",
        width: "12%",
        render: (data, type, row) => {
            return `<a href="/admin/materials/edit/${row.id}" class="fw-bold">${data}</a>`;
        },
    },
    {
        data: "supplier",
        name: "supplier",
        title: "nhà cung cấp",
    },
    {
        data: "total",
        name: "total",
        title: "tổng tiền (USD)",
    },
    {
        data: "paid",
        name: "paid",
        title: "đã trả (USD)",
    },
    {
        data: "payment_status",
        name: "payment_status",
        title: "trạng thái",
    },
    {
        data: "date",
        name: "date",
        title: "ngày nhập",
        orderable: false,
        searchable: false,
    },
    {
        data: "created_at",
        name: "created_at",
        title: "Ngày tạo",
        orderable: false,
        searchable: false,
    },
];
