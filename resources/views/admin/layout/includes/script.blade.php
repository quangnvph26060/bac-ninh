<script src="{{ asset('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/core/bootstrap.min.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/chart.js/chart.min.js') }}"></script>
{{-- <script src="{{ asset('backend/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script> --}}
{{-- <script src="{{ asset('backend/assets/js/plugin/chart-circle/circles.min.js') }}"></script> --}}

<script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/webfont/webfont.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/kaiadmin.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/kaiadmin.js') }}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('backend/assets/js/setting-demo.js') }}"></script>
<script src="{{ asset('backend/assets/js/setting-demo2.js') }}"></script>
<script src="{{ asset('backend/library/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('global/js/toastr.js') }}"></script>
<script src="{{ asset('global/js/validate.js') }}?v={{ filemtime(public_path('global/js/validate.js')) }}"></script>
<script
    src="{{ asset('backend/assets/js/helper.js') }}?v={{ filemtime(public_path('backend/assets/js/helper.js')) }}">
</script>

<script>
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}',
        adminId: {{ auth('admin')->id() ?? 'null' }},
        userId: {{ auth('web')->id() ?? 'null' }}
    };
</script>

@vite('resources/js/app.js')

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            403: function(xhr) {
                if (xhr.getResponseHeader('Content-Type').includes('text/html')) {
                    document.open();
                    document.write(xhr.responseText);
                    document.close();
                }
            }
        }
    });

    class Base64UploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => {
                        resolve({
                            default: reader.result
                        });
                    };
                    reader.onerror = error => reject(error);
                    reader.readAsDataURL(file);
                }));
        }

        abort() {
            // Optional: support cancel upload if needed
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Marquee control
        var marquee = document.getElementById('demoMarquee');
        if (marquee) {
            marquee.addEventListener('mouseenter', function() {
                marquee.stop();
            });
            marquee.addEventListener('mouseleave', function() {
                marquee.start();
            });
        }
    });
</script>
@stack('scripts')
