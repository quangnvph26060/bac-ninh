$(document).ready(function () {
    let eye = $(".fa-regular.fa-eye");
    let eye_invisible = $(".far.fa-eye-slash");

    let input = $('input[name="password"]');

    eye.click(function () {
        eye.hide();
        eye_invisible.show();

        input.attr("type", "text");
    });

    eye_invisible.click(function () {
        eye_invisible.hide();
        eye.show();

        input.attr("type", "password");
    });

    const notyf = new Notyf({
        duration: 500000,
        ripple: true,
        types: [
            {
                type: "success",
                background: "#198754",
                icon: {
                    className: "fa-solid fa-circle-check",
                    tagName: "i",
                    color: "white",
                },
            },
            {
                type: "error",
                background: "#dc3545",
                icon: {
                    className: "fa-solid fa-circle-xmark",
                    tagName: "i",
                    color: "white",
                },
            },
        ],
    });

    $("#form-login").on("submit", function (e) {
        e.preventDefault();

        let $form = $(this).serializeArray();

        $.ajax({
            url: window.location.href,
            method: "POST",
            data: $form,
            success(response) {
                console.log(response);

                if (response.success) {
                    window.location.href = response.data.redirect;
                }
            },
            error(xhr) {
                notyf.error(
                    xhr.responseJSON?.message ||
                        "Đã có lỗi xảy ra. Vui lòng thử lại sau!"
                );
            },
        });
    });
});
