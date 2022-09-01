<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\User;
use Livewire\Component;
use function PHPUnit\Framework\at;

class ChatList extends Component
{

    public $auth_id;
    public $conversations ;
    public $receiverInstance;
    public $name;
    public $selectedConversation;

    protected $listeners= ['chatUserSelected','refresh'=>'$refresh',
                'resetComponent'];

    public function searchConversation(){
        if ($this->name != ''){
            $this->auth_id = auth()->id();
            // Get the list of user's conversations
            $tconversations = Conversation::where('sender_id', $this->auth_id)
                ->where('last_time_message', '=', null)->get();
            foreach ($tconversations as $cn){
                // Get username from the conversation
                $userName = $this->getChatUserInstance($cn, 'name');
                if (strpos($userName, $this->name) !== false){
                    // If username contains the input value add to search result
                    $this->conversations->prepend($cn);
                }
            }
        }else{
            $this->mount();
        }
    }


     public function chatUserSelected(Conversation $conversation,$receiverId)
     {
      //  dd($conversation,$receiverId);
      $this->selectedConversation= $conversation;
      $receiverInstance= User::find($receiverId);

      // load messages of the conversation
      $this->emitTo('chat.chatbox','loadConversation',
          $this->selectedConversation,$receiverInstance);

      // send details of the selected conversation to the other component
      $this->emitTo('chat.send-message','updateSendMessage',
          $this->selectedConversation,$receiverInstance);
     }

    public function getChatUserInstance(Conversation $conversation, $request)
    {
        // This method helps to get the other user in a conversation
        // that the (auth) user is chatting with, then get a field on that user
        $this->auth_id = auth()->id();
        if ($conversation->sender_id == $this->auth_id) {
            $this->receiverInstance = User::firstWhere('id', $conversation->receiver_id);
        } else {
            $this->receiverInstance = User::firstWhere('id', $conversation->sender_id);
        }
        if (isset($request)) {
            return $this->receiverInstance->$request;
        }
    }

    public function resetComponent()
    {
        // Clear conversation details when it is closed
        $this->selectedConversation= null;
        $this->receiverInstance= null;
    }

    public function mount()
    {
        $this->auth_id = auth()->id();
        // Get the list of user's conversations
        $this->conversations = Conversation::where('sender_id', $this->auth_id)
            ->where('last_time_message', '!=', null)
            ->orWhere('receiver_id', $this->auth_id)
            ->where('last_time_message', '!=', null)
            ->orderBy('last_time_message', 'DESC')->get();
    }

    public function render()
    {
        return view('livewire.chat.chat-list');
    }
}
