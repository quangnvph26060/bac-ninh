@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $items = [
                    ['name' => 'ticket', 'url' => route('admin.tickets.index')],
                    ['name' => "chi tiết ticket - $ticket->code"],
                ];
            @endphp
            <x-breadcrumb :items="$items" />
        </div>

        <!-- Card Thông tin ticket -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    @php
                        $badgeClass = match ($ticket->status) {
                            'open' => 'success',
                            'resolving' => 'primary',
                            'resolved' => 'info',
                            'closed' => 'secondary',
                            default => 'light',
                        };
                    @endphp

                    <div class="card-header bg-light d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Ticket #{{ $ticket->code }}</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span id="ticket-status-badge" class="badge badge-{{ $badgeClass }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                            @php
                                $statuses = ['open', 'resolving', 'resolved', 'closed'];
                                $currentIndex = array_search($ticket->status, $statuses);
                            @endphp

                            <select id="ticket-status-select" name="status" class="form-select form-select-sm me-2"
                                style="width:120px;">
                                @foreach ($statuses as $index => $status)
                                    <option value="{{ $status }}" {{ $ticket->status == $status ? 'selected' : '' }}
                                        {{ $index < $currentIndex ? 'disabled' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Lý do:</strong> {!! $ticket->reason ?? '<span class="text-muted">N/A</span>' !!}
                                </p>
                                <p class="mb-2"><strong>Chủ đề:</strong> {!! $ticket->subject->title ?? '<span class="text-muted">N/A</span>' !!}</p>
                                <p class="mb-2"><strong>Mã đơn hàng:</strong> {{ $ticket->order->order_code }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Ngày tạo:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p class="mb-2"><strong>Đánh giá:</strong> {!! $ticket->rating ?? '<span class="text-muted">N/A</span>' !!} <i class="fas fa-star"
                                        style="color: #FFD43B;"></i></p>
                                <p class="mb-2"><strong>Nhận xét:</strong> {!! $ticket->feedback ?? '<span class="text-muted">N/A</span>' !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Chat -->
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="min-height: 550px; display: flex; flex-direction: column;">
                    <div class="card-body p-0 d-flex flex-column" style="height: 550px;">
                        <!-- Khung tin nhắn -->
                        <div class="messages-container flex-grow-1 p-4" style="overflow-y: auto; background: #f5f6fa;">
                            @foreach ($ticket->messages as $message)
                                @php
                                    $isUser = $message->sender_type === 'App\\Models\\User';
                                    $avatar = showImage($message->sender->img_url ?? $message->sender->avatar);
                                    $name = $message->sender->name ?? $message->sender->full_name;
                                @endphp
                                <div class="d-flex mb-4 {{ $isUser ? '' : 'flex-row-reverse' }}">
                                    <img src="{{ $avatar }}" alt="avatar"
                                        class="rounded-circle  {{ $isUser ? 'me-3' : 'ms-3' }}"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    <div style="max-width: 100%;"
                                        class="d-flex flex-column {{ $isUser ? 'justify-content-start' : 'justify-content-end' }}">
                                        <div class="">
                                            <p class="fw-bold mb-0 {{ $isUser ? '' : 'd-flex justify-content-end' }}"
                                                style="font-size: 15px;">
                                                {{ $name }}</p>
                                            <span class="text-muted"
                                                style="font-size: 12px;">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if (isset($message->image))
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $message->image) }}" alt="image"
                                                    style="max-width: 100%; border-radius: 10px;">
                                            </div>
                                        @endif
                                        @if ($message->message)
                                            <div class="chat-bubble mt-2 {{ $isUser ? 'left' : 'right' }}">
                                                {!! $message->message !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Form gửi tin nhắn -->
                        <div class="chat-input pt-3 border-top bg-white">
                            <!-- Form gửi tin nhắn -->
                            <form id="chat-form" method="POST" enctype="multipart/form-data"
                                class="border-top pt-3 bg-white">

                                <!-- Xem trước ảnh -->
                                <div id="preview-images" class="d-flex flex-wrap gap-2 mt-3"></div>

                                <!-- Wrapper flex -->
                                <div class="d-flex flex-wrap align-items-end gap-3">
                                    <!-- Textarea người nhập -->
                                    <div class="flex-grow-1" style="min-width: 200px;">
                                        <textarea id="text-input" rows="1" class="form-control" placeholder="Nhập tin nhắn..."
                                            style="resize: vertical; overflow-y: auto; max-height: 150px;"></textarea>

                                    </div>

                                    <!-- Nút chọn ảnh + gửi -->
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="file" name="images[]" accept="image/*" multiple class="d-none"
                                            id="chat-image-input">
                                        <label for="chat-image-input" class="mb-0" style="cursor:pointer;">
                                            <i class="fas fa-image fa-2xl text-secondary"></i>
                                        </label>

                                        <button type="submit" class="btn btn-primary rounded-circle"
                                            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Hidden field để gửi nội dung thực -->
                                <input type="hidden" name="message" id="message">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            const $textInput = $('#text-input');
            const $preview = $('#preview-images');
            const $hiddenMessage = $('#message');
            const $form = $('#chat-form');
            const $submitBtn = $form.find('button[type="submit"]');
            const $messagesContainer = $('.messages-container');

            let base64Images = [];
            const maxImages = 5;
            const maxSizeMB = 2;

            $textInput.on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault(); // Ngăn xuống dòng
                    $form.trigger('submit'); // Gọi sự kiện submit
                }
            });

            const statusColors = {
                open: 'success',
                resolving: 'primary',
                resolved: 'info',
                closed: 'secondary',
            };

            let previousStatus = $('#ticket-status-select').val();

            $('#ticket-status-select').on('change', function() {
                const newStatus = $(this).val();

                Swal.fire({
                    title: "Bạn có chắc chắn muốn cập nhật trạng thái?",
                    text: "Hành động này sẽ không thể hoàn tác!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Đồng ý, cập nhật!",
                    cancelButtonText: "Hủy",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/tickets/update-status',
                            method: 'PATCH',
                            data: {
                                status: newStatus,
                                ticketId: "{{ $ticket->id }}"
                            },
                            success: function(res) {
                                datgin.success(res.message);

                                if (newStatus === 'resolved') {
                                    // KHÔNG cập nhật giao diện ngay nếu đang chờ khách xác nhận
                                    // Quay lại trạng thái trước để giữ đúng status cho khách
                                    $('#ticket-status-select').val(previousStatus);
                                    return;
                                }

                                // Với các trạng thái khác, cập nhật giao diện bình thường
                                const badgeText = newStatus.charAt(0).toUpperCase() +
                                    newStatus.slice(1);
                                const badgeClass = statusColors[newStatus] || 'light';

                                $('#ticket-status-badge')
                                    .attr('class', `badge badge-${badgeClass}`)
                                    .text(badgeText);

                                // Disable các option thấp hơn
                                const $options = $('#ticket-status-select option');
                                $options.each(function(index) {
                                    $(this).prop('disabled', index <= $options
                                        .index($options.filter(
                                            `[value="${newStatus}"]`)));
                                });

                                previousStatus = newStatus;
                            },
                            error: function(xhr) {
                                datgin.error(xhr.responseJSON.message);
                                $('#ticket-status-select').val(previousStatus);
                            }
                        });
                    } else {
                        $('#ticket-status-select').val(previousStatus);
                    }
                });
            });

            // Chọn ảnh
            $('#chat-image-input').on('change', function() {
                const files = Array.from(this.files);

                if (base64Images.length + files.length > maxImages) {
                    alert(`Chỉ được chọn tối đa ${maxImages} ảnh.`);
                    return $(this).val('');
                }

                files.forEach(file => {
                    const sizeMB = file.size / (1024 * 1024);
                    if (sizeMB > maxSizeMB) {
                        alert(`Ảnh "${file.name}" vượt quá ${maxSizeMB}MB.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const base64 = e.target.result;
                        base64Images.push(base64);

                        const $wrapper = $(`
                            <div class="position-relative me-2 mb-2">
                                <img src="${base64}" style="max-width: 120px; border-radius: 6px;">
                                <button type="button" class="remove-img"
                                    style="position:absolute;top:0;right:0;background:rgba(0,0,0,0.6);color:#fff;
                                        border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;">×</button>
                            </div>
                        `);

                        $wrapper.find('.remove-img').on('click', function() {
                            const index = base64Images.indexOf(base64);
                            if (index > -1) base64Images.splice(index, 1);
                            $wrapper.remove();
                        });

                        $preview.append($wrapper);
                    };
                    reader.readAsDataURL(file);
                });

                $(this).val('');
            });

            // Gửi tin nhắn
            $form.on('submit', function(e) {
                e.preventDefault();

                if (!$('#text-input').val().trim()) {
                    datgin.error('Tin nhắn không được để trống!');
                    return
                }

                if ($submitBtn.prop('disabled')) return;

                let finalHtml = '';
                base64Images.forEach(base64 => {
                    finalHtml += `
                        <figure class="image">
                            <img src="${base64}" alt="">
                        </figure>
                    `;
                });

                finalHtml += `<p>${$textInput.val().replace(/\n/g, '<br>')}</p>`;
                $hiddenMessage.val(finalHtml);

                $submitBtn.prop('disabled', true); // disable nút gửi

                $.ajax({
                    url: '/admin/tickets/send-message', // sửa lại nếu cần
                    method: 'POST',
                    data: {
                        message: finalHtml,
                        ticket_id: "{{ $ticket->id }}"
                    },
                    success: function(res) {
                        // Reset form
                        $textInput.val('').trigger('input');
                        $preview.empty();
                        $hiddenMessage.val('');
                        $submitBtn.prop('disabled', false);

                        // Thêm tin nhắn mới vào cuối
                        const currentTime = new Date().toLocaleString('vi-VN');
                        const newMessage = $(`
                            <div class="d-flex mb-4 flex-row-reverse">
                                <img src="{{ showImage(auth('admin')->user()->avatar) }}" alt="avatar"
                                    class="rounded-circle ms-3"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div class="d-flex flex-column justify-content-end" style="max-width: 100%;">
                                    <div>
                                        <p class="fw-bold mb-0 d-flex justify-content-end" style="font-size: 15px;">{{ auth('admin')->user()->full_name }}</p>
                                        <span class="text-muted" style="font-size: 12px;">{{ now()->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="chat-bubble mt-2 right">${finalHtml}</div>
                                </div>
                            </div>
                        `);

                        $messagesContainer.append(newMessage);

                        // Cuộn xuống cuối
                        $messagesContainer.scrollTop($messagesContainer[0].scrollHeight);
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false);
                        datgin?.error(xhr.responseJSON?.message || 'Gửi thất bại');
                        $preview.empty();
                        $form[0].reset();
                    },
                    complete: () => {
                        base64Images = [];
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .chat-bubble {
            padding: 5px 12px;
            border-radius: 18px;
            display: inline-block;
            font-size: 15px;
            line-height: 1.5;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            word-break: break-word;
        }

        .chat-bubble.left {
            background: #fff;
            color: #222;
            border-bottom-left-radius: 4px;
        }

        .chat-bubble.right {
            background: #007bff;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: #d1d1d1;
            border-radius: 3px;
        }

        figure.image {
            justify-content: center;
            display: flex
        }

        .messages-container img {
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            /* nếu muốn bo tròn ảnh */
            display: block;
        }

        .messages-container p {
            margin-bottom: 0;
        }
    </style>
@endpush
