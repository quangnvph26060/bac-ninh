@if ($type !== 'textarea')
    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
        class="form-control {{ $class }}" placeholder="{{ $placeholder }}" value="{{ $value }}"
        data-rules="{{ $rules }}" data-attribute="{{ $attribute }}" @disabled($disabled)>
@else
    <textarea id="{{ $id }}" name="{{ $name }}" class="form-control {{ $class }}"
        placeholder="{{ $placeholder }}" rows="{{ $rows }}" data-rules="{{ $rules }}"
        data-attribute="{{ $attribute }}" @disabled($disabled)>{!! $value !!}</textarea>
@endif

<small class="text-danger error-message"></small>

@push('styles')
    <style>
        .error-message {
            display: none;
            margin-top: 5px;
            font-weight: bolder;
        }
    </style>
@endpush
