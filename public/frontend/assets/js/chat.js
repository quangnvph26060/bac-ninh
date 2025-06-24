const chatBtn = document.getElementById("chat-button");
const chatPopup = document.getElementById("chat-popup");
const chatIcon = document.getElementById("chat-icon");
const chatForm = document.getElementById("chat-form");
const chatInput = document.getElementById("chat-input");
const chatMessages = document.getElementById("chat-messages");

let isOpen = false;
let hasLoadedMessages = false;
let messagePage = 1;
let isLoadingMessages = false;
let hasMoreMessages = true;

chatBtn?.addEventListener("click", () => {
    isOpen = !isOpen;

    if (isOpen) {
        chatPopup.style.display = "flex";
        setTimeout(() => {
            chatPopup.classList.add("show");
        }, 10);

        if (!hasLoadedMessages) {
            recentMessages(); // Tải trang đầu tiên
            hasLoadedMessages = true;
        }
    } else {
        chatPopup.classList.remove("show");
        setTimeout(() => {
            chatPopup.style.display = "none";
        }, 300);
    }

    chatIcon.innerHTML = isOpen
        ? '<i class="bi bi-x-circle"></i>'
        : '<i class="bi bi-chat-left-dots"></i>';
});

function recentMessages(page = 1, appendToTop = false) {
    if (isLoadingMessages || !hasMoreMessages) return;

    isLoadingMessages = true;

    $.ajax({
        url: `/recent-messages?page=${page}`,
        method: "GET",
        success: (response) => {
            const chatMessages = $("#chat-messages");

            if (response.length < 20) {
                hasMoreMessages = false; // Không còn dữ liệu
            }

            if (!appendToTop) {
                chatMessages.empty(); // Chỉ xoá nếu là lần đầu
            }

            // Ghi lại chiều cao ban đầu nếu đang append lên đầu
            const oldScrollHeight = chatMessages[0].scrollHeight;

            // Đảo ngược thứ tự tin nhắn khi load lần đầu để hiển thị tin nhắn cũ ở trên, mới ở dưới
            const messagesToDisplay = appendToTop
                ? response
                : [...response].reverse();

            messagesToDisplay.forEach((msg) => {
                const isAdmin = msg.sender.full_name !== undefined;
                const messageHtml = `
                    <div class="${isAdmin ? "message-in" : "message-out"}">
                        ${escapeHtml(msg.message)}
                    </div>
                `;

                if (appendToTop) {
                    chatMessages.prepend(messageHtml);
                } else {
                    chatMessages.append(messageHtml);
                }
            });

            if (appendToTop) {
                // Giữ đúng vị trí scroll sau khi prepend
                const newScrollHeight = chatMessages[0].scrollHeight;
                chatMessages.scrollTop(newScrollHeight - oldScrollHeight);
            } else {
                // Cuộn xuống đáy ở lần đầu
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }

            isLoadingMessages = false;
        },
        error: (xhr) => {
            console.error("Lỗi khi load tin nhắn:", xhr.responseText);
            isLoadingMessages = false;
        },
    });
}

// Bắt sự kiện scroll lên đầu để load thêm
$("#chat-messages").on("scroll", function () {
    if ($(this).scrollTop() === 0 && !isLoadingMessages && hasMoreMessages) {
        messagePage++;
        recentMessages(messagePage, true);
    }
});

function escapeHtml(text) {
    return $("<div>").text(text).html();
}

$("#chat-input").keypress(function (e) {
    if (e.which == 13 && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

chatForm?.addEventListener("submit", function (e) {
    e.preventDefault();
    sendMessage();
});

function sendMessage() {
    const text = chatInput.value.trim();
    if (text) {
        fetch("/send-messages", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.Laravel.csrfToken,
            },
            body: JSON.stringify({ message: text, receiver_id: 1 }),
        })
            .then((response) => {
                if (!response.ok)
                    throw new Error("Network response was not ok");
                return response.json();
            })
            .then((data) => {
                const msg = document.createElement("div");
                msg.className = "message-out";
                msg.textContent = text;
                chatMessages.appendChild(msg);

                // Reset textarea
                chatInput.value = "";
                autosize.update(chatInput); // 👈 reset chiều cao về mặc định

                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch((error) => {
                console.error("Error sending message:", error);
            });
    }
}
