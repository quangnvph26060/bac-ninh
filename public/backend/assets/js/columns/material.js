const columns = [
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
        width: "5%",
    },
    {
        data: "name",
        name: "name",
        title: "Tên vật liệu",
        width: "15%",
        render: (data) => {
            return `<strong>${data}</strong>`;
        },
    },
    {
        data: "items",
        name: "items",
        title: "loại",
    },
    {
        data: "total_stock",
        name: "total_stock",
        title: "Tổng số lượng",
        orderable: false,
        searchable: false,
        width: "10%",
    },
];
