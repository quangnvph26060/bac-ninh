const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

const dataTables = (
    api,
    columns,
    model,
    filters = {},
    sortable = false,
    isOperation = true,
    hasCheckbox = true,
    hasDateRange = false
) => {
    const hasDtControl = columns.some((col) => col.className === "dt-control");

    // Tạo <thead> động
    let thead = "<thead><tr>";

    if (hasDtControl) {
        thead += "<th></th>"; // Dành cho dt-control
    }

    if (hasCheckbox) {
        thead +=
            '<th><input type="checkbox" id="selectAll" class="form-check-input" /></th>';
    }

    columns.forEach(function (column) {
        if (column.className !== "dt-control") {
            thead += "<th>" + (column.title || "") + "</th>";
        }
    });

    if (isOperation) {
        thead += "<th>Hành động</th>";
    }

    thead += "</tr></thead>";
    $("#myTable").append(thead);

    let finalColumns;

    if (hasDtControl) {
        finalColumns = [
            {
                className: "dt-control",
                orderable: false,
                data: null,
                defaultContent: "",
            },
            ...(hasCheckbox
                ? [
                      {
                          data: "checkbox",
                          name: "checkbox",
                          orderable: false,
                          searchable: false,
                          width: "5px",
                          className: "text-center",
                      },
                  ]
                : []),
            ...columns.filter((col) => col.className !== "dt-control"),
        ];
    } else {
        finalColumns = [
            ...(hasCheckbox
                ? [
                      {
                          data: "checkbox",
                          name: "checkbox",
                          orderable: false,
                          searchable: false,
                          width: "5px",
                          className: "text-center",
                      },
                  ]
                : []),
            ...columns,
        ];
    }

    if (isOperation) {
        finalColumns.push({
            data: "operations",
            name: "operations",
            title: "hành động",
            orderable: false,
            searchable: false,
            className: "text-center",
            width: "8%",
        });
    }

    const table = $("#myTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: api,
            data: function (d) {
                const urlParams = new URLSearchParams(window.location.search);
                d.status = urlParams.get("status");
                Object.keys(filters).forEach((key) => {
                    let value = $(`#filter-${key}`).val();
                    if (value) {
                        d[key] = value;
                    }
                });

                const dateRange = $("#dateRangePicker").val();
                if (dateRange) {
                    // Phân tách startDate và endDate từ giá trị của dateRange
                    const [startDate, endDate] = dateRange.split(" - ");
                    d.start_date = moment(startDate, "DD/MM/YYYY").format(
                        "YYYY-MM-DD"
                    );
                    d.end_date = moment(endDate, "DD/MM/YYYY").format(
                        "YYYY-MM-DD"
                    );
                }
            },
        },
        columns: finalColumns,
        createdRow: function (row, data) {
            $(row).attr("data-id", data.id);
        },
        drawCallback: function () {
            // Kiểm tra xem có cần khởi tạo sortable hay không
            if ($("#myTable tbody tr").length > 1 && sortable) {
                // Khởi tạo SortableJS mỗi khi DataTables vẽ lại bảng
                new Sortable(document.querySelector("#myTable tbody"), {
                    handle: "td", // Vùng kéo thả
                    onEnd: function (evt) {
                        var order = [];
                        $("#myTable tbody tr").each(function (index) {
                            order.push($(this).data("id"));
                            $(this)
                                .find("td.position")
                                .text(index + 1);
                        });

                        // Gửi yêu cầu cập nhật thứ tự lên server
                        updateOrderInDatabase(order, model);
                    },
                });
            }
        },
        order: [],
        layout: {
            topEnd: {
                search: {
                    placeholder: "Search...",
                },
            },
        },
        language: {
            // url: "/backend/assets/js/plugin/datatables/vi.json",
        },
    });

    $(document).on("click", ".btn-operation-destroy", function () {
        let id = $(this).data("id");
        let pageInfo = table.page.info();
        Swal.fire({
            title: "Bạn có chắc chắn muốn xóa?",
            text: "Hành động này sẽ không thể hoàn tác!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Đồng ý, xóa!",
            cancelButtonText: "Hủy",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `handle-bulk-action`,
                    type: "POST",
                    data: {
                        ids: [id],
                        model: model,
                        type: "delete",
                    },
                    success: function (response) {
                        table.ajax.reload(function () {
                            let newPageInfo = table.page.info();

                            // Nếu trang hiện tại vẫn còn dữ liệu, giữ nguyên
                            if (pageInfo.page < newPageInfo.pages) {
                                table.page(pageInfo.page).draw(false);
                            } else {
                                // Nếu không còn dữ liệu ở trang hiện tại, quay về trang trước đó
                                table
                                    .page(Math.max(pageInfo.page - 1, 0))
                                    .draw(false);
                            }
                        }, false);
                        $.notify(
                            {
                                icon: "icon-bell",
                                title: "Thông báo",
                                message: "Xóa thành công.",
                            },
                            {
                                type: "success",
                                placement: {
                                    from: "bottom",
                                    align: "right",
                                },
                                time: 1000,
                            }
                        );
                    },
                    error: function (xhr) {
                        Notifications(xhr.responseJSON.message, "danger");
                    },
                });
            }
        });
    });

    $(document).on("click", "#cancelEditBtn", function () {
        // Đóng form mà không lưu thay đổi
        let tr = $(this).closest("tr");
        let row = table.row(tr);
        row.child.hide();
    });

    table.on("requestChild.dt", function (e, row) {
        row.child(format(row.data())).show();
    });

    table.on("click", "td.dt-control", function (e) {
        let tr = e.target.closest("tr");
        let row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
        } else {
            // Open this row
            row.child(format(row.data())).show();
        }
    });

    $('label[for="dt-length-0"]').remove();

    const targetDiv = $(".dt-layout-cell.dt-layout-start .dt-length");

    let _html = "";
    let currentPath = window.location.pathname;
    console.log(currentPath);

    if (currentPath.includes("/orders")) {
        // Trang đơn hàng: Hiển thị select trạng thái
        _html = `
            <select id="orderStatusSelect" class="form-select d-none">
                <option value="" disabled selected>-- Thay đổi trạng thái --</option>
                <option value="pending">Chờ xác nhận</option>
                <option value="confirmed_pending_production">Đã xác nhận, chờ sản xuất</option>
                <option value="in_production">Đang sản xuất</option>
                <option value="produced_awaiting_completion">Đã sản xuất xong, chờ hoàn thiện</option>
                <option value="completed_waiting_for_shipment">Đã hoàn thiện, chờ giao hàng</option>
                <option value="shipped">Đã giao hàng</option>
            </select>
            `;
    } else {
        // Trang khác: Hiển thị hành động mặc định
        _html = `
            <div id="actionDiv" style="display: none;">
                <div class="d-flex">
                    <select id="actionSelect" class="form-select">
                        <option value="">-- Hành động --</option>
                        <option value="delete">Xóa</option>
                    </select>
                </div>
            </div>
            `;
    }

    targetDiv.after(_html);

    if (Object.keys(filters).length > 0) {
        Object.keys(filters).forEach((key) => {
            let { title, data } = filters[key];
            let sortedData = Object.entries(data).sort((a, b) =>
                a[1].localeCompare(b[1])
            );
            let options = sortedData
                .map(([id, name]) => `<option value="${id}">${name}</option>`)
                .join("\n");

            let filterHtml = `
            <div class="ms-2">
                <div class="d-flex">
                    <select id="filter-${key}" class="form-select action-filter">
                        <option value="">-- ${title} --</option>
                        ${options}
                    </select>
                </div>
            </div>`;
            $("#actionDiv").after(filterHtml);
        });

        let resetButton = `
        <div class="ms-2">
            <button id="resetFilters" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-refresh"></i>
            </button>
        </div>`;
        $("#actionDiv").nextAll().last().after(resetButton);
    }

    if (hasDateRange) {
        const datePickerHtml = `
            <div class="d-flex align-items-center w-75">
                <input type="text" id="dateRangePicker" name="date_range" class="form-control" placeholder="Chọn khoảng ngày" />
            </div>
        `;

        targetDiv.after(datePickerHtml);

        $("#dateRangePicker").daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: "DD/MM/YYYY",
                cancelLabel: "Hủy",
                applyLabel: "Áp dụng",
                customRangeLabel: "Tùy chọn",
                daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                monthNames: [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12",
                ],
                firstDay: 1,
            },
            ranges: {
                "Hôm nay": [moment(), moment()],
                "Ngày mai": [moment().add(1, "days"), moment().add(1, "days")],
                "Tuần này": [moment().startOf("week"), moment().endOf("week")],
                "Tuần sau": [
                    moment().add(1, "week").startOf("week"),
                    moment().add(1, "week").endOf("week"),
                ],
                "Tháng này": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Tháng sau": [
                    moment().add(1, "month").startOf("month"),
                    moment().add(1, "month").endOf("month"),
                ],
            },
        });

        $("#dateRangePicker").on(
            "cancel.daterangepicker",
            function (ev, picker) {
                $(this).val("");
                table.ajax.reload();
            }
        );

        // Bắt sự kiện khi chọn ngày
        $("#dateRangePicker").on(
            "apply.daterangepicker",
            function (ev, picker) {
                $(this).val(
                    picker.startDate.format("DD/MM/YYYY") +
                        " - " +
                        picker.endDate.format("DD/MM/YYYY")
                );

                table.ajax.reload();
            }
        );
    }

    $(".action-filter").on("change", function () {
        table.ajax.reload();
    });

    $(document).on("click", "#resetFilters", function () {
        let hasSelectedFilters = false;
        $(".form-select").each(function () {
            if ($(this).val()) {
                hasSelectedFilters = true;
                return false;
            }
        });

        if (!hasSelectedFilters) return;

        $(".form-select").val("").trigger("change");
    });

    $('#myTable thead input[type="checkbox"]').on("click", function () {
        const isChecked = $(this).prop("checked");
        $('#myTable tbody input[type="checkbox"]').prop("checked", isChecked);
        toggleActionDiv();
    });

    $("#myTable tbody").on("click", 'input[type="checkbox"]', function () {
        const allChecked =
            $('#myTable tbody input[type="checkbox"]').length ===
            $('#myTable tbody input[type="checkbox"]:checked').length;
        $('#myTable thead input[type="checkbox"]').prop("checked", allChecked);
        toggleActionDiv();
    });

    $(document).on("change", "#orderStatusSelect", function () {
        const status = $(this).val();

        const selectedIds = $(".row-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selectedIds.length <= 0) {
            Notifications("Vui lòng chọn ít nhất 1 bản ghi!", "danger");
        }

        $.ajax({
            url: "/admin/orders/change-status",
            method: "POST",
            data: {
                ids: selectedIds,
                model: model,
                status,
            },
            success: (res) => {
                table.ajax.reload(function () {
                    let newPageInfo = table.page.info();

                    // Nếu trang hiện tại vẫn còn dữ liệu, giữ nguyên
                    if (pageInfo.page < newPageInfo.pages) {
                        table.page(pageInfo.page).draw(false);
                    } else {
                        // Nếu không còn dữ liệu ở trang hiện tại, quay về trang trước đó
                        table.page(Math.max(pageInfo.page - 1, 0)).draw(false);
                    }
                }, false); // Sử dụng biến table thay vì gọi lại $('#myTable').DataTable()
                Notifications(res.message, "success");
                $("#actionSelect").val("");
                $('input[type="checkbox"]').prop("checked", false);
                toggleActionDiv();
            },
            error: (xhr) => {
                Notifications(
                    xhr.responseJSON?.message ||
                        "Đã có lỗi xảy ra, vui lòng thử lại sau!",
                    "danger"
                );
            },
        });
    });

    $("#actionSelect").on("change", function () {
        const selectedAction = $("#actionSelect").val();

        if (!selectedAction) return;

        const selectedIds = $(".row-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        let pageInfo = table.page.info(); // Lưu trang hiện tại

        if (selectedAction === "delete") {
            $.ajax({
                url: "handle-bulk-action",
                method: "POST",
                data: {
                    ids: selectedIds,
                    model: model,
                    type: "delete",
                },
                success: function (response) {
                    table.ajax.reload(function () {
                        let newPageInfo = table.page.info();

                        // Nếu trang hiện tại vẫn còn dữ liệu, giữ nguyên
                        if (pageInfo.page < newPageInfo.pages) {
                            table.page(pageInfo.page).draw(false);
                        } else {
                            // Nếu không còn dữ liệu ở trang hiện tại, quay về trang trước đó
                            table
                                .page(Math.max(pageInfo.page - 1, 0))
                                .draw(false);
                        }
                    }, false); // Sử dụng biến table thay vì gọi lại $('#myTable').DataTable()
                    Notifications("Xóa thành công.", "success");
                    $("#actionSelect").val("");
                    $('input[type="checkbox"]').prop("checked", false);
                    toggleActionDiv();
                },
                error: function (xhr) {
                    $('input[type="checkbox"]').prop("checked", false);
                    $("#actionSelect").val("");
                    $("#actionDiv").hide(); // Ẩn #actionDiv nếu có lỗi xảy ra
                    Notifications(xhr.responseJSON.message, "danger");
                },
            });
        }
    });
};

function updateOrderInDatabase(order, model) {
    $.ajax({
        url: "/admin/change-order",
        method: "POST",
        data: {
            order: order,
            model: model,
        },
        success: function (response) {
            Toast.fire({
                icon: "success",
                title: response.message,
            });
        },
        error: function (xhr) {
            Toast.fire({
                icon: "error",
                title: xhr.responseJSON.message,
            });
        },
    });
}

function toggleActionDiv() {
    if ($(".row-checkbox:checked").length > 0) {
        $("#actionDiv").show();
        $("#orderStatusSelect").removeClass("d-none");
    } else {
        $("#actionDiv").hide();
        $("#orderStatusSelect").addClass("d-none");
    }
}

$('#myTable thead input[type="checkbox"]').on("click", function () {
    const isChecked = $(this).prop("checked");
    $('#myTable tbody input[type="checkbox"]').prop("checked", isChecked);
    toggleActionDiv();
});

const handleDestroy = (model) => {
    $("tbody").on("click", ".btn-destroy", function (e) {
        e.preventDefault();

        if (confirm("Chắc chắn muốn xóa?")) {
            var form = $(this).closest("form");

            form.append("model", model);

            $.ajax({
                url: form.attr("action"),
                method: "POST",
                data: form.serialize(),
                success: function (response) {
                    $("#myTable").DataTable().ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(jqXHR);
                },
            });
        }
    });
};
