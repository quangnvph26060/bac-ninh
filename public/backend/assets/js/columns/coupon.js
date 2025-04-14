const columns = [
    {
        data: "code",
        name: "code",
        title: "code",
        width: "10%",
    },
    {
        data: "type",
        name: "type",
        title: "kiểu giảm",
        render(data) {
            return data == "fixed" ? "Tiền" : "Phần trăm";
        },
    },
    {
        data: "value",
        name: "value",
        title: "Giá trị giảm",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return row.type === "fixed"
                ? formatCurrency(data)
                : `${parseFloat(data)}%`;
        },
    },
    {
        data: "max_discount",
        name: "max_discount",
        title: "Giảm tối đa",
        orderable: false,
        searchable: false,
        render(data) {
            return formatCurrency(data);
        },
    },
    {
        data: "min_order_value",
        name: "min_order_value",
        title: "Đơn hàng tối thiểu",
        orderable: false,
        searchable: false,
        render(data) {
            return formatCurrency(data);
        },
    },
    {
        data: "start_date",
        name: "start_date",
        title: "Bắt đầu",
        orderable: false,
    },
    {
        data: "end_date",
        name: "end_date",
        title: "Kết thúc",
        orderable: false,
    },
    {
        data: "status",
        name: "status",
        title: "trạng thái",
        render: function (data) {
            if (data == 1) {
                return `<span class="badge" style="background-color: rgb(47, 179, 68);">Xuất bản</span>`;
            } else {
                return `<span class="badge" style="background-color: rgb(247, 103, 7);">Chưa xuất bản</span>`;
            }
        },
        searchable: false,
    },
];
