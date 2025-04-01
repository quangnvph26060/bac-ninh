console.log(typeof $.notify);

// Kiểm tra xem có cột dt-control hay không
const hasDtControl = columns.some((col) => col.className === "dt-control");

// Tạo <thead>
let thead = "<thead><tr>";

// Nếu có dt-control, thì thêm vào đầu tiên
if (hasDtControl) {
    thead += "<th></th>"; // Chỗ này để chừa vị trí cho dt-control
}

thead += '<th><input type="checkbox" id="selectAll" /></th>';

// Thêm các cột khác vào thead
columns.forEach(function (column) {
    if (column.className !== "dt-control") {
        thead += "<th>" + column.title + "</th>";
    }
});
thead += "</tr></thead>";

// Thêm <thead> vào bảng
$("#myTable").append(thead);

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
    isOperation = true
) => {
    // Nếu có dt-control, đảm bảo nó ở vị trí đầu tiên
    let finalColumns = hasDtControl
        ? [
              {
                  className: "dt-control",
                  orderable: false,
                  data: null,
                  defaultContent: "",
              },
              {
                  data: "checkbox",
                  name: "checkbox",
                  orderable: false,
                  searchable: false,
                  width: "5px",
                  className: "text-center",
              },
              ...columns.filter((col) => col.className !== "dt-control"),
          ]
        : [
              {
                  data: "checkbox",
                  name: "checkbox",
                  orderable: false,
                  searchable: false,
                  width: "5px",
                  className: "text-center",
              },
              ...columns,
          ];

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
                Object.keys(filters).forEach((key) => {
                    let value = $(`#filter-${key}`).val();
                    if (value) {
                        d[key] = value;
                    }
                });
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
            search: "",
        },
    });

    $(document).on("click", ".btn-operation-destroy", function () {
        let id = $(this).data("id");

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
                        table.ajax.reload();
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
                        console.log(xhr);
                        Swal.fire("Lỗi!", "Không thể xóa dữ liệu.", "error");
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

    $(document).on("submit", "#myForm", function (e) {
        e.preventDefault();

        let formData = new FormData(this),
            row = table.row($(this).closest("tr"));

        formData.append("id", row.data().id);
        formData.append("_method", "PUT");
        formData.append("model", model);

        $.post({
            url: "/admin/handle-fast-update",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Toast.fire({
                        icon: "success",
                        title: res.message,
                    });
                    table.draw();
                }
            },
            error: (xhr) =>
                Toast.fire({
                    icon: "error",
                    title: xhr.responseJSON.message,
                }),
        });
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

    let _html = `
    <div id="actionDiv" style="display: none;">
        <div class="d-flex">
            <select id="actionSelect" class="form-select">
                <option value="">-- Hành động --</option>
                <option value="delete">Xóa</option>
            </select>
        </div>
    </div>
    `;

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

    $("#myTable tbody").on("click", 'input[type="checkbox"]', function () {
        const allChecked =
            $('#myTable tbody input[type="checkbox"]').length ===
            $('#myTable tbody input[type="checkbox"]:checked').length;
        $('#myTable thead input[type="checkbox"]').prop("checked", allChecked);
        toggleActionDiv();
    });

    $("#actionSelect").on("change", function () {
        const selectedAction = $("#actionSelect").val();

        console.log(selectedAction);

        if (!selectedAction) return;

        const selectedIds = $(".row-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

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
                    table.ajax.reload(); // Sử dụng biến table thay vì gọi lại $('#myTable').DataTable()
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
                    $("#actionSelect").val("");
                    $('input[type="checkbox"]').prop("checked", false);
                    toggleActionDiv();
                },
                error: function () {
                    alert("Có lỗi xảy ra, vui lòng thử lại!");
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
    } else {
        $("#actionDiv").hide();
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
