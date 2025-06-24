console.log(window.Laravel.userId);

const chatMessages = document.getElementById("chat-messages");

if (window.Laravel.userId) {
    const userType = "User";
    const userId = window.Laravel.userId;
    window.Echo.private(`chat.${userType}.${userId}`).listen(
        "MessageSent",
        (e) => {
            console.log("Tin nhắn đến (bạn):", e);
            const msg = document.createElement("div");
            msg.className = "message-in";
            msg.textContent = e.message;
            chatMessages.appendChild(msg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    );
}
