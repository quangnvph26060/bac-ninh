@if ($photos->isNotEmpty())
    @foreach ($photos as $photo)
        <div class="col-md-3 col-sm-4 col-6 mb-4">
            <div class="artwork-card position-relative border rounded overflow-hidden">

                {{-- Checkbox hoặc Radio --}}
                @if ($type === 'checkbox')
                    <input type="checkbox" id="{{ $photo->id }}" value="{{ $photo->id }}"
                        class="form-check-input position-absolute top-0 start-0 m-2 z-2">
                @else
                    <input type="radio" name="selected_photo" id="{{ $photo->id }}" value="{{ $photo->id }}"
                        class="form-check-input position-absolute top-0 start-0 m-2 z-2">
                @endif

                {{-- Label và ảnh --}}
                <label for="{{ $photo->id }}" class="cursor w-100">
                    <div class="artwork-image-wrapper position-relative">
                        <img src="{{ showImage($photo->path) }}" class="artwork-image" alt="Artwork">
                        <div class="artwork-overlay">
                            <div class="overlay-content text-white text-start p-2">
                                <div><strong>Width:</strong> {{ $photo->width }}px</div>
                                <div><strong>Height:</strong>{{ $photo->height }}px</div>
                                <div><strong>PPI:</strong> {{ $photo->ppi }}</div>
                                <div><strong>Format:</strong> {{ $photo->format }}</div>
                            </div>
                        </div>
                    </div>
                </label>

                {{-- Tên ảnh --}}
                <div class="artwork-name text-center py-2 fw-semibold text-truncate">
                    {{ $photo->name }}
                </div>
            </div>
        </div>
    @endforeach

    {{-- Pagination --}}
    {{ $photos->links('vendor.pagination.custom') }}
@else
    <p class="text-center text-muted">There are no photos</p>
@endif
