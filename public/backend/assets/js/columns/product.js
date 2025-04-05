const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "name",
        name: "name",
        title: "tên sản phẩm",
        width: "15%",
    },
    {
        data: "brand_id",
        name: "brand_id",
        title: "thương hiệu",
        orderable: false,
        searchable: false,
        width: "10%",
    },
    {
        data: "category_id",
        name: "category_id",
        title: "danh mục",
        orderable: false,
        searchable: false,
        width: "10%",
    },
    {
        data: "stock",
        name: "stock",
        title: "số lượng",
        width: "5%",
        searchable: false,
    },
    {
        data: "sale_price",
        name: "sale_price",
        title: "giá bán",
        width: "10%",
        render(data, type, row) {
            return formatCurrency(data);
        },
        searchable: false,
    },
    {
        data: "stock_status",
        name: "stock_status",
        title: "tttk",
        render(data) {
            if (data == "in_stock") {
                return `<span class="badge" style="background-color: rgb(47, 179, 68);">Còn hàng</span>`;
            } else if (data == "out_of_stock") {
                return `<span class="badge" style="background-color: rgb(247, 103, 7);">Hết hàng</span>`;
            } else {
                return `<span class="badge" style="background-color: rgb(247, 103, 7);">Sắp có hàng</span>`;
            }
        },
        searchable: false,
    },
    {
        data: "type",
        name: "type",
        title: "loại",
        searchable: false,
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
