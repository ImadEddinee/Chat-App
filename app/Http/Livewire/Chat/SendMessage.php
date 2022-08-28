<?php

namespace App\Http\Livewire\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SendMessage extends Component
{
    public $selectedConversation;
    public $receiverInstance;
    public $body;
    public $createdMessage;
    protected $listeners = ['updateSendMessage', 'dispatchMessageSent',
                        'resetComponent'];

    public function resetComponent()
    {
        // Clear conversation details when it is closed
      $this->selectedConversation= null;
      $this->receiverInstance= null;
    }

    function updateSendMessage(Conversation $conversation, User $receiver)
    {
        // When the use select a conversation this method receives the
        // selected conversation and receiver
        $this->selectedConversation = $conversation;
        $this->receiverInstance = $receiver;
    }

    public function sendMessage()
    {
        if ($this->body == null) {
            // Check if input is empty
            return null;
        }
        $this->createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverInstance->id,
            'body' => $this->body,
        ]);

        $this->selectedConversation->last_time_message = $this->createdMessage->created_at;
        $this->selectedConversation->save();

        // push the message in the messages array
        $this->emitTo('chat.chatbox', 'pushMessage', $this->createdMessage->id);

        // update conversation list with the last message and time
        $this->emitTo('chat.chat-list', 'refresh');

        // clear input field
        $this->reset('body');
        // Send the message to the receiver
        $this->emitSelf('dispatchMessageSent');
    }

    public function dispatchMessageSent()
    {
        broadcast(new MessageSent(Auth()->user(), $this->createdMessage, $this->selectedConversation, $this->receiverInstance));
    }

    public function render()
    {
        return view('livewire.chat.send-message');
    }
}
