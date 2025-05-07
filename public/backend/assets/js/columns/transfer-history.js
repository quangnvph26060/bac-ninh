const columns = [
    {
        data: "id",
        name: "id",
        title: "id",
        width: "5%",
    },
    {
        data: "user",
        name: "user",
        title: "Khách hàng",
        orderable: false,
        searchable: false,
    },
    {
        data: "code",
        name: "code",
        title: "code",
        width: "5%",
    },
    {
        data: "amount",
        name: "amount",
        title: "amount",
        orderable: false,
        searchable: false,
        render(data, type, row) {
            return (
                `${row.type === "deposit" ? "+" : "-"}` + formatCurrency(data)
            );
        },
    },
    {
        data: "balance_before",
        name: "balance_before",
        title: "balance before",
        orderable: false,
        searchable: false,
        render(data) {
            return formatCurrency(data);
        },
    },
    {
        data: "balance_after",
        name: "balance_after",
        title: "balance after",
        orderable: false,
        searchable: false,
        render(data) {
            return formatCurrency(data);
        },
    },
    {
        data: "note",
        name: "note",
        title: "note",
        orderable: false,
        searchable: false,
    },
    {
        data: "created_at",
        name: "created_at",
        title: "create date",
        searchable: false,
    },
];
