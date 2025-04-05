function formatCurrency(amount) {
    return "₫" + Math.round(amount).toLocaleString("vi-VN");
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
        .replace(/đ/g, "d")   // xử lý thường
        .replace(/Đ/g, "D")   // xử lý hoa
        .normalize("NFD")     // tách dấu
        .replace(/[\u0300-\u036f]/g, "") // xóa dấu
        .replace(/[^a-zA-Z0-9\-]/g, "")  // chỉ giữ chữ, số và dấu -
        .toUpperCase();
}


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
        imgElement.src = reader.result;
    };
    if (file) {
        reader.readAsDataURL(file);
    }
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

function submitForm(formId, successCallback) {
    $(formId).on("submit", function (e) {
        e.preventDefault();

        var submitBtn = $("#submitBtn");
        var spinner = submitBtn.find(".spinner-border");

        spinner.removeClass("d-none");

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

        var formData = new FormData(this);

        $.ajax({
            url: window.location.href,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
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
                // Khôi phục trạng thái nút sau khi API hoàn tất (thành công hoặc lỗi)
                submitBtn.prop("disabled", false);
                spinner.addClass("d-none");
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
