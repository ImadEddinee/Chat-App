<div>
    @if ($selectedConversation)
        <div class="chatbox_header">
            <div class="return">
                <i class="bi bi-arrow-left"></i>
            </div>
            <div class="img_container">
                <img src="https://ui-avatars.com/api/?name={{ $receiverInstance->name }}" alt="">
            </div>
            <div class="name fw-light fs-5 ms-3 text-capitalize text-white">
                {{ $receiverInstance->name }}
            </div>
        </div>
        <div class="chatbox_body">
            @foreach ($messages as $message)
                <div class="msg_body text-white  {{ auth()->id() == $message->sender_id ? 'msg_body_me main-sc' : 'msg_body_receiver bg-re' }}"
                    style="width:80%;max-width:80%;max-width:max-content">
                    {{ $message->body }}
                    <div class="msg_body_footer">
                        <div class="date">
                            {{ $message->created_at->format('m: i a') }}
                        </div>
                        <div class="read">
                            @php
                                  if($message->user->id === auth()->id()){
                                    if($message->read == 0){
                                        echo'<i class="bi bi-check2 status_tick "></i> ';
                                    }
                                    else {
                                        echo'<i class="bi bi-check2-all text-primary  "></i> ';
                                    }
                          }
                            @endphp
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <script>
            // Load more messages when the user scroll to the top
            $(".chatbox_body").on('scroll', function() {
                var top = $('.chatbox_body').scrollTop();
                if (top == 0) {
                    // Wait until user scroll to top then load more messages
                    window.livewire.emit('loadmore');
                }
            });
        </script>
        <script>
            window.addEventListener('updatedHeight', event => {
                let old = event.detail.height;
                let newHeight = $('.chatbox_body')[0].scrollHeight;
                // Move scroll bare where last stopped before update
                let height = $('.chatbox_body').scrollTop(newHeight - old);
                // store the new height
                window.livewire.emit('updateHeight', {
                    height: height,
                });
            });
        </script>
    @else
        <div class="fs-4 text-center text-success  mt-5">
            No conversation selected
        </div>
    @endif
    <script>
        window.addEventListener('rowChatToBottom', event => {
            // Scroll to the last message
            $('.chatbox_body').scrollTop($('.chatbox_body')[0].scrollHeight);

        });
    </script>
    <script>
        // Clear conversation details when it is closed
        $(document).on('click','.return',function(){
            window.livewire.emit('resetComponent');
        });
    </script>
    <script>
        window.addEventListener('markMessageAsRead',event=>{
            // Get messages marked as unread
         var value= document.querySelectorAll('.status_tick');
         value.array.forEach(element, index => {
            element.classList.remove('bi bi-check2');
             // Mark them as read
            element.classList.add('bi bi-check2-all','text-primary');
     });
    });
    </script>
</div>
