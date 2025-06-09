<div class="modal-header border-bottom">
    <h5 class="modal-title fw-bold text-center w-100">T{{ $ticket->code }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="d-flex gap-3 mb-3">

        {{-- THÔNG TIN CHUNG --}}
        <div class="w-50 border shadow-sm">
            <div class="p-3" style="background-color: #e4e9f2">
                <strong class="text-secondary">THÔNG TIN CHUNG</strong>
            </div>

            <div class="p-3">
                @php
                    $statusLabels = [
                        'open' => [
                            'label' => 'Chưa Giải Quyết',
                            'class' => 'badge rounded-pill text-bg-warning text-dark',
                        ],
                        'resolving' => [
                            'label' => 'Đang Xử Lý',
                            'class' => 'badge rounded-pill text-bg-info text-dark',
                        ],
                        'resolved' => ['label' => 'Đã Giải Quyết', 'class' => 'badge rounded-pill text-bg-success'],
                        'closed' => ['label' => 'Đã Đóng', 'class' => 'badge rounded-pill text-bg-secondary'],
                    ];

                    $status = $ticket->status ?? 'closed';
                    $label = $statusLabels[$status]['label'] ?? ucfirst($status);
                    $badgeClass = $statusLabels[$status]['class'] ?? 'badge rounded-pill text-bg-light text-dark';
                @endphp

                <p class="mb-2">
                    <strong>Trạng thái:</strong>
                    <span class="{{ $badgeClass }}">{{ $label }}</span>
                </p>

                <p class="mb-2"><strong>Tiêu đề:</strong> {{ $ticket->subject->title ?? 'N/A' }}</p>

                @if ($ticket->reason)
                    <p class="mb-2"><strong>Lý do:</strong> {{ $ticket->reason }}</p>
                @endif

                <p class="mb-0">
                    <strong>ID Đơn hàng:</strong>
                    <span class="text-warning fw-semibold">{{ $ticket->order->order_code ?? 'N/A' }}</span>
                </p>
            </div>
        </div>

        {{-- ĐÁNH GIÁ DỊCH VỤ --}}
        <div class="w-50 border shadow-sm">
            <div class="p-3" style="background-color: #e4e9f2">
                <strong class="text-secondary">ĐÁNH GIÁ DỊCH VỤ</strong>
            </div>

            <div class="p-3">
                <p class="mb-2"><strong>Đánh giá:</strong> {!! $ticket->rating ?? '<span class="text-muted">N/A</span>' !!}</p>
                <p class="mb-0"><strong>Mô tả:</strong> {!! $ticket->feedback ?? '<span class="text-muted">N/A</span>' !!}</p>
            </div>
        </div>

    </div>


    <div id="messagesContainer" class="bg-white shadow-sm border" style="overflow-y: auto; max-height: 60vh;">
        <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-2" style="background-color: #e4e9f2">
            <strong class="text-secondary">TICKET CONTENT</strong>

            <div data-ticket-id="{{ $ticket->id }}">
                @if ($ticket->is_confirmed)
                    <!-- Button trigger modal -->
                    <button class="ant-btn ant-btn-default px-3 text-f06022 border-f06022" data-bs-toggle="modal"
                        data-bs-target="#ratingModal">
                        Resolved
                    </button>
                @endif
                @if ($ticket->status !== 'closed' && $ticket->status !== 'resolved')
                    <button class="ant-btn ant-btn-primary px-3 reply"><i class="bi bi-send me-2"></i> Reply</button>
                    <button class="ant-btn ant-btn-default px-3" data-bs-toggle="modal" data-bs-target="#closeTicketModal" id="closed">Close</button>
                @endif
            </div>
        </div>

        @foreach ($ticket->messages as $message)
            @php
                $isUser = $message->sender_type === 'App\Models\User' || $message->sender_type === 'user';
                $sender = $message->sender; // Đây là quan hệ polymorphic
                $avatar = showImage($sender->img_url ?? $sender->avatar); // giả sử avatar lưu trong cột avatar
                $name = $sender->name ?? $sender->full_name;
            @endphp

            <div class="d-flex mb-4 px-3 {{ $isUser ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="d-flex {{ $isUser ? 'flex-row-reverse' : '' }} align-items-start" style="max-width: 100%;">
                    {{-- Avatar --}}
                    <div class="{{ $isUser ? 'ms-2' : 'me-2' }}" style="flex: 0 0 48px;">
                        <img src="{{ $avatar }}" class="rounded-circle border"
                            style="width: 48px; height: 48px; object-fit: cover; object-position: center;"
                            alt="Avatar">
                    </div>

                    {{-- Nội dung tin nhắn --}}
                    <div class="flex-grow-1">
                        <strong class="d-block text-{{ $isUser ? 'end' : 'start' }}">{{ $name }}</strong>
                        <small class="text-muted d-block text-{{ $isUser ? 'end' : 'start' }}">
                            {{ $message->created_at->format('d-m-Y H:i:s') }}
                        </small>
                        <div class="border rounded p-2 message-content bg-light mt-1">
                            {!! $message->message !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div id="replyFormContainer"></div>

        @if ($ticket->messages->isEmpty())
            <p class="text-muted fst-italic">No message content yet.</p>
        @endif
    </div>
</div>

<template id="replyFormTemplate">
    <div class="d-flex mt-4 reply-form-block p-3" style="border: 1px solid #f26722;">
        <div style="flex: 0 0 48px;" class="me-2">
            <img src="{{ showImage(auth('web')->user()->img_url) }}" class="rounded-circle border"
                style="width: 48px; height: 48px; object-fit: cover; object-position: center;" />
        </div>
        <div class="flex-grow-1">
            <strong class="d-block text-start">{{ auth('web')->user()->name }}</strong>
            <small class="text-muted d-block text-start mb-2">
                {{ auth('web')->user()->email }}
            </small>
            <form method="POST" action="" id="form-send">
                <input type="hidden" value="{{ $ticket->id }}" name="ticket_id">
                <textarea id="replyEditor" name="message" class="form-control" rows="5"></textarea>
                <div class="text-end mt-2">
                    <button type="button" class="ant-btn ant-btn-default px-3" id="close-form-reply">Close</button>
                    <button type="submit" class="ant-btn ant-btn-primary px-3"><i class="bi bi-send me-2"></i>
                        Reply</button>
                </div>
            </form>
        </div>
    </div>
</template>
