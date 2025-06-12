const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "STT",
        orderable: false,
        searchable: false,
        width: "1%",
    },
    {
        data: "code",
        name: "code",
        title: "Mã phiếu",
        width: "12%",
        render: (data, type, row) => {
            return `<a href="/admin/materials/edit/${row.material_import_id}" class="fw-bold">${data}</a>`;
        },
    },
    {
        data: "supplier_name",
        name: "supplier_name",
        title: "Nhà cung cấp",
        orderable: false,
        searchable: false,
    },
    {
        data: "total_amount",
        name: "total_amount",
        title: "Tổng tiền (USD)",
        orderable: false,
        searchable: false,
    },
    {
        data: "paid_amount",
        name: "paid_amount",
        title: "Đã trả (USD)",
        orderable: false,
        searchable: false,
    },
    {
        data: "debt_amount",
        name: "debt_amount",
        title: "Còn nợ (USD)",
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
