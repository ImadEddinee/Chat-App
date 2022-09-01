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
    public $result = [];
    public $name;
    public $conversationAddedMessage = false;

    public function searchUser(){
        $this->users = [];
        $this->conversationAddedMessage = false;
        if ($this->name == ""){
            // clear search result if input field empty
            $this->users = [];
        }else{
            $search = $this->name . "%";
            $this->result = User::where('name', 'LIKE', $search)
                ->where('id', '!=', auth()->user()->id)->get();
            foreach ($this->result as $user){
                if ($this->checkIfConversationExists($user->id)){
                    // If the two users don't already have a conversation
                    // add to the search result
                     $this->users[] = $user;
                }
            }
        }

    }

    private function checkIfConversationExists($receiverId){
        // check if the two users doesn't already have a conversation
        // with each other
        $checkedConversation = Conversation::where('receiver_id', auth()->user()->id)
            ->where('sender_id', $receiverId)->orWhere('receiver_id', $receiverId)
            ->where('sender_id', auth()->user()->id)->get();
        if (count($checkedConversation) == 0){
            return true;
        }else{
            return false;
        }
    }

    public function createConversation($receiverId)
    {
            // Create a new conversation
        $createdConversation= Conversation::create(['receiver_id'=>$receiverId,
                'sender_id'=>auth()->user()->id]);

        $createdConversation->save();
        // Clear search result
        $this->users = [];
        $this->conversationAddedMessage = true;
        // Clear search input
        $this->name = "";
    }
    public function render()
    {
        return view('livewire.chat.create-chat');
    }
}
