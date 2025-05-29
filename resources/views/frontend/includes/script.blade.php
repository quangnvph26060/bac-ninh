<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="{{ asset('frontend/assets/js/script.js') }}"></script>

<script src="{{ asset('frontend/assets/js/chat.js') }}?v={{ filemtime(public_path('frontend/assets/js/chat.js')) }}">
</script>

<script>
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}',
        adminId: {{ auth('admin')->id() ?? 'null' }},
        userId: {{ auth('web')->id() ?? 'null' }}
    };
</script>

@vite('resources/js/app.js')

@stack('scripts')
