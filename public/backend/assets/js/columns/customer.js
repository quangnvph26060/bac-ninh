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
        data: "phone",
        name: "phone",
        title: "số điện thoại",
        render: function (data) {
            return data == null
                ? '<small class="text-muted">Chưa cập nhật</small>'
                : data;
        },
    },
    {
        data: "email",
        name: "email",
        title: "email",
        render: function (data) {
            return data == null
                ? '<small class="text-muted">Chưa cập nhật</small>'
                : data;
        },
    },

    {
        data: "customer_type",
        name: "customer_type",
        title: "Loại",
        orderable: false,
        searchable: false,
        render: function (data) {
            switch (data) {
                case "retail":
                    return "Khách lẻ";
                case "wholesale":
                    return "Khách sỉ";
                default:
                    return "Đại lý";
            }
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
                ? '<small class="text-muted">Chưa cập nhật</small>'
                : data;
        },
        width: "15%",
    },
    {
        data: "birthday",
        name: "birthday",
        title: "ngày sinh",
        orderable: false,
        searchable: false,
        render: function (data) {
            return data == null
                ? '<small class="text-muted">Chưa cập nhật</small>'
                : data;
        },
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
                    return "Nam";
                case "female":
                    return "Nữ";
                    case "other":
                        return 'Khác'
                default:
                    return '<small class="text-muted">Chưa cập nhật</small>';
            }
        },
    },

    {
        data: "created_at",
        name: "created_at",
        title: "ngày tạo",
        searchable: false,
    },
];
