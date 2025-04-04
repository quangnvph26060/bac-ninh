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
        width: "12%",
        render(data, type, row) {
            return formatCurrency(data);
        },
        searchable: false,
    },
    {
        data: "company_name",
        name: "company_name",
        title: "nhà cung cấp",
        orderable: false,
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
