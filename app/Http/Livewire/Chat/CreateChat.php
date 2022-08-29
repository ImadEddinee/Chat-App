<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Livewire\Component;

class CreateChat extends Component
{
    public $users = [];
    public $message= 'hello how are you';
    public $name;

    public function searchUser(){
        if ($this->name == ""){
            // clear search result if input field empty
            $this->users = [];
        }else{
            $search = $this->name . "%";
            $this->users = User::where('name', 'LIKE', $search)
                ->where('id', '!=', auth()->user()->id)->get();
        }
    }

    public function checkconversation($receiverId)
    {
        // check if the two users doesn't already have a conversation
        // with each other
        $checkedConversation = Conversation::where('receiver_id', auth()->user()->id)
            ->where('sender_id', $receiverId)->orWhere('receiver_id', $receiverId)
            ->where('sender_id', auth()->user()->id)->get();

        if (count($checkedConversation) == 0) {
            // If not create a new conversation
            $current_date_time = Carbon::now()->toDateTimeString();
            $createdConversation= Conversation::create(['receiver_id'=>$receiverId,
                'sender_id'=>auth()->user()->id,
                'last_time_message'=>$current_date_time]);

            $createdMessage= Message::create(['conversation_id'=>$createdConversation->id,
                'sender_id'=>auth()->user()->id,'receiver_id'=>$receiverId,
                'body'=>$this->message]);

            $createdConversation->last_time_message= $createdMessage->created_at;
            $createdConversation->save();
        }
    }
    public function render()
    {
        return view('livewire.chat.create-chat');
    }
}
