console.log(window.Laravel.adminId);

if (window.Laravel.adminId) {
    const userType = "Employee";
    const userId = window.Laravel.adminId;
    window.Echo.private(`chat.${userType}.${userId}`).listen(
        "MessageSent",
        (e) => {
            console.log("Tin nhắn đến (admin):", e);

            const messageHtml = `
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="${e.avatar}" class="rounded-circle user_img_msg">
                            </div>
                            <div class="ms-3" style="max-width: 75%">
                                <div class="msg_cotainer">
                                    ${e.message}
                                </div>
                                <span class="msg_time">${e.created_at}</span>
                            </div>
                        </div>
                    `;

            const container = $("#messageContainer");
            console.log(container);

            container.append(messageHtml);

            // Cuộn xuống tin nhắn mới nhất
            container.scrollTop(container[0].scrollHeight);
        }
    );
}
