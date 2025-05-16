const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "order_code",
        name: "order_code",
        title: "Mã đơn hàng",
        width: "5%",
    },
    {
        data: "customer_information",
        name: "customer_information",
        title: "Thông tin khách hàng",
        orderable: false,
        searchable: false,
    },
    {
        data: "order_name",
        name: "order_name",
        title: "Tên đơn hàng",
    },
    {
        data: "product_count",
        name: "product_count",
        title: "Số lượng sản phẩm",
        orderable: false,
        searchable: false,
        render: (data) => {
            return `${data} product`;
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
            return data ? data : 'N/A';
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
