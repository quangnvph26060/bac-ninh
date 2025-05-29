@extends('admin.layout.index')

@section('content')
    <div class="container-fluid">
        <div class="row" id="chat-container">
            <!-- Contact List Column -->
            <div class="col-md-4 col-xl-3 chat">
                <div class="card mb-sm-3 mb-md-0 contacts_card">
                    <div class="card-header">
                        <div class="input-group">
                            <input type="search" placeholder="Search over contacts" name="search" id="searchInput"
                                class="form-control search">
                            <div class="input-group-prepend">
                                <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body contacts_body">
                        @include('admin.chat.contact-list', ['users', $users])
                    </div>
                </div>
            </div>
            <!-- Chat Area Column -->
            <div class="col-md-8 col-xl-9 chat">
                <div class="card">
                    <div class="card-header msg_head">
                        <div class="d-flex align-items-center bd-highlight">
                            <div class="img_cont">
                                <img src="{{ showImage('') }}" class="rounded-circle user_img" alt="No User Selected">
                            </div>
                            <div class="user_info">
                                <span>Chưa chọn khách hàng</span>
                                <p>Vui lòng chọn một người để bắt đầu trò chuyện</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body msg_card_body" id="messageContainer">
                        <!-- Messages will be loaded here -->
                    </div>
                    <div class="card-footer">
                        <form id="messageForm" class="input-group">
                            {{-- <div class="input-group-prepend">
                                <span class="input-group-text attach_btn"><i class="fas fa-paperclip"></i></span>
                            </div> --}}
                            <textarea name="message" class="form-control type_msg" rows="1" placeholder="Hello, my name is Max"></textarea>
                            <div class="input-group-append">
                                <button type="submit" class="send_btn">
                                    <svg class="xsrhx6k" height="20px" viewBox="0 0 24 24" width="20px">
                                        <title>Nhấn Enter để gửi</title>
                                        <path
                                            d="M16.6915026,12.4744748 L3.50612381,13.2599618 C3.19218622,13.2599618 3.03521743,13.4170592 3.03521743,13.5741566 L1.15159189,20.0151496 C0.8376543,20.8006365 0.99,21.89 1.77946707,22.52 C2.41,22.99 3.50612381,23.1 4.13399899,22.8429026 L21.714504,14.0454487 C22.6563168,13.5741566 23.1272231,12.6315722 22.9702544,11.6889879 C22.8132856,11.0605983 22.3423792,10.4322088 21.714504,10.118014 L4.13399899,1.16346272 C3.34915502,0.9 2.40734225,1.00636533 1.77946707,1.4776575 C0.994623095,2.10604706 0.8376543,3.0486314 1.15159189,3.99121575 L3.03521743,10.4322088 C3.03521743,10.5893061 3.34915502,10.7464035 3.50612381,10.7464035 L16.6915026,11.5318905 C16.6915026,11.5318905 17.1624089,11.5318905 17.1624089,12.0031827 C17.1624089,12.4744748 16.6915026,12.4744748 16.6915026,12.4744748 Z"
                                            fill="var(--chat-composer-button-color)"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPage = 2;
        let hasMorePages = true;
        let isLoading = false;
        let selectedUserId = null;

        let searchTimeout;

        $('#searchInput').on('keyup', function() {
            const query = $(this).val();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route('admin.chats.index') }}',
                    type: 'GET',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        $('#contactList').html(data.html);
                    }
                });
            }, 300); // Chờ 300ms sau lần gõ cuối
        });

        $(document).ready(function() {
            // Handle form submission
            $('#messageForm').on('submit', function(e) {
                e.preventDefault();
                sendMessage();
            });

            // Handle contact list scrolling
            $('.contacts_body').on('scroll', function() {
                let $this = $(this);
                if ($this.scrollTop() + $this.innerHeight() >= this.scrollHeight - 10) {
                    if (hasMorePages && !isLoading) {
                        loadMoreUsers();
                    }
                }
            });

            // Handle contact selection
            $('#contactList').on('click', 'li', function() {

                if ($(this).hasClass('active')) return

                const userId = $(this).data('user-id');
                const userName = $(this).find('.user_info span').text();

                $('#contactList li').removeClass('active');
                $(this).addClass('active');

                $(this).find('p').removeClass('fw-bold')

                $('.msg_head .user_info span').text(userName);

                currentPage = 2; // reset page
                hasMorePages = true;
                isLoading = false;
                selectedUserId = userId;

                loadMessages(userId);
            });

            // Handle enter key press
            $('.type_msg').keypress(function(e) {
                if (e.which == 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        });

        const container = $('#messageContainer');

        container.off('scroll').on('scroll', function() {
            if (container.scrollTop() === 0 && hasMorePages && !isLoading) {
                isLoading = true;

                const previousHeight = container[0].scrollHeight;

                $.get(`{{ route('admin.chats.messages', '') }}/${selectedUserId}`, {
                    page: currentPage,
                    limit: 20
                }, function(res) {
                    const messages = res.messages;

                    if (messages.length > 0) {
                        [...messages].reverse().forEach(message => {
                            const isAdmin = message.is_admin;
                            const sender = message.from;
                            const avatar = sender.image;

                            const messageHtml = `
                        <div class="d-flex justify-content-${isAdmin ? 'end' : 'start'} mb-4">
                            ${!isAdmin ? `
                                                                                                                <div class="img_cont_msg">
                                                                                                                    <img src="${avatar}" class="rounded-circle user_img_msg">
                                                                                                                </div>
                                                                                                            ` : ''}
                            <div class="${isAdmin ? 'me-3' : 'ms-3'}" style="max-width: 75%">
                                <div class="msg_cotainer${isAdmin ? '_send' : ''}">
                                    ${message.message}
                                </div>
                                <span class="msg_time${isAdmin ? '_send' : ''}">${formatDate(message.created_at)}</span>
                            </div>
                            ${isAdmin ? `
                                                                                                                <div class="img_cont_sharer">
                                                                                                                    <img src="${avatar}" class="rounded-circle user_img_msg">
                                                                                                                </div>
                                                                                                            ` : ''}
                        </div>
                    `;

                            container.prepend(messageHtml); // chèn lên trên
                        });

                        currentPage++;
                        hasMorePages = res.has_more;

                        // Giữ nguyên vị trí scroll
                        const newHeight = container[0].scrollHeight;
                        container.scrollTop(newHeight - previousHeight);
                    } else {
                        hasMorePages = false;
                    }

                    isLoading = false;
                });
            }
        });

        function loadMessages(userId) {

            const container = $('#messageContainer');

            $.get(`{{ route('admin.chats.messages', '') }}/${userId}`, function(response) {

                container.empty();

                response.messages.forEach(message => {
                    const isAdmin = message.is_admin;

                    const sender = message.from;
                    const avatar = sender.image

                    const messageHtml = `
                        <div class="d-flex justify-content-${isAdmin ? 'end' : 'start'} mb-4">
                            ${!isAdmin ? `
                                                                                                                                                                                    <div class="img_cont_msg">
                                                                                                                                                                                        <img src="${avatar}" class="rounded-circle user_img_msg">
                                                                                                                                                                                    </div>
                                                                                                                                                                                ` : ''}

                            <div class="${isAdmin ? 'me-3' : 'ms-3'}" style="max-width: 75%">
                                <div class="msg_cotainer${isAdmin ? '_send' : ''}">
                                    ${message.message}
                                </div>
                                <span class="msg_time${isAdmin ? '_send' : ''}">${formatDate(message.created_at)}</span>
                            </div>

                            ${isAdmin ? `
                                                                                                                                                                                    <div class="img_cont_sharer">
                                                                                                                                                                                        <img src="${avatar}" class="rounded-circle user_img_msg">
                                                                                                                                                                                    </div>
                                                                                                                                                                                ` : ''}
                        </div>
                    `;

                    container.append(messageHtml);
                });

                container.attr('data-id', userId);

                // Cuộn xuống cuối
                container.scrollTop(container[0].scrollHeight);
            });
        }


        function sendMessage() {
            if (!selectedUserId) {
                alert('Vui lòng chọn một liên hệ đầu tiên');
                return;
            }

            const messageText = $('.type_msg').val().trim();
            if (!messageText) return;

            $.ajax({
                url: '{{ route('admin.chats.sendMessage') }}',
                method: 'POST',
                data: {
                    receiver_id: selectedUserId,
                    message: messageText,
                },
                success: function(response) {
                    $('.type_msg').val('');

                    // Thêm tin nhắn mới trực tiếp vào container
                    const message = response.message;
                    const isAdmin = true; // Tin nhắn gửi đi luôn là của admin
                    const avatar = response.avatar || '{{ showImage('') }}';

                    const messageHtml = `
                        <div class="d-flex justify-content-end mb-4">
                            <div class="me-3" style="max-width: 75%">
                                <div class="msg_cotainer_send">
                                    ${messageText}
                                </div>
                                <span class="msg_time_send">${formatDate(new Date())}</span>
                            </div>
                            <div class="img_cont_sharer">
                                <img src="${avatar}" class="rounded-circle user_img_msg">
                            </div>
                        </div>
                    `;

                    const container = $('#messageContainer');
                    container.append(messageHtml);

                    // Cuộn xuống tin nhắn mới nhất
                    container.scrollTop(container[0].scrollHeight);
                }
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);

            // Format: dd/mm/yyyy HH:MM:SS
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // getMonth() trả từ 0-11
            const year = date.getFullYear();

            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }


        function loadMoreUsers() {
            isLoading = true;
            $.ajax({
                url: "{{ route('admin.chats.loadMoreUsers') }}",
                method: 'GET',
                data: {
                    page: currentPage,
                    search: $('#searchInput').val()
                },
                success: function(res) {
                    res.users.forEach(user => {
                        $('#contactList').append(`
                            <li data-user-id="${user.id}">
                                <div class="d-flex align-items-center bd-highlight">
                                    <div class="img_cont">
                                        <img src="${user.img_url}" class="rounded-circle user_img">
                                    </div>
                                    <div class="user_info">
                                        <span>${user.name}</span>
                                        <p class="text-truncate" style="max-width: 200px;">${user.last_message || 'Chưa có tin nhắn'}</p>
                                    </div>
                                    <p class="mb-0 time">${user.last_message_at || ''}</p>
                                </div>
                            </li>
                        `);
                    });

                    currentPage = res.nextPage;
                    hasMorePages = res.hasMorePages;
                    isLoading = false;
                },
                error: function() {
                    console.error("Could not load more users.");
                    isLoading = false;
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        footer {
            display: none
        }

        .container-fluid {
            padding-top: 20px;
            padding-bottom: 20px;
            height: calc(100vh - 60px);
            /* Adjust this value based on your header height */
            overflow: hidden;
        }

        .row {
            height: 100% !important;
            overflow: hidden;
        }

        .chat {
            height: 100%;
            overflow: hidden;
        }

        .card {
            height: 100%;
            border-radius: 10px !important;
            background-color: #ffffff !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .contacts_body {
            padding: 0.75rem 0 !important;
            overflow-y: auto;
            white-space: nowrap;
            flex: 1;
        }

        .msg_card_body {
            overflow-y: auto;
            flex: 1;
        }

        .card-header {
            border-bottom: 0 !important;
        }

        .card-footer {
            border-top: 0 !important;
        }

        .container,
        .container-fluid {
            height: 100%;
            overflow: hidden;
        }

        .input-group-prepend span {
            background-color: #e9ecef !important;
            /* Light grey background */
            border-color: #ced4da !important;
            /* Bootstrap default border color */
            color: #495057;
            /* Dark text color */
            cursor: pointer;
        }

        .input-group-append span {
            color: #007bff;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0 0 0 15px;
        }

        .send_btn {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: none;
            border: none
        }

        .send_btn svg {
            fill: #007bff;
            transition: fill 0.2s ease;
        }

        .send_btn:hover svg {
            fill: #0056b3;
        }

        .type_msg {
            background-color: #ffffff !important;
            border-color: #ced4da !important;
            color: #495057 !important;
            /* overflow-y: auto; */
            /* resize: none; */
            /* height: 40px; */
            padding: 8px 15px;
            line-height: 24px;
        }

        .type_msg:focus {
            outline: 0px !important;
        }

        .attach_btn {
            border-top-left-radius: 15px !important;
            border-bottom-left-radius: 15px !important;
        }

        .search {
            border-top-left-radius: 4px !important;
            /* Match card radius */
            border-bottom-left-radius: 4px !important;
            /* Match card radius */
            background-color: #ffffff !important;
            /* White background */
            border-color: #ced4da !important;
            /* Bootstrap default border color */
            color: #495057 !important;
            /* Dark text color */
        }

        .search:focus {
            outline: 0px !important;
        }

        .contacts {
            list-style: none;
            padding: 0;
        }

        .contacts li {
            width: 100% !important;
            padding: 10px 15px;
            margin-bottom: 10px !important;
            /* Reduced margin */
            border-radius: 5px;
            /* Added border radius */
            cursor: pointer;
        }

        .contacts li:hover {
            background-color: #e9ecef;
            border-radius: 0px;
        }

        .contacts .active {
            background-color: #e9ecef;
            /* Light grey background for active contact */
        }

        .user_img {
            height: 50px;
            width: 50px;
            margin-top: auto;
            margin-bottom: auto;
        }

        .user_img_msg {
            height: 40px;
            width: 40px;
            margin-top: auto;
            margin-bottom: auto;
        }

        .img_cont {
            position: relative;
            height: 50px;
            width: 50px;
        }

        .img_cont_msg {
            height: 40px;
            width: 40px;
        }

        .img_cont_sharer {
            height: 40px;
            width: 40px;
        }

        .online_icon {
            position: absolute;
            height: 15px;
            width: 15px;
            background-color: #4cd137;
            border-radius: 50%;
            bottom: 0.2em;
            right: 0.4em;
            border: 1.5px solid white;
        }

        .offline {
            background-color: #c23616 !important;
        }

        .user_info {
            width: 100%;
            margin-top: auto;
            margin-bottom: auto;
            margin-left: 10px;
        }

        .user_info span {
            font-size: 14px;
            /* Smaller font size */
            color: #495057;
            /* Dark text color */
        }

        .user_info p {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 0;
            /* Muted text color */
        }

        .video_cam {
            margin-left: 50px;
            margin-top: 5px;
            position: absolute;
            left: 10px;
        }

        .video_cam span {
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-right: 20px;
        }

        .msg_cotainer {
            /* Increased margin */
            border-radius: 10px;
            /* Smaller border radius */
            background-color: #e9ecef;
            /* Light grey background */
            padding: 10px 15px;
            /* max-width: 75%; */
            /* Limit message width */
        }

        .msg_cotainer_send {
            /* Increased margin */
            border-radius: 10px;
            /* Smaller border radius */
            background-color: #007bff;
            /* Blue background */
            color: white;
            /* White text */
            padding: 10px 15px;
            /* Adjusted padding */
            /* Limit message width */
        }

        .msg_time {
            /* position: absolute;
                                                                                                                                                                                                        left: 0;
                                                                                                                                                                                                        bottom: -15px; */
            color: #6c757d;
            /* Muted text color */
            font-size: 10px;
        }

        .msg_time_send {
            color: #6c757d;
            /* Lighter muted text color */
            font-size: 10px;
            display: flex;
            justify-content: end;
            margin-top: 3px;
        }

        .msg_head {
            position: relative;
            auto;
        }

        #action_menu_btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 20px;
        }

        .action_menu {
            z-index: 1;
            position: absolute;
            top: 30px;
            right: 10px;
            background-color: #ffffff;
            /* White background */
            color: #495057;
            /* Dark text */
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: none;
        }

        .action_menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .action_menu ul li {
            padding: 7px;
            font-size: 14px;
        }

        .action_menu ul li i {
            padding-right: 10px;
        }

        .action_menu ul li:hover {
            cursor: pointer;
            background-color: #e9ecef;
            /* Light grey hover effect */
        }

        /* Scrollbar styles */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f8f9fa;
            /* Lighter track */
        }

        ::-webkit-scrollbar-thumb {
            background: #adb5bd;
            /* Grey thumb */
            border-radius: 2.5px;
        }

        @media(max-width: 576px) {
            .contacts_card {
                margin-bottom: 15px !important;
            }
        }

        @media(min-width: 1200px) {
            #chat-container {
                height: 93% !important;
            }
        }

        @media(min-width: 1800px) {
            #chat-container {
                height: 95% !important;
            }
        }
    </style>
@endpush
