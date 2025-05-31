function formatCurrency(amount) {
    if (!amount) return "$0";

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

const removeImage = function (imgId, inputId, imageDefault) {
    const imgElement = document.getElementById(imgId);
    const inputElement = document.getElementById(inputId);
    const parentElement = imgElement.parentElement;
    // const result = inputId.split("_").pop();

    // Set lại ảnh mặc định
    imgElement.src = imageDefault;

    // Clear giá trị của input file
    inputElement.value = "";

    parentElement.classList.remove("has-image");
    // document.querySelector(`#delete_icon_${result}`).classList.add("d-none");

    // Hiển thị thông báo
    datgin.success("Ảnh đã được xóa thành công.");
};

function validateAndPreviewImage(event, imgId, inputContainerId, imageDefault) {
    const $fileInput = $(event.target);
    const file = $fileInput[0].files[0];
    const $container = $("#" + inputContainerId);
    const expectedWidth = parseInt($container.data("width"));
    const expectedHeight = parseInt($container.data("height"));
    const expectedFormat = $container.data("format").toLowerCase();
    const maxFileSizeMB = 10;

    if (!file) return;

    const $imgElement = $("#" + imgId);
    const $parentElement = $imgElement.parent();

    // 📦 File size check
    if (file.size > maxFileSizeMB * 1024 * 1024) {
        datgin.error("The image exceeds the maximum allowed size (10MB).");
        resetImageInput();
        return;
    }

    // 🖼️ File type check
    const fileType = file.type;
    if (!fileType.startsWith("image/")) {
        datgin.error("The selected file is not a valid image.");
        resetImageInput();
        return;
    }

    const actualFormat = fileType.split("/")[1].toLowerCase();
    const allowedFormats = ["jpg", "jpeg", "png", "webp", "gif", "bmp", "tiff"];

    if (!allowedFormats.includes(actualFormat)) {
        datgin.error("Unsupported image format.");
        resetImageInput();
        return;
    }

    if (
        actualFormat !== expectedFormat &&
        !(actualFormat === "jpeg" && expectedFormat === "jpg")
    ) {
        datgin.error(
            `Invalid image format.\nExpected: .${expectedFormat}\nYour file: .${actualFormat}`
        );
        resetImageInput();
        return;
    }

    // 📏 Check image dimensions
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            if (img.width !== expectedWidth || img.height !== expectedHeight) {
                datgin.error(
                    `Invalid image dimensions.\nExpected: ${expectedWidth}x${expectedHeight}\nYour image: ${img.width}x${img.height}`
                );
                resetImageInput();
                return;
            }

            // ✅ Valid → check PPI with server
            const formData = new FormData();
            formData.append("image", file);
            formData.append("expectedPpi", $container.data("dpi"));

            $.ajax({
                url: "/orders/validate-image",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    "X-Requested-With": "XMLHttpRequest", // giúp phân biệt Ajax request
                    Accept: "application/json", // server sẽ trả JSON
                },
                success: function (data) {
                    if (data.valid) {
                        $imgElement.attr("src", e.target.result);
                        $parentElement.addClass("has-image");
                    } else {
                        datgin.error(
                            `Incorrect image PPI.\nExpected: ${data.expectedPpi} dpi\nYour image: ${data.ppi} dpi`
                        );
                        resetImageInput();
                    }
                },
                error: function (xhr) {
                    datgin.error(
                        xhr.responseJSON?.message ||
                            "An error occurred while validating the image."
                    );
                    resetImageInput();
                },
            });
        };
        img.onerror = function () {
            datgin.error("Failed to read image dimensions.");
            resetImageInput();
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    function resetImageInput() {
        $fileInput.val("");
        $imgElement.attr("src", imageDefault);
        $parentElement.removeClass("has-image");
    }
}

// Hàm mở popup và hiển thị hình ảnh
function viewImage(productId) {
    const imageUrl = $(`#${productId}`).attr("src");

    // Gán URL vào modal
    $("#imagePreview").attr("src", imageUrl);

    // Hiển thị modal Bootstrap thông qua jQuery
    $("#imagePreviewModal").modal("show");
}

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

function submitForm(formId, successCallback, url = null, errorCallback = null) {
    $(formId).on("submit", function (e) {
        e.preventDefault();

        let isValid = true;

        $(this)
            .find("input[data-rules], textarea[data-rules], select[data-rules]")
            .each(function () {
                // Gọi validate lại từng input/textarea/select
                if (!validateInput(this)) {
                    isValid = false;
                }
            });

        // Nếu có lỗi, chặn submit
        if (!isValid) {
            return;
        }

        // Cập nhật tất cả giá trị CKEditor vào các textarea tương ứng
        for (const instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

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
                if (
                    xhr.status === 403 &&
                    xhr.getResponseHeader("Content-Type").includes("text/html")
                ) {
                    document.open();
                    document.write(xhr.responseText);
                    document.close();
                }
                if (typeof errorCallback === "function") {
                    errorCallback(xhr);
                }

                $("#loadingSpinner").fadeOut();

                Notifications(xhr.responseJSON.message, "danger");
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
    $(document).on("input", ".usd-price-format", function (e) {
        if (
            e.originalEvent.inputType === "insertText" &&
            e.originalEvent.data === "."
        ) {
            return;
        }
        formatUSDPrice($(this));
    });

    // Format lại khi mất focus (blur)
    $(document).on("blur", ".usd-price-format", function () {
        formatUSDPrice($(this));
    });

    // Giới hạn chỉ nhập tối đa 2 số sau dấu phẩy
    $(document).on("keypress", ".usd-price-format", function (e) {
        if (e.key === ".") return;

        let value = $(this).val();
        let parts = value.split(".");

        if (parts.length > 1 && parts[1].length >= 2) {
            e.preventDefault();
        }
    });
});
