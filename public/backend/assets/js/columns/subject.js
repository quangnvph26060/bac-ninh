const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        width: "5%",
        orderable: false,
        searchable: false,
    },
    {
        data: "title",
        name: "title",
        title: "chủ thể",
    },
    {
        data: "created_at",
        name: "created_at",
        title: "ngày tạo",
        width: "10%",
        searchable: false,
    },
    {
        data: "status",
        name: "status",
        title: "trạng thái",
        width: "10%",
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
        data: "operations",
        name: "operations",
        title: "hành động",
        orderable: false,
        searchable: false,
        width: "15%",
    },
];
