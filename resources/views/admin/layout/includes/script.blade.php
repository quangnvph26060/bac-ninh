<script src="{{ asset('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/core/bootstrap.min.js') }}"></script>

<script src="{{ asset('backend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/chart-circle/circles.min.js') }}"></script>

{{-- <script src="{{ asset('backend/assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script> --}}
{{-- <script src="{{ asset('backend/assets/js/plugin/jsvectormap/world.js') }}"></script> --}}
<script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/webfont/webfont.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/kaiadmin.min.js') }}"></script>kai
<script src="{{ asset('backend/assets/js/kaiadmin.js') }}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('backend/assets/js/setting-demo.js') }}"></script>
<script src="{{ asset('backend/assets/js/setting-demo2.js') }}"></script>
<script src="{{ asset('backend/library/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('backend/assets/js/helper.js') }}"></script>


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
