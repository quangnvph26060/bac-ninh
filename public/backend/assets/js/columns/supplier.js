const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
    },
    {
        data: "company_name",
        name: "company_name",
        title: "thông tin",
    },
    {
        data: "representative_name",
        name: "representative_name",
        title: "người đại diện",
        orderable: false,
        searchable: false,
    },
    {
        data: "bank_account_number",
        name: "bank_account_number",
        title: "Số tài khoản",
        searchable: false,
        orderable: false,
    },
    {
        data: "tax_code",
        name: "tax_code",
        title: "mã số thuế",
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
