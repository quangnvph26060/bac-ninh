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
        title: "order code",
        width: "5%",
    },
    {
        data: "customer_information",
        name: "customer_information",
        title: "customer information",
        orderable: false,
        searchable: false,
    },
    {
        data: "product_count",
        name: "product_count",
        title: "Quantity",
        orderable: false,
        searchable: false,
        render: (data) => {
            return `${data} product`;
        },
    },
    {
        data: "total",
        name: "total",
        title: "total",
        orderable: false,
        searchable: false,
        render: (data) => {
            return formatCurrency(data);
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
        title: "trạng thái",
        // render: function (data) {
        //     if (data == 1) {
        //         return `<span class="badge" style="background-color: rgb(47, 179, 68);">Xuất bản</span>`;
        //     } else {
        //         return `<span class="badge" style="background-color: rgb(247, 103, 7);">Chưa xuất bản</span>`;
        //     }
        // },
        orderable: false,
    },
];
