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
    },
    {
        data: "brand_id",
        name: "brand_id",
        title: "thương hiệu",
        orderable: false,
        searchable: false,
    },
    {
        data: "category_id",
        name: "category_id",
        title: "danh mục",
        orderable: false,
        searchable: false,
    },
    {
        data: "quantity",
        name: "quantity",
        title: "số lượng",
        width: "5%",
        searchable: false,
    },
    {
        data: "price",
        name: "price",
        title: "giá nhập",
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
];
