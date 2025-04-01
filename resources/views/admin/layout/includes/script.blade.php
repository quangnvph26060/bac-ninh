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
<script src="{{ asset('backend/assets/js/kaiadmin.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/kaiadmin.js') }}"></script>
<script src="{{ asset('backend/assets/js/setting-demo.js') }}"></script>
<script src="{{ asset('backend/assets/js/setting-demo2.js') }}"></script>
<script src="{{ asset('backend/library/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('backend/assets/js/helper.js') }}"></script>
{{-- <script src="{{ asset('backend/assets/js/demo.js') }}"></script> --}}

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@stack('scripts')
