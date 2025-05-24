const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
    },
    {
        data: "order_code",
        name: "order_code",
        title: "Mã đơn hàng",
        width: "15%",
        render(data, type, row) {
            return `
            <p class='mb-0'>${data}</p>
            <p class='mb-0 text-muted'>${row.order_name}</p>
            `;
        },
    },
    {
        data: "customer_information",
        name: "customer_information",
        title: "Thông tin khách hàng",
        orderable: false,
        searchable: false,
    },
    {
        data: "barcode",
        name: "barcode",
        title: "tải mã vạch",
    },
    {
        data: "product_count",
        name: "product_count",
        title: "Số lượng",
        orderable: false,
        searchable: false,
        render: (data) => {
            return `${data} sản phẩm`;
        },
    },
    {
        data: "total",
        name: "total",
        title: "Tổng tiền",
        orderable: false,
        searchable: false,
        render: (data) => {
            return formatCurrency(data);
        },
    },
    {
        data: "reason",
        name: "reason",
        title: "Lý do hủy đơn",
        orderable: false,
        searchable: false,
        render: (data) => {
            return data ? data : "N/A";
        },
    },
    {
        data: "created_at",
        name: "created_at",
        title: "Ngày tạo",
        searchable: false,
    },
    {
        data: "status",
        name: "status",
        title: "Trạng thái",
        orderable: false,
    },
];
