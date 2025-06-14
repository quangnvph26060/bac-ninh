$(document).ready(function () {
    // Hàm debounce giúp giới hạn số lần gọi sự kiện
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    const isValidDateFormat = (date, format) => {
        const [day, month, year] = date.split("/");

        // Kiểm tra đúng format d/m/Y
        if (format === "d/m/Y") {
            if (day.length === 2 && month.length === 2 && year.length === 4) {
                const parsedDate = new Date(`${year}-${month}-${day}`);
                return (
                    parsedDate.getDate() == day &&
                    parsedDate.getMonth() + 1 == month &&
                    parsedDate.getFullYear() == year
                );
            }
        }

        // Thêm các format khác tại đây nếu cần
        return false;
    };

    // Sự kiện input cho từng thẻ input
    $(document).on(
        "input change",
        "input[data-rules], textarea[data-rules], select[data-rules]",
        debounce(function () {
            validateInput(this);
        }, 250)
    );

    // Hàm validate từng input
    function validateInput(inputElement) {
        // Lấy các rule, attribute và giá trị
        let rules = $(inputElement).data("rules").split("|");
        let attribute =
            $(inputElement).data("attribute") || $(inputElement).attr("name");
        let value = $(inputElement).val();
        let errorMessage = "";

        attribute = attribute.charAt(0).toUpperCase() + attribute.slice(1);

        let isNullable = rules.includes("nullable");

        // Danh sách thông điệp lỗi mặc định
        const defaultMessages = {
            required: `${attribute} là bắt buộc.`,
            email: `${attribute} không đúng định dạng.`,
            min: (min) => `${attribute} phải có ít nhất ${min} ký tự.`,
            max: (max) => `${attribute} không được vượt quá ${max} ký tự.`,
            numeric: `${attribute} chỉ chấp nhận số.`,
            integer: `${attribute} phải là số nguyên.`,
            alpha: `${attribute} chỉ chấp nhận ký tự chữ.`,
            alpha_num: `${attribute} chỉ chấp nhận ký tự chữ và số.`,
            regex: `${attribute} không đúng định dạng.`,
            date: `${attribute} không phải là ngày hợp lệ.`,
            date_format: (format) =>
                `${attribute} phải có định dạng ${format}.`,
            before: (date) => `${attribute} phải trước ngày ${date}.`,
            after_today: `${attribute} phải sau ngày hôm nay.`,
            array: `${attribute} phải là một mảng.`,
            url: `${attribute} không đúng định dạng URL.`,
            in: (values) =>
                `${attribute} phải là một trong các giá trị sau: ${values.join(
                    ", "
                )}.`,
        };

        // Nếu nullable và không có giá trị, bỏ qua mọi rule
        if (
            isNullable &&
            (value === "" || value === null || value === undefined)
        ) {
            // Xóa message lỗi nếu có
            $(inputElement).next(".error-message").text("");
            return true;
        }

        for (let index = 0; index < rules.length; index++) {
            let rule = rules[index];

            if (rule === "nullable") continue; // Bỏ qua rule nullable

            if (rule === "required") {
                if (value.trim() === "") {
                    errorMessage = defaultMessages.required;
                    break;
                }
            } else if (rule === "email") {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(value)) {
                    errorMessage = defaultMessages.email;
                    break;
                }
            } else if (rule.startsWith("min")) {
                const minLength = parseInt(rule.split(":")[1]);
                if (value.length < minLength) {
                    errorMessage = defaultMessages.min(minLength);
                    break;
                }
            } else if (rule.startsWith("max")) {
                const maxLength = parseInt(rule.split(":")[1]);
                if (value.length > maxLength) {
                    errorMessage = defaultMessages.max(maxLength);
                    break;
                }
            } else if (rule === "numeric") {
                if (!/^\d+$/.test(value)) {
                    errorMessage = defaultMessages.numeric;
                    break;
                }
            } else if (rule === "integer") {
                if (!/^[-+]?\d+$/.test(value)) {
                    errorMessage = defaultMessages.integer;
                    break;
                }
            } else if (rule === "alpha") {
                if (!/^[a-zA-Z]+$/.test(value)) {
                    errorMessage = defaultMessages.alpha;
                    break;
                }
            } else if (rule === "alpha_num") {
                if (!/^[a-zA-Z0-9]+$/.test(value)) {
                    errorMessage = defaultMessages.alpha_num;
                    break;
                }
            } else if (rule.startsWith("regex")) {
                const regexPattern = rule.split(":")[1];
                const regex = new RegExp(regexPattern);
                if (!regex.test(value)) {
                    errorMessage = defaultMessages.regex;
                    break;
                }
            } else if (rule === "date") {
                if (isNaN(Date.parse(value))) {
                    errorMessage = defaultMessages.date;
                    break;
                }
            } else if (rule.startsWith("date_format")) {
                const format = rule.split(":")[1];
                if (!isValidDateFormat(value, format)) {
                    errorMessage = defaultMessages.date_format(format);
                    break;
                }
            } else if (rule.startsWith("before")) {
                const date = rule.split(":")[1];
                if (new Date(value) >= new Date(date)) {
                    errorMessage = defaultMessages.before(date);
                    break;
                }
            } else if (rule === "after_today") {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const [day, month, year] = value.split("/");
                const inputDate = new Date(`${year}-${month}-${day}`);

                if (inputDate <= today) {
                    errorMessage = defaultMessages.after_today;
                    break;
                }
            } else if (rule === "array") {
                if (!Array.isArray(value)) {
                    errorMessage = defaultMessages.array;
                    break;
                }
            } else if (rule === "url") {
                const urlPattern = /^(https?:\/\/[^\s$.?#].[^\s]*)$/;
                if (!urlPattern.test(value)) {
                    errorMessage = defaultMessages.url;
                    break;
                }
            } else if (rule.startsWith("in")) {
                const values = rule.split(":")[1].split(",");
                if (!values.includes(value)) {
                    errorMessage = defaultMessages.in(values);
                    break;
                }
            }
        }

        if (errorMessage) {
            $(inputElement)
                .next(".error-message")
                .text(errorMessage)
                .css("display", "block");
        } else {
            $(inputElement)
                .next(".error-message")
                .text("")
                .css("display", "none");
        }

        // Trả về trạng thái lỗi
        return errorMessage === "";
    }
});
