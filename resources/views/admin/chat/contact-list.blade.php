@if ($users->isEmpty())
    <div class="text-center text-muted py-4">
        Không tìm thấy người dùng nào phù hợp.
    </div>
@else
    <ul class="contacts" id="contactList">
        @foreach ($users as $user)
            <li data-user-id="{{ $user->id }}">
                <div class="d-flex align-items-center bd-highlight">
                    <div class="img_cont">
                        <img src="{{ showImage($user->img_url) }}" class="rounded-circle user_img">
                    </div>
                    <div class="user_info">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $user->name }}</span>
                            <small class="mb-0 time text-muted">
                                {{ $user->last_message_at ? \Carbon\Carbon::parse($user->last_message_at)->diffForHumans() : '' }}
                            </small>
                        </div>
                        <p class="text-truncate {{ $user->is_read ? 'fw-bold' : '' }}" style="max-width: 230px;">
                            {{ $user->last_message ?? 'Chưa có tin nhắn' }}
                        </p>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif
