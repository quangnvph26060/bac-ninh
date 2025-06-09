 <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="createTicketModalLabel">Create ticket</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <form action="#" method="POST" id="myForm">
                 <div class="modal-body">
                     <div class="mb-3">
                         <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                         <select name="subject_id" id="subject_id" class="form-select">
                             <option value="">Select subject</option>
                             @foreach ($subjects as $id => $title)
                                 <option value="{{ $id }}">{{ $title }}</option>
                             @endforeach
                         </select>
                     </div>

                     <div class="mb-3">
                         <label for="order_id" class="form-label">Order</label>
                         <select name="order_id" id="order_id" class="form-select">
                             <option value="">Select order</option>
                             @foreach ($availableOrders as $availableOrder)
                                 <option value="{{ $availableOrder->id }}">
                                     {{ $availableOrder->order_code }} - {{ $availableOrder->order_name }}
                                 </option>
                             @endforeach
                         </select>
                     </div>

                     <div class="mb-3">
                         <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                         <textarea name="content" id="content" class="form-control" rows="6"></textarea>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="ant-btn ant-btn-default px-3" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="ant-btn ant-btn-primary px-3">Send</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <div class="modal fade" id="ticketDetailModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-xl">
         <div class="modal-content" id="ticket-detail-content">
             <!-- Nội dung chi tiết sẽ được load bằng JS -->
         </div>
     </div>
 </div>

 @push('scripts')
     <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
     <script>
         $(function() {

             ClassicEditor
                 .create(document.querySelector('#content'), {
                     // Không cần uploadUrl khi dùng base64
                 })
                 .then(editor => {
                     // Kích hoạt base64 upload
                     editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                         return new Base64UploadAdapter(loader);
                     };

                     // Nếu cần lấy nội dung khi submit: lưu editor trong window
                     window.editorInstance = editor;
                 })
                 .catch(error => {
                     console.error(error);
                 });


             $('#date-range').daterangepicker({
                 autoUpdateInput: false,
                 locale: {
                     cancelLabel: 'Clear',
                     applyLabel: 'Apply',
                     format: 'DD/MM/YYYY'
                 }
             });

             $('#date-range').on('apply.daterangepicker', function(ev, picker) {
                 $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                     'DD/MM/YYYY'));
                 fetchTicket();
             });

             $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                 $(this).val('');
                 fetchTicket();
             });

             // Gõ tìm kiếm (debounce)
             let debounceTimer;
             $(document).on('input', 'input[name="search"]', function() {
                 clearTimeout(debounceTimer);
                 debounceTimer = setTimeout(() => {
                     fetchTicket();
                 }, 500); // 500ms chờ sau khi ngừng gõ
             });

             let oldStatus = 'all';

             $(document).on('click', '.filter-btn', function(e) {
                 $('.filter-btn').removeClass('active')
                 $(this).addClass('active')

                 if (oldStatus == $('.filter-btn.active').data('status')) return;

                 oldStatus = $('.filter-btn.active').data('status')

                 fetchTicket()
             });

             // Phân trang
             $(document).on('click', '.page-url-link', function(e) {
                 e.preventDefault();
                 const url = $(this).attr('href');
                 if (url) {
                     fetchTicket(url);
                 }
             });

             $(document).on('change', '.per-page-selector', function() {
                 fetchTicket();
             });
         });

         $('#subject').on('change', function() {
             fetchTicket()
         })

         // Gửi AJAX để lọc đơn hàng
         function fetchTicket(url = "{{ route('tickets.index') }}", page = 1) {
             const search = $('input[name="search"]').val();
             const status = $('.filter-btn.active').data('status') || 'all'; // <-- thêm dòng này

             const urlWithParams = new URL(url, window.location.href);
             const searchParams = new URLSearchParams(urlWithParams.search);
             const pageParam = searchParams.get('page') || page;
             const subject = $('#subject').val()

             $.ajax({
                 url: urlWithParams.pathname,
                 method: 'GET',
                 data: {
                     search: search,
                     status: status, // <-- thêm vào đây
                     page: pageParam,
                     subject
                 },
                 beforeSend: () => {
                     $('#ticket-content').hide();
                     $('#loadingOverlay').show();
                 },
                 success: function(response) {
                     $('#ticket-content').html(response.html).fadeIn(200);
                     $('#loadingOverlay').hide();
                 },
                 error: function(xhr) {
                     datgin.error('Đã có lỗi xảy ra. Vui lòng thử lại sau!');
                 },
                 complete: () => {
                     $('#loadingOverlay').hide();
                     $('#ticket-content').show();
                 }
             });
         }

         if (window.location.href.includes('tickets')) {
             fetchTicket();
         }

         $('#myForm').on('submit', function(e) {
             e.preventDefault();

             const content = window.editorInstance.getData();

             let formData = new FormData(this);

             formData.set('content', content);

             $.ajax({
                 url: "/tickets",
                 method: "POST",
                 data: formData,
                 contentType: false,
                 processData: false,
                 beforeSend: () => {
                     $('#loadingOverlay').show();
                 },
                 success: (response) => {
                     if (response.data && response.data.statusCounts) {
                         updateTotalStatus(response)
                     }

                     $('#createTicketModal').modal('hide');
                     fetchTicket()
                     datgin.success(response.message)
                     $('#myForm')[0].reset();
                     window.editorInstance.setData('');
                 },
                 error: (xhr) => {
                     datgin.error(xhr.responseJSON.message || 'something went wrong!')
                 },
                 complete: () => {
                     $('#loadingOverlay').hide();
                 }
             })
         })

         let lastOpenedTicketId = null; // Lưu ID ticket đã mở trước đó

         $(document).on('click', '.btn-view-ticket', function() {
             const ticketId = $(this).data('id');

             // Nếu ticket hiện tại trùng với lần trước → chỉ show modal
             if (ticketId === lastOpenedTicketId) {
                 $('#ticketDetailModal').modal('show');
                 return;
             }

             // Nếu khác ID → gọi AJAX để load nội dung mới
             $.ajax({
                 url: `/tickets/${ticketId}`,
                 method: 'GET',
                 beforeSend: () => {
                     $('#loadingOverlay').show();
                 },
                 success: function(response) {
                     $('#ticket-detail-content').html(response.html);
                     $('#ticketDetailModal').modal('show');

                     // Cập nhật ID ticket đã mở
                     lastOpenedTicketId = ticketId;
                 },
                 error: function() {
                     datgin.error('Không lấy được dữ liệu ticket');
                 },
                 complete: () => {
                     $('#loadingOverlay').hide();
                 }
             });
         });

         $(document).on('click', '.reply', function() {
             const $container = $('#replyFormContainer');

             // Ẩn nút trả lời (hoặc có thể toggle tùy ý)
             $('.reply').hide();

             // Tránh thêm nhiều form
             if ($container.find('.reply-form-block').length > 0) return;

             // Append template
             const $template = $($('#replyFormTemplate').html());
             $container.append($template);

             // Khởi tạo CKEditor sau khi form đã append vào DOM
             ClassicEditor
                 .create(document.querySelector('#replyEditor'), {})
                 .then(editor => {
                     editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                         return new Base64UploadAdapter(loader);
                     };

                     // Gắn global để submit hoặc debug nếu cần
                     window.editorInstance = editor;

                     editor.editing.view.focus();
                 })
                 .catch(error => {
                     console.error(error);
                 });
         });

         $(document).on('submit', '#form-send', function(e) {
             e.preventDefault();

             const message = window.editorInstance.getData();

             let formData = new FormData(this);

             formData.set('message', message);

             $.ajax({
                 url: "/tickets/send",
                 method: "POST",
                 data: formData,
                 contentType: false,
                 processData: false,
                 beforeSend: () => {
                     $('#loadingOverlay').show();
                 },
                 success: (response) => {
                     const html = `
                        <div class="d-flex mb-4 justify-content-end">
                            <div class="d-flex flex-row-reverse align-items-start" style="max-width: 100%;">
                                <div class="ms-2" style="flex: 0 0 48px;">
                                    <img src="{{ showImage(auth('web')->user()->img_url) }}" class="rounded-circle border"
                                        style="width: 48px; height: 48px; object-fit: cover; object-position: center;" />
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="d-block text-end">{{ auth('web')->user()->name }}</strong>
                                    <small class="text-muted d-block text-end">
                                        ${new Date().toLocaleString('vi-VN')}
                                    </small>
                                    <div class="border rounded p-2 message-content bg-light mt-1">
                                        ${message}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                     $('#messagesContainer').append(html);

                     $('#form-send')[0].reset();

                     window.editorInstance.setData('');

                     $('#form-send').closest('.reply-form-block').remove();

                     $('.reply').show();

                     datgin.success(response.message)

                     $('#messagesContainer').scrollTop($('#messagesContainer')[0].scrollHeight);
                 },
                 error: (xhr) => {
                     datgin.error(xhr.responseJSON.message || 'something went wrong!')
                 },
                 complete: () => {
                     $('#loadingOverlay').hide();
                 }
             })
         })

         $(document).on('click', '#close-form-reply', function() {
             $('#replyFormContainer').empty();

             $('.reply').show()
         })

         $(document).on('click', '#closed', function() {
             let ticketId = $(this).closest('[data-ticket-id]').data('ticket-id'); // bạn cần thêm attr này
             $('#close-ticket-id').val(ticketId);
             $('#close_reason').val('');
             // $('#closeTicketModal').modal('show');
         });

         $(document).on('submit', '#form-close-ticket', function(e) {
             e.preventDefault();

             let formData = $(this).serialize();

             $.ajax({
                 url: '/tickets/close',
                 method: 'POST',
                 data: formData,
                 beforeSend: function() {
                     $('#loadingOverlay').show();
                 },
                 success: function(response) {
                     datgin.success(response.message);
                     lastOpenedTicketId = null
                     $('#closeTicketModal').modal('hide');
                     $('#ticketDetailModal').modal('hide');

                     // Cập nhật số lượng trạng thái ticket
                     if (response.data && response.data.statusCounts) {
                         updateTotalStatus(response)
                     }
                     fetchTicket();
                 },
                 error: function(xhr) {
                     datgin.error(xhr.responseJSON.message || 'Lỗi khi đóng ticket!');
                 },
                 complete: function() {
                     $('#loadingOverlay').hide();
                 }
             });
         });

         function updateTotalStatus(response) {
             const counts = response.data.statusCounts;
             const total = response.data.totalCount;

             $('.filter-btn[data-status="all"]').text(`All (${total})`);
             $('.filter-btn[data-status="open"]').text(`Open (${counts.open ?? 0})`);
             $('.filter-btn[data-status="resolving"]').text(
                 `Resolving (${counts.resolving ?? 0})`);
             $('.filter-btn[data-status="resolved"]').text(
                 `Resolved (${counts.resolved ?? 0})`);
             $('.filter-btn[data-status="closed"]').text(`Closed (${counts.closed ?? 0})`);
         }
     </script>
 @endpush
