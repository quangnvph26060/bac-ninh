console.log(window.Laravel.adminId);

if (window.Laravel.adminId) {
    const userType = "Employee";
    const userId = window.Laravel.adminId;

    window.Echo.private(`chat.${userType}.${userId}`).listen(
        "MessageSent",
        (e) => {
            console.log("Tin nhắn đến (admin):", e);

            const container = $("#messageContainer");
            const contactList = $("#contactList");

            // Kiểm tra đoạn chat đang mở với người gửi không
            const isCurrentChatOpen =
                Number(container.attr("data-id")) === e.sender_id;

            // Nếu đoạn chat đang mở, thêm tin nhắn vào container
            if (isCurrentChatOpen) {
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
                container.append(messageHtml);
                container.scrollTop(container[0].scrollHeight);
            }

            // Cập nhật hoặc thêm user vào danh sách liên hệ
            const existingItem = contactList.find(
                `li[data-user-id='${e.sender_id}']`
            );

            if (existingItem.length > 0) {
                // Cập nhật nội dung tin nhắn và thời gian
                existingItem
                    .find("p")
                    .text(e.message)
                    .toggleClass("fw-bold", !isCurrentChatOpen); // In đậm nếu không phải chat đang mở

                existingItem.find("small.time").text(e.date);

                // Nếu không phải phần tử đầu tiên, di chuyển lên đầu với hiệu ứng
                if (!existingItem.is(contactList.children("li").first())) {
                    existingItem.slideUp(200, function () {
                        contactList.prepend(existingItem);
                        existingItem.slideDown(200);
                    });
                }
            } else {
                // Tạo mới li nếu chưa có
                const newContact = $(`
                <li data-user-id="${e.sender_id}">
                    <div class="d-flex align-items-center bd-highlight">
                        <div class="img_cont">
                            <img src="${
                                e.avatar
                            }" class="rounded-circle user_img">
                        </div>
                        <div class="user_info">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>${
                                    e.sender_name ?? "Người dùng mới"
                                }</span>
                                <small class="mb-0 time text-muted">${
                                    e.created_at
                                }</small>
                            </div>
                            <p class="text-truncate ${
                                !isCurrentChatOpen ? "fw-bold" : ""
                            }" style="max-width: 230px;">
                                ${e.message}
                            </p>
                        </div>
                    </div>
                </li>
            `);

                newContact.hide().prependTo(contactList).slideDown(200);
            }
        }
    );
}
