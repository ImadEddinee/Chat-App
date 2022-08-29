<div >
    <div class="chat_container main-con" >
        <div class="chat_list_container main-con" >
            @livewire('chat.chat-list')
        </div>
        <div class="chat_box_container main-con">
            @livewire('chat.chatbox')
            @livewire('chat.send-message')
        </div>
    </div>
    <script>
        window.addEventListener('chatSelected', event => {
            if (window.innerWidth < 768) {
                $('.chat_list_container').hide();
                $('.chat_box_container').show();
            }
            // Scroll to the last message
            $('.chatbox_body').scrollTop($('.chatbox_body')[0].scrollHeight);
            // Get the current height of the chatbox
            let height= $('.chatbox_body')[0].scrollHeight;
            // Update the height when more messages are loaded
            window.livewire.emit('updateHeight',{
                height:height,
             });
        });
        $(window).resize(function() {
            if (window.innerWidth > 768) {
                $('.chat_list_container').show();
                $('.chat_box_container').show();
            }
        });
        $(document).on('click', '.return', function() {
            $('.chat_list_container').show();
            $('.chat_box_container').hide();
        });
    </script>
    <script>
        // Load more messages when the user scroll to the top
        $(document).on('scroll','#chatBody',function() {
            var top = $('.chatbox_body').scrollTop();
            if (top == 0) {
                // Wait until user scroll to top then load more messages
                window.livewire.emit('loadmore');
            }
        });
    </script>
</div>
