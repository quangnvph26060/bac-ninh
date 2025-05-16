(function (window) {
    const positions = [
        "top-right",
        "top-center",
        "top-left",
        "bottom-right",
        "bottom-center",
        "bottom-left",
    ];

    function getContainer(position) {
        if (!positions.includes(position)) position = "top-right";
        const id = "toastr-container-" + position;
        let container = document.getElementById(id);
        if (!container) {
            container = document.createElement("div");
            container.id = id;
            document.body.appendChild(container);
        }
        return container;
    }

    const toastr = {
        show(message, type = "info", options = {}) {
            const delay = options.time || 3000;
            const position = options.position || "top-right";

            const container = getContainer(position);

            const toast = document.createElement("div");
            toast.classList.add("toastr", type);

            // Thêm class show-top hoặc show-bottom tùy vị trí để chạy animation đúng
            if (position.startsWith("top")) {
                toast.classList.add("show-top");
            } else {
                toast.classList.add("show-bottom");
            }

            const iconMap = {
                success: '<i class="fas fa-check-circle"></i>',
                error: '<i class="fas fa-times-circle"></i>',
                warning: '<i class="fas fa-exclamation-triangle"></i>',
                info: '<i class="fas fa-info-circle"></i>',
            };

            toast.innerHTML = `
            <span class="icon">${iconMap[type]}</span>
            <span>${message}</span>
            <span class="close-btn"><i class="fas fa-times"></i></span>
        `;

            toast.addEventListener("animationend", () => {
                if (
                    toast.classList.contains("hide-up") ||
                    toast.classList.contains("hide-down")
                ) {
                    if (container.contains(toast)) container.removeChild(toast);
                }
            });

            toast.querySelector(".close-btn").onclick = () => {
                if (position.startsWith("top")) {
                    toast.classList.remove("show-top");
                    toast.classList.add("hide-up");
                } else {
                    toast.classList.remove("show-bottom");
                    toast.classList.add("hide-down");
                }
            };

            container.appendChild(toast);

            setTimeout(() => {
                if (!container.contains(toast)) return;
                if (position.startsWith("top")) {
                    toast.classList.remove("show-top");
                    toast.classList.add("hide-up");
                } else {
                    toast.classList.remove("show-bottom");
                    toast.classList.add("hide-down");
                }
            }, delay);
        },

        success(msg, opts) {
            this.show(msg, "success", opts);
        },
        error(msg, opts) {
            this.show(msg, "error", opts);
        },
        warning(msg, opts) {
            this.show(msg, "warning", opts);
        },
        info(msg, opts) {
            this.show(msg, "info", opts);
        },
    };

    window.datgin = toastr;
})(window);
