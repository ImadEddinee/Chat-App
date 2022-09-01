<div>
    <div class="chatlist_header">
        <div class="title text-white">
            Chat
        </div>
        <div class="img_container">
            <img src="https://ui-avatars.com/api/?background=5A7159&color=fff&name={{auth()->user()->name}}" alt="">
        </div>
    </div>
    <div class="chatlist_body">
        <form wire:submit.prevent='searchConversation' action="">
            <div class="form-floating form-s">
                <input type="text"  wire:model='name' class=" bg-dark text-white form-control" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput" class="text-white">
                    <i class="bi bi-search"></i> Search for users...
                </label>
                <button type="submit"></button>
            </div>
        </form>
        @if (count($conversations) > 0)
            @foreach ($conversations as $conversation)
                <div class="chatlist_item main-sc" wire:key='{{$conversation->id}}'
                     wire:click="$emit('chatUserSelected', {{$conversation}},{{$this->getChatUserInstance($conversation, $name = 'id') }})">
                    <div class="chatlist_img_container">
                        <img
                            src="https://ui-avatars.com/api/?name={{$this->getChatUserInstance($conversation, $name = 'name')}}"
                            alt="">
                    </div>
                    <div class="chatlist_info">
                        <div class="top_row">
                            <div
                                class="list_username text-capitalize text-white">{{ $this->getChatUserInstance($conversation, $name = 'name') }}
                            </div>
                            <span class="date text-white">
                                {{ $conversation->messages->last()?->created_at->shortAbsoluteDiffForHumans() !== null }}</span>
                        </div>
                        <div class="bottom_row">
                            <div class="message_body text-white text-truncate">
                                {{ $conversation->messages->last()?->body }}
                            </div>
                            @php
                                if(count($conversation->messages->where('read',0)->where('receiver_id',Auth()->user()->id))){
                             echo ' <div class="unread_count badge rounded-pill text-light bg-success">  '
                                 . count($conversation->messages->where('read',0)->where('receiver_id',Auth()->user()->id)) .'</div> ';
                                }
                            @endphp
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-success text-center mt-8">
                you have no conversations
            </div>
        @endif
    </div>
</div>
<script>
    // Send request to search for a user whenever a new letter is entered
    $(function () {
        $("form").each(function () {
            $(this)
                .find("input")
                .keyup(function (e) {
                    $("button").click()
                });
        });
    });
</script>
