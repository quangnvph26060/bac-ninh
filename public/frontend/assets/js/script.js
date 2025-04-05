AOS.init({
    duration: 1000, // Thời gian chạy animation (ms)
    once: true, // Chỉ chạy 1 lần khi cuộn
    delay: 200, // Độ trễ trước khi chạy
});

// Thumbnail slider (ảnh nhỏ) có autoplay
function initThumbSlider() {
    let isVertical = window.matchMedia("(min-width: 768px)").matches; // Kiểm tra màn hình

    return new Swiper(".thumb-slider", {
        spaceBetween: 20,
        slidesPerView: 5,
        direction: isVertical ? "vertical" : "horizontal", // Dọc khi lớn hơn 768px, ngang khi nhỏ hơn
        loop: true,
        autoplay: {
            delay: 3000, // Tự chạy mỗi 3 giây
            disableOnInteraction: false,
        },
        watchSlidesProgress: true,
        watchSlidesVisibility: true,
    });
}

var thumbSlider = initThumbSlider();

window.addEventListener("resize", function () {
    let newIsVertical = window.matchMedia("(min-width: 768px)").matches;
    let newSlidesPerView = 5;

    if (
        newIsVertical !== (thumbSlider.params.direction === "vertical") ||
        newSlidesPerView !== thumbSlider.params.slidesPerView
    ) {
        thumbSlider.destroy(true, true); // Hủy slider cũ
        thumbSlider = initThumbSlider(); // Tạo lại slider mới
        mainSlider.thumbs.swiper = thumbSlider; // Cập nhật liên kết thumbnail
    }
});

// Main slider (ảnh lớn)
var mainSlider = new Swiper(".main-slider", {
    spaceBetween: 10,
    loop: true,
    effect: "fade",
    autoplay: {
        delay: 3000, // Đồng bộ chạy với thumbnail
        disableOnInteraction: false,
    },
    thumbs: {
        swiper: thumbSlider, // Liên kết với thumbnail slider
    },
});

var swiper = new Swiper(".my-catalog", {
    slidesPerView: 2,
    spaceBetween: 10,
    grabCursor: true, // Biểu tượng bàn tay khi kéo
    centeredSlides: false, // Không căn giữa slide để tránh lỗi kéo
    loop: false, // Không lặp lại để tránh lỗi tràn slide
    breakpoints: {
        768: { slidesPerView: 5 }, // Desktop hiển thị 5 danh mục
    },
});

document.addEventListener("DOMContentLoaded", function () {
    const menuItems = document.querySelectorAll(".has-children");
    const backButtons = document.querySelectorAll(".back-btn");
    let history = []; // Lưu lịch sử menu để quay lại

    menuItems.forEach((item) => {
        item.addEventListener("click", function () {
            const currentMenu = this.closest("ul");
            const nextMenu = document.querySelector(
                `ul[data-level="${parseInt(currentMenu.dataset.level) + 1}"]`
            );

            if (nextMenu) {
                history.push(currentMenu);
                currentMenu.classList.remove("active");
                nextMenu.classList.add("active");

                // Cập nhật tiêu đề của menu con
                nextMenu.querySelector(".menu-title").textContent =
                    this.querySelector("a").textContent;
            }
        });
    });

    backButtons.forEach((button) => {
        button.addEventListener("click", function () {
            if (history.length > 0) {
                const currentMenu = this.closest("ul");
                const previousMenu = history.pop();
                currentMenu.classList.remove("active");
                previousMenu.classList.add("active");
            }
        });
    });

    let dropdown = document.querySelector(".full-screen-dropdown");
    let navItem = document.querySelector(".nav-item#catalogDropdown");
    let timeout;

    gsap.set(dropdown, { opacity: 0, y: 20, display: "none" });

    function showDropdown() {
        clearTimeout(timeout);
        gsap.to(dropdown, {
            opacity: 1,
            y: 0,
            display: "flex",
            duration: 0.3,
            ease: "power2.out",
        });
    }

    function hideDropdown() {
        timeout = setTimeout(() => {
            gsap.to(dropdown, {
                opacity: 0,
                y: 20,
                display: "none",
                duration: 0.3,
                ease: "power2.in",
            });
        }, 300);
    }

    navItem.addEventListener("mouseenter", showDropdown);
    dropdown.addEventListener("mouseenter", showDropdown);
    navItem.addEventListener("mouseleave", hideDropdown);
    dropdown.addEventListener("mouseleave", hideDropdown);

    const openMenu = document.getElementById("openMenu");
    const closeMenu = document.getElementById("closeMenu");
    const sidebarMenu = document.getElementById("sidebarMenu");

    const showFilterBtn = document.getElementById("showFilterBtn");
    const closeFilterBtn = document.getElementById("closeFilterBtn");
    const filterPopup = document.getElementById("filterPopup");
    const overlay = document.getElementById("overlay");

    // Mở menu
    openMenu.addEventListener("click", function () {
        sidebarMenu.classList.add("active");
        overlay.classList.add("active");
    });

    // Đóng menu khi bấm nút X hoặc bấm ra ngoài overlay
    function closeSidebar() {
        sidebarMenu.classList.remove("active");
        overlay.classList.remove("active");
    }

    closeMenu.addEventListener("click", closeSidebar);
    overlay.addEventListener("click", closeSidebar);

    showFilterBtn?.addEventListener("click", () => {
        filterPopup.classList.add("show");
        overlay.classList.add("active");
    });

    closeFilterBtn?.addEventListener("click", () => {
        filterPopup.classList.remove("show");
        overlay.style.display = "none";
    });

    overlay.addEventListener("click", () => {
        filterPopup.classList.remove("show");
        overlay.classList.remove("active");
    });

    function moveFilterForm() {
        const filterForm = document.getElementById("filter-form");
        const showFilterForm = document.getElementById("show-filter-form");
        const listProducts = document.querySelector(".list-products");

        if (!filterForm || !showFilterForm || !listProducts) return;

        let form =
            filterForm.querySelector("form") ||
            showFilterForm.querySelector("form");

        if (!form) return; // Nếu không tìm thấy <form>, thoát luôn

        if (window.innerWidth < 768) {
            // Chuyển <form> vào popup mobile
            if (!showFilterForm.contains(form)) {
                showFilterForm.appendChild(form);
            }
        } else {
            // Trả <form> về vị trí cũ
            if (!filterForm.contains(form)) {
                filterForm.appendChild(form);
            }
        }
    }

    // Gọi hàm ngay khi tải trang và khi thay đổi kích thước màn hình
    moveFilterForm();
    window.addEventListener("resize", moveFilterForm);

    var swiperThumbnail = new Swiper(".swiper-thumbnail", {
        direction: "vertical",
        slidesPerView: 5,
        spaceBetween: 10,
        watchSlidesProgress: true,
        watchSlidesVisibility: true,
    });

    function updateSwiperConfig() {
        let isMobile = window.innerWidth < 786;

        // Khởi tạo Swiper
        var swiperMain = new Swiper(".swiper-main", {
            spaceBetween: 10,
            allowTouchMove: isMobile, // Nếu nhỏ hơn 786px thì cho kéo
            thumbs: {
                swiper: swiperThumbnail,
            },
        });
    }

    // Gọi hàm lần đầu tiên
    updateSwiperConfig();

    // Lắng nghe sự kiện thay đổi kích thước màn hình
    window.addEventListener("resize", updateSwiperConfig);

    var swiper = new Swiper(".my_suggest_product", {
        slidesPerView: 2, // Mobile mặc định hiển thị 2 box
        spaceBetween: 12,
        breakpoints: {
            768: {
                slidesPerView: 5, // Từ tablet trở lên hiển thị 5 box
            },
        },
    });

    const tabsContainer = document.querySelector(".nav-tabs");

    let isDown = false;
    let startX;
    let scrollLeft;

    tabsContainer?.addEventListener("mousedown", (e) => {
        isDown = true;
        tabsContainer.classList.add("active");
        startX = e.pageX - tabsContainer.offsetLeft;
        scrollLeft = tabsContainer.scrollLeft;
    });

    tabsContainer?.addEventListener("mouseleave", () => {
        isDown = false;
        tabsContainer.classList.remove("active");
    });

    tabsContainer?.addEventListener("mouseup", () => {
        isDown = false;
        tabsContainer.classList.remove("active");
    });

    tabsContainer?.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - tabsContainer.offsetLeft;
        const walk = (x - startX) * 2; // Tăng tốc độ kéo
        tabsContainer.scrollLeft = scrollLeft - walk;
    });
});
