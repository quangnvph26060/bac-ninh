function formatCurrency(amount) {
    if (!amount) return "0";

    // Format with 2 decimal places
    const formatted = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);

    // Remove .00 if present
    if (formatted.endsWith(".00")) {
        return formatted.slice(0, -3);
    }

    // Remove trailing 0 if present
    if (formatted.endsWith("0")) {
        return formatted.slice(0, -1);
    }

    return formatted;
}

function convertToAsciiUpper(inputId) {
    $(inputId).on("input", function () {
        let value = $(this).val();

        // Chuyển thành chữ IN HOA, loại bỏ dấu tiếng Việt và khoảng trắng
        value = value
            .toUpperCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "") // Bỏ dấu tiếng Việt
            .replace(/\s+/g, ""); // Bỏ tất cả khoảng trắng

        $(this).val(value);
    });
}

function updateCharCount(inputSelector, maxLength) {
    // Tìm label có 'for' tương ứng với inputSelector
    const labelSelector = $('label[for="' + inputSelector.substring(1) + '"]');

    // Tạo thẻ charCountSelector và thêm vào sau label
    const charCountSelector = $("<small></small>")
        .addClass("char-count")
        .css({
            position: "absolute",
            right: "1.2rem",
            top: ".5rem",
        })
        .insertAfter(labelSelector);

    // Đặt maxlength ban đầu cho phần tử input/textarea
    $(inputSelector).attr("maxlength", maxLength);

    // Hàm cập nhật số ký tự
    $(inputSelector).on("input", function () {
        var currentLength = $(this).val().length;
        charCountSelector.text(currentLength + "/" + maxLength);

        // Kiểm tra khi đã đạt maxLength, ngừng nhập
        if (currentLength >= maxLength) {
            $(this).attr("maxlength", maxLength); // Ngừng cho phép nhập thêm ký tự
        }
    });

    // Cập nhật số ký tự ban đầu khi trang tải
    var initialLength = $(inputSelector).val().length;
    charCountSelector.text(initialLength + "/" + maxLength);
}

// function ckeditor(id, height = 200) {
//     if (CKEDITOR.instances[id]) {
//         CKEDITOR.instances[id].destroy(true);
//     }

//     CKEDITOR.replace(id, {
//         filebrowserUploadMethod: "form",
//         height: height,
//     });

//     // CKEDITOR.instances[id].on("change", function () {
//     //     updateCKEditorCharCount(id, maxLength);
//     // });
// }

function convertToSKU(str) {
    return str
        .replace(/đ/g, "d") // xử lý thường
        .replace(/Đ/g, "D") // xử lý hoa
        .normalize("NFD") // tách dấu
        .replace(/[\u0300-\u036f]/g, "") // xóa dấu
        .replace(/[^a-zA-Z0-9\-]/g, "") // chỉ giữ chữ, số và dấu -
        .toUpperCase();
}

$(".money-input").on("input", function () {
    let input = $(this).val().replace(/\D/g, ""); // Bỏ tất cả ký tự không phải số
    let formatted = input.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    $(this).val(formatted);
});

function updateCKEditorCharCount(id, maxLength) {
    let content = CKEDITOR.instances[id].document.getBody().getText(); // Lấy nội dung chỉ có text
    let charCount = content.length;
    let countDisplay = document.getElementById("charCount");

    if (countDisplay) {
        countDisplay.innerText = `Ký tự: ${charCount}/${maxLength}`;
    }

    // Giới hạn ký tự nếu vượt quá maxLength
    if (charCount > maxLength) {
        alert(`Nội dung không được vượt quá ${maxLength} ký tự!`);
    }
}

const previewImage = function (event, imgId) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function () {
        const imgElement = document.getElementById(imgId);
        const parentElement = imgElement.parentElement;
        imgElement.src = reader.result;
        parentElement.classList.add("has-image");
    };
    if (file) {
        reader.readAsDataURL(file);
    }
};

const removeImage = function (imgId, inputId) {
    const imgElement = document.getElementById(imgId);
    const inputElement = document.getElementById(inputId);
    const parentElement = imgElement.parentElement;
    // const result = inputId.split("_").pop();

    // Set lại ảnh mặc định
    imgElement.src = "http://127.0.0.1:8000/images/image-default.png";

    // Clear giá trị của input file
    inputElement.value = "";

    parentElement.classList.remove("has-image");
    // document.querySelector(`#delete_icon_${result}`).classList.add("d-none");

    // Hiển thị thông báo
    showToast("success", "Ảnh đã được xóa thành công.");
};

const validateAndPreviewImage = function (event, imgId, options) {
    const {
        expectedWidth,
        expectedHeight,
        expectedPPI,
        expectedFormat,
        inputId,
    } = options;

    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function () {
        const imgElement = document.getElementById(imgId);
        const image = new Image();

        image.onload = function () {
            const width = image.width;
            const height = image.height;
            const ppi = Math.round(width / 4 / (file.size / 1024 / 1024));
            const fileType = file.type;

            if (
                width === expectedWidth &&
                height === expectedHeight &&
                ppi === expectedPPI &&
                fileType === "image/" + expectedFormat
            ) {
                // Hiển thị icon thùng rác khi hover
                const inputElement = document.getElementById(inputId);

                const parentElement = inputElement.parentElement;

                parentElement.classList.add("has-image");

                // deleteIcon.classList.remove("d-none");

                imgElement.src = reader.result;
            } else {
                event.target.value = "";
                imgElement.src =
                    "http://127.0.0.1:8000/images/image-default.png";

                // Gọi hàm hiển thị lỗi
                showToast(
                    "error",
                    `Thiết kế không khớp với mẫu.
                    Thiết kế đề xuất: Width: ${expectedWidth}px, Height: ${expectedHeight}px, PPI: ${expectedPPI}, File format: ${expectedFormat}.
                    Thiết kế của bạn: Width: ${width}px, Height: ${height}px, PPI: ${ppi}, File format: ${fileType}.`
                );
            }
        };
        image.src = reader.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
};

// Hàm mở popup và hiển thị hình ảnh
function viewImage(productId) {
    const imageUrl = $(`#${productId}`).attr("src");

    // Gán URL vào modal
    $("#imagePreview").attr("src", imageUrl);

    // Hiển thị modal Bootstrap thông qua jQuery
    $("#imagePreviewModal").modal("show");
}

const showToast = (type, message) => {
    const toast = notyf[type](message);

    // Khi hover vào, xóa bộ đếm thời gian
    toast.on("mouseenter", () => {
        console.log(123);

        toast.dismissible = false;
    });

    // Khi rời khỏi hover, thiết lập lại thời gian biến mất
    toast.on("mouseleave", () => {
        toast.dismissible = true;
    });
};

function generateSlug(text) {
    return text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "") // Loại bỏ dấu tiếng Việt
        .replace(/đ/g, "d")
        .replace(/Đ/g, "D") // Chuyển `đ` -> `d`
        .replace(/[^a-z0-9 -]/g, "") // Xóa ký tự đặc biệt
        .replace(/\s+/g, "-") // Thay khoảng trắng bằng dấu `-`
        .replace(/-+/g, "-") // Xóa dấu `-` dư thừa
        .trim(); // Xóa khoảng trắng đầu cuối
}

function autoGenerateSlug(fromSelector, toSelector) {
    $(fromSelector).on("input", function () {
        const text = $(this).val();
        const slug = text
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .replace(/Đ/g, "d")
            .replace(/[^a-z0-9 -]/g, "")
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-")
            .replace(/^-|-$/g, "")
            .trim();

        $(toSelector).val(slug).trigger("input"); // ✅ cập nhật rồi trigger input để char count update
    });
}

function submitForm(formId, successCallback, url = null) {
    $(formId).on("submit", function (e) {
        e.preventDefault();

        // Kiểm tra xem CKEditor đã được khởi tạo trên textarea chưa
        $("textarea.ckeditor").each(function () {
            const editorId = this.id;

            // Nếu CKEditor chưa được khởi tạo, khởi tạo nó
            if (!CKEDITOR.instances[editorId]) {
                CKEDITOR.replace(editorId, {
                    filebrowserUploadMethod: "form",
                });
            } else {
                // Nếu CKEditor đã được khởi tạo, cập nhật giá trị của nó
                CKEDITOR.instances[editorId].updateElement();
            }
        });

        // 👉 Xóa dấu phẩy trong các input có class `usd-price-format`
        $(".usd-price-format").each(function () {
            const value = $(this).val().replace(/,/g, "");
            $(this).val(value);
        });

        var formData = new FormData(this);

        $.ajax({
            url: url || window.location.href,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loadingSpinner").fadeIn();
            },
            success: function (response) {
                console.log("Dữ liệu đã được gửi thành công", response);
                if (typeof successCallback === "function") {
                    successCallback(response);
                }
            },
            error: function (xhr) {
                Notifications(xhr.responseJSON.message, "danger");
                console.log("Lỗi khi gửi dữ liệu: ", xhr);
            },
            complete: function () {
                $("#loadingSpinner").fadeOut();
            },
        });
    });
}

function Notifications(message, type) {
    $.notify(
        {
            icon: "icon-bell",
            title: "Thông báo",
            message: message || "Đã có lỗi xảy ra. Vui lòng thử lại sau!!!",
        },
        {
            type: type,
            placement: {
                from: "bottom",
                align: "right",
            },
            time: 100000,
        }
    );
}

function formatUSDPrice($input) {
    // Lưu lại giá trị gốc trước khi format
    let originalValue = $input.val();
    let cursorPos = $input.prop("selectionStart");
    let value = originalValue.replace(/[^\d.]/g, "");

    // Đếm số lượng dấu chấm
    let decimalCount = (value.match(/\./g) || []).length;

    // Nếu có nhiều hơn một dấu chấm, giữ lại dấu đầu tiên
    if (decimalCount > 1) {
        let parts = value.split(".");
        value = parts[0] + "." + parts.slice(1).join("");
    }

    // Tách phần nguyên và phần thập phân
    let parts = value.split(".");

    // Format phần nguyên
    if (parts[0]) {
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Xử lý phần thập phân
    if (parts.length > 1) {
        parts[1] = parts[1].slice(0, 2);
    }

    // Ghép lại
    let newValue = parts.join(".");

    // Cập nhật giá trị cho input
    $input.val(newValue);

    // Tính lại vị trí con trỏ
    if (cursorPos !== null) {
        // Tính toán số dấu phẩy trước và sau
        let oldCommas = (originalValue.slice(0, cursorPos).match(/,/g) || [])
            .length;
        let newCommas = (
            newValue
                .slice(0, cursorPos + (newValue.length - originalValue.length))
                .match(/,/g) || []
        ).length;

        // Điều chỉnh vị trí con trỏ
        let newCursorPos = cursorPos + (newCommas - oldCommas);

        // Đảm bảo vị trí con trỏ không vượt quá độ dài của chuỗi
        newCursorPos = Math.min(newCursorPos, newValue.length);

        // Đặt lại vị trí con trỏ
        $input[0].setSelectionRange(newCursorPos, newCursorPos);
    }
}

$(document).ready(function () {
    // Lấy tất cả input có class `usd-price-format`
    $(".usd-price-format").on("input", function (e) {
        if (
            e.originalEvent.inputType === "insertText" &&
            e.originalEvent.data === "."
        ) {
            return;
        }
        formatUSDPrice($(this));
    });

    // Format lại khi mất focus (blur)
    $(".usd-price-format").on("blur", function () {
        formatUSDPrice($(this));
    });

    // Giới hạn chỉ nhập tối đa 2 số sau dấu phẩy
    $(".usd-price-format").on("keypress", function (e) {
        if (e.key === ".") return;

        let value = $(this).val();
        let parts = value.split(".");

        if (parts.length > 1 && parts[1].length >= 2) {
            e.preventDefault();
        }
    });
});
