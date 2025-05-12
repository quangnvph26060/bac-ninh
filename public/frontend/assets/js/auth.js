let eye = $(".fa-regular.fa-eye");
let eye_invisible = $(".far.fa-eye-slash");

let input = $('input[name="password"]');

eye.click(function () {
    console.log("eye clicked");

    eye.hide();
    eye_invisible.show();

    input.attr("type", "text");
});

eye_invisible.click(function () {
    eye_invisible.hide();
    eye.show();

    input.attr("type", "password");
});

$(document).ready(function () {
    $("#form-login, #form-register").on("submit", function (e) {
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
