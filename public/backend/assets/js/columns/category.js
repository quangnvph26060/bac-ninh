const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
    },
    {
        data: "name",
        name: "name",
        title: "tên danh mục",
    },
    {
        data: "image",
        name: "image",
        title: "Hình ảnh",
        searchable: false,
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
    {
        data: "parent_id",
        name: "parent_id",
        title: "danh mục cha",
        orderable: false,
    },
    {
        data: "created_at",
        name: "created_at",
        title: "ngày tạo",
    },
];
