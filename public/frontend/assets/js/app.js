document.addEventListener("DOMContentLoaded", function () {
	// ----------------- Dropdown menu info user ---------------------
	const dropdownMenu = document.querySelector(".avatar__info");
	const dropdownPopup = document.querySelector(".dropdown_popup");

	// ----------------- Dropdown menu notification ---------------------
	const notificationBtn = document.getElementById("notificationBtn");
	const notificationPopup = document.getElementById("notificationPopup");

	// Hàm đóng popup cụ thể
	function togglePopup(popup, otherPopup) {
		if (popup.classList.contains("show")) {
			popup.classList.remove("show");
		} else {
			otherPopup.classList.remove("show"); // Đóng popup còn lại
			popup.classList.add("show");
		}
	}

	// Toggle dropdown user
	dropdownMenu.addEventListener("click", function (event) {
		event.stopPropagation();
		togglePopup(dropdownPopup, notificationPopup);
	});

	// Toggle notification
	notificationBtn.addEventListener("click", function (event) {
		event.stopPropagation();
		togglePopup(notificationPopup, dropdownPopup);
	});

	// Ẩn tất cả popup khi click ra ngoài
	document.addEventListener("click", function (event) {
		if (
			!dropdownPopup.contains(event.target) &&
			!dropdownMenu.contains(event.target) &&
			!notificationPopup.contains(event.target) &&
			!notificationBtn.contains(event.target)
		) {
			dropdownPopup.classList.remove("show");
			notificationPopup.classList.remove("show");
		}
	});

	document.querySelectorAll(".has-submenu > .nav-link").forEach((menu) => {
		menu.addEventListener("click", function (event) {
			if (
				document.querySelector(".layout-sider").classList.contains("collapsed")
			) {
				return;
			}
			event.preventDefault();
			const parent = this.parentElement;
			const submenu = this.nextElementSibling;
			const arrow = this.querySelector(".dropdown-arrow"); // Lấy mũi tên

			// Đóng tất cả các menu khác trước khi mở
			document.querySelectorAll(".has-submenu.open").forEach((item) => {
				if (item !== parent) {
					item.classList.remove("open");
					item.querySelector(".submenu").style.maxHeight = null;
					item.querySelector(".dropdown-arrow").classList.remove("rotate");
				}
			});

			// Toggle trạng thái của submenu hiện tại
			if (parent.classList.contains("open")) {
				parent.classList.remove("open");
				submenu.style.maxHeight = null; // Đóng submenu
				arrow.classList.remove("rotate"); // Xóa class để mũi tên quay về
			} else {
				parent.classList.add("open");
				submenu.style.maxHeight = submenu.scrollHeight + "px"; // Mở submenu
				arrow.classList.add("rotate"); // Thêm class để mũi tên xoay
			}
		});
	});

	const navItems = document.querySelectorAll(".layout-sider .nav-item");
	const sider = document.querySelector(".layout-sider");
	let activeTooltip = null; // Theo dõi tooltip đang hiển thị
	let activePopup = null; // Theo dõi popup đang hiển thị

	// Hàm ẩn tất cả tooltip và popup
	const hideAllOverlays = () => {
		document.querySelectorAll(".tooltip").forEach((tooltip) => {
			tooltip.style.visibility = "hidden";
			tooltip.style.opacity = "0";
		});
		document.querySelectorAll(".submenu-popup").forEach((popup) => {
			popup.style.visibility = "hidden";
			popup.style.opacity = "0";
		});
		activeTooltip = null;
		activePopup = null;
	};

	navItems.forEach((item) => {
		const link = item.querySelector(".nav-link");
		const span = link.querySelector("span");
		const tooltipText = span.textContent;
		const href = link.getAttribute("href");
		const isSubmenu = item.classList.contains("has-submenu");
		let timeoutId = null;

		if (isSubmenu) {
			// Xử lý cho mục có submenu
			const submenuItems = item.querySelector(".submenu"); // Giả sử submenu đã có trong HTML
			const popup = document.createElement("div");
			popup.classList.add("submenu-popup");

			// Tạo cấu trúc ul li a cho popup
			const ul = document.createElement("ul");
			submenuItems.querySelectorAll("li").forEach((subItem) => {
				const subLink = subItem.querySelector("a");
				const li = document.createElement("li");
				const a = document.createElement("a");
				a.textContent = subLink.textContent;
				a.href = subLink.href;
				li.appendChild(a);
				ul.appendChild(li);
			});
			popup.appendChild(ul);
			document.body.appendChild(popup);

			// Hàm hiển thị popup
			const showPopup = () => {
				if (!sider.classList.contains("collapsed")) {
					return; // Không hiển thị popup nếu sidebar không thu nhỏ
				}
				hideAllOverlays();

				const rect = item.getBoundingClientRect();
				popup.style.top = `${rect.top}px`;
				popup.style.left = `${rect.right + 6}px`;
				popup.style.visibility = "visible";
				popup.style.opacity = "1";
				activePopup = popup;

				if (timeoutId) {
					clearTimeout(timeoutId);
				}
			};

			// Hàm ẩn popup với delay
			const hidePopupWithDelay = () => {
				timeoutId = setTimeout(() => {
					popup.style.visibility = "hidden";
					popup.style.opacity = "0";
					timeoutId = null;
					if (activePopup === popup) {
						activePopup = null;
					}
				}, 500);
			};

			// Sự kiện cho nav-item có submenu
			item.addEventListener("mouseenter", showPopup);
			popup.addEventListener("mouseenter", showPopup);
			item.addEventListener("mouseleave", (e) => {
				const relatedTarget = e.relatedTarget;
				if (relatedTarget !== popup && !popup.contains(relatedTarget)) {
					hidePopupWithDelay();
				}
			});
			popup.addEventListener("mouseleave", (e) => {
				const relatedTarget = e.relatedTarget;
				if (relatedTarget !== item && !item.contains(relatedTarget)) {
					hidePopupWithDelay();
				}
			});
		} else {
			// Xử lý cho mục không có submenu (tooltip)
			const tooltip = document.createElement("a");
			tooltip.classList.add("tooltip");
			tooltip.textContent = tooltipText;
			tooltip.setAttribute("href", href);
			document.body.appendChild(tooltip);

			// Hàm hiển thị tooltip
			const showTooltip = () => {
				if (!sider.classList.contains("collapsed")) {
					return;
				}
				hideAllOverlays();

				const rect = item.getBoundingClientRect();
				tooltip.style.top = `${rect.top + rect.height / 2}px`;
				tooltip.style.left = `${rect.right + 10}px`;
				tooltip.style.visibility = "visible";
				tooltip.style.opacity = "1";
				activeTooltip = tooltip;

				if (timeoutId) {
					clearTimeout(timeoutId);
				}
			};

			// Hàm ẩn tooltip với delay
			const hideTooltipWithDelay = () => {
				timeoutId = setTimeout(() => {
					tooltip.style.visibility = "hidden";
					tooltip.style.opacity = "0";
					timeoutId = null;
					if (activeTooltip === tooltip) {
						activeTooltip = null;
					}
				}, 500);
			};

			// Sự kiện cho nav-item không có submenu
			item.addEventListener("mouseenter", showTooltip);
			tooltip.addEventListener("mouseenter", showTooltip);
			item.addEventListener("mouseleave", (e) => {
				const relatedTarget = e.relatedTarget;
				if (relatedTarget !== tooltip) {
					hideTooltipWithDelay();
				}
			});
			tooltip.addEventListener("mouseleave", (e) => {
				const relatedTarget = e.relatedTarget;
				if (relatedTarget !== item) {
					hideTooltipWithDelay();
				}
			});
		}
	});

	// Sự kiện thu nhỏ sidebar
	document
		.querySelector(".iconCollapsed button")
		.addEventListener("click", function () {
			document.querySelectorAll(".has-submenu.open").forEach((item) => {
				item.classList.remove("open");
				item.querySelector(".submenu").style.maxHeight = null;
				item.querySelector(".dropdown-arrow").classList.remove("rotate");
			});
			document.querySelector(".layout-sider").classList.toggle("collapsed");
			hideAllOverlays(); // Ẩn tất cả tooltip/popup khi toggle sidebar
		});

	function wrapDivs() {
		const header = document.querySelector(".header");
		if (!header) return; // Kiểm tra nếu không có header để tránh lỗi

		const existingWrapper = document.getElementById("mobile-wrapper");
		const logo = document.querySelector(".logo-header"); // Lấy logo
		const contentHeader = document.querySelector(".content_header"); // Lấy content_header

		if (screen.width <= 768) {
			if (!existingWrapper) {
				const wrapper = document.createElement("div");
				wrapper.id = "mobile-wrapper";

				// Tạo button và thêm vào đầu tiên
				// const buttonContainer = document.createElement("div");
				// buttonContainer.innerHTML = `
				// 		<button class="d-flex" id="hamburgerBtn">
				// 			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				// 				<g clip-path="url(#clip0_6356_153471)">
				// 					<path d="M0.833328 10H19.1667" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10" stroke-linecap="square"></path>
				// 					<path d="M0.833328 4.16699H19.1667" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10" stroke-linecap="square"></path>
				// 					<path d="M0.833328 15.834H19.1667" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10" stroke-linecap="square"></path>
				// 				</g>
				// 				<defs>
				// 					<clipPath id="clip0_6356_153471">
				// 						<rect width="20" height="20" fill="white"></rect>
				// 					</clipPath>
				// 				</defs>
				// 			</svg>
				// 		</button>
				// 	`;

				// wrapper.appendChild(buttonContainer); // Thêm nút button vào đầu tiên

				// Di chuyển tất cả div con vào wrapper
				while (header.firstChild) {
					wrapper.appendChild(header.firstChild);
				}

				header.appendChild(wrapper);
			}

			// Thay đổi class của logo
			if (logo) {
				logo.classList.replace("logo-header", "logo-header-mobile");
			}

			// Thay đổi class của content_header thành header_avatar_box
			if (contentHeader) {
				contentHeader.classList.replace("content_header", "header_avatar_box");
			}
		} else {
			if (existingWrapper) {
				// Di chuyển các phần tử ra khỏi wrapper rồi xóa wrapper
				while (existingWrapper.firstChild) {
					header.appendChild(existingWrapper.firstChild);
				}
				existingWrapper.remove();
			}

			// Đổi lại class khi màn hình lớn hơn 768px
			if (logo) {
				logo.classList.replace("logo-header-mobile", "logo-header");
			}

			// Đổi lại class khi màn hình lớn hơn 768px
			if (contentHeader) {
				contentHeader.classList.replace("header_avatar_box", "content_header");
			}
		}
	}

	window.addEventListener("load", wrapDivs);
	window.addEventListener("resize", wrapDivs);

	const hamburgerBtn = document.getElementById("hamburgerBtn");
	const closeMenuBtn = document.getElementById("closeMenuBtn");
	const overlay = document.getElementById("overlay");

	// Mở menu
	hamburgerBtn.addEventListener("click", () => {
		sider.classList.add("active");
		overlay.classList.add("active");
	});

	// Đóng menu
	closeMenuBtn.addEventListener("click", () => {
		sider.classList.remove("active");
		overlay.classList.remove("active");
	});

	// Đóng menu khi bấm vào overlay
	overlay.addEventListener("click", () => {
		sider.classList.remove("active");
		overlay.classList.remove("active");
	});
});
