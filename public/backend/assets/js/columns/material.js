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
        width: "25%",
    },

    {
        data: "stock",
        name: "stock",
        title: "số lượng",
        width: "5%",
        searchable: false,
    },
    {
        data: "price_vnd",
        name: "price_vnd",
        title: "Giá vnd",
        width: "10%",

        searchable: false,
    },
    {
        data: "price_usd",
        name: "price_usd",
        title: "Giá usd",
        width: "10%",
        searchable: false,
    },
    {
        data: "type",
        name: "type",
        title: "loại",
        render: function (data) {
            if (data == 'variant') {
                return `<span class="badge" style="background-color: #0d6efd;" >Biến thể</span>`;
            } else {
                return `<span class="badge" style="background-color: #6c757d;">Loại thường</span>`;
            }
        },
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
