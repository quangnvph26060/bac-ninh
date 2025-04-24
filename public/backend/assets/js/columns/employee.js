const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "employee_code",
        name: "employee_code",
        title: "mã nhân viên",
        width: "10%",
    },
    {
        data: "full_name",
        name: "full_name",
        title: "Tên đẩy đủ",
        orderable: false,
    },
    {
        data: "phone",
        name: "phone",
        title: "Số điện thoại",
        orderable: false,
    },
    {
        data: "email",
        name: "email",
        title: "Email",
        orderable: false,
    },
    {
        data: "gender",
        name: "gender",
        title: "giới tính",
        orderable: false,
        render(data) {
            switch (data) {
                case "male":
                    return "Nam";
                case "female":
                    return "Nữ";
                default:
                    return "Khác";
            }
        },
    },
    {
        data: "date_of_birth",
        name: "date_of_birth",
        title: "ngày sinh",
        searchable: false,
    },
    {
        data: "contract_type",
        name: "contract_type",
        title: "hợp đồng",
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
];
