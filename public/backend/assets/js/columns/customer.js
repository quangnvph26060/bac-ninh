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
        title: "tên khách hàng",
    },
    {
        data: "email",
        name: "email",
        title: "email",
    },
    {
        data: "phone",
        name: "phone",
        title: "số điện thoại",
        render: function (data) {
            return data == null
                ? '<span class="badge bg-danger">Chưa cập nhật...</span>'
                : data;
        },
    },
    {
        data: "address",
        name: "address",
        title: "địa chỉ",
        orderable: false,
        searchable: false,
        render: function (data) {
            return data == null
                ? '<span class="badge bg-danger">Chưa cập nhật...</span>'
                : data;
        },
        width: "15%",
    },
    {
        data: "gender",
        name: "gender",
        title: "giới tính",
        orderable: false,
        searchable: false,
        render: function (data) {
            switch (data) {
                case "male":
                    return '<span class="badge bg-success">Nam</span>';
                case "female":
                    return '<span class="badge bg-danger">Nữ</span>';
                default:
                    return '<span class="badge bg-warning">Khác</span>';
            }
        },
    },
    {
        data: "day_of_birth",
        name: "day_of_birth",
        title: "ngày sinh",
        orderable: false,
        searchable: false,
        render: function (data) {
            return data == null
                ? '<span class="badge bg-danger">Chưa cập nhật...</span>'
                : data;
        },
    },
    {
        data: "img_url",
        name: "img_url",
        title: "ảnh đại diện",
        orderable: false,
        searchable: false,
        render: function (data) {
            return `<img src="${data}" alt="ảnh đại diện" class="img-fluid" style="width: 50px; height: 50px;">`;
        },
    },
    {
        data: "status",
        name: "status",
        title: "trạng thái",
        render: function (data) {
            console.log(data);

            return data == 1
                ? `<span class="badge" style="background-color: rgb(47, 179, 68);">Hoạt động</span>`
                : `<span class="badge" style="background-color: rgb(247, 103, 7);">Ngưng hoạt động</span>`;
        },
        searchable: false,
        orderable: false,
    },
    {
        data: "created_at",
        name: "created_at",
        title: "ngày tạo",
        searchable: false,
    },
    {
        data: "action",
        name: "action",
        title: "hành động",
        orderable: false,
        searchable: false,
        render: (data, type, row) => {
            return `<a href="/admin/customers/show/${row.id}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>`;
        },
        className: "text-center",
    },
];
