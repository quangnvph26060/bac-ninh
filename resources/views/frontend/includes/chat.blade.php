 <!-- Chat Toggle Button -->
 @if (auth('web')->check())
     <button id="chat-button">
         <span id="chat-icon"><i class="bi bi-chat-left-dots"></i></span>
     </button>
 @endif


 <!-- Chat Popup -->
 <div id="chat-popup" class="flex-column" style="max-height: 450px; display: none;">
     <div class="chat-header d-flex justify-content-center align-items-center">
         <div class="text-center">
             <p class="fs-5 mb-0">Artyland Fulfillment</p>
             <span>Hỗ trợ khách hàng 24/7</span>
         </div>
     </div>
     <div class="chat-messages" id="chat-messages" style="max-height: 400px; overflow-y: auto;">
         {{-- <div class="text-center text-muted small my-1">
             Thứ ba, 27 Tháng năm
         </div> --}}
     </div>
     <form id="chat-form" class="chat-input">
         <div class="input-group">
             <textarea id="chat-input" class="form-control" rows="1" placeholder="Nhập tin nhắn..."></textarea>
             <button class="btn" style="background-color: #f26722; color: white" type="submit">
                 <i class="bi bi-send"></i>
             </button>
         </div>
     </form>
 </div>
