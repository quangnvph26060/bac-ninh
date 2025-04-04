const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "logo",
        name: "logo",
        title: "logo",
        orderable: false,
        searchable: false,
        width: "5%",
    },
    {
        data: "name",
        name: "name",
        title: "tên thương hiệu",
    },
    {
        data: "website",
        name: "website",
        title: "website",
        orderable: false,
        searchable: false,
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
