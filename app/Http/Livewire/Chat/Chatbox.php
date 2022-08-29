<?php

namespace App\Http\Livewire\Chat;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Livewire\Component;


class Chatbox extends Component
{

    public $selectedConversation;
    public $receiver;
    public $messages;
    public $paginateVar = 10;
    public $height;

    public function  getListeners()
    {
        $auth_id = auth()->user()->id;
        return [
            "echo-private:chat.{$auth_id},MessageSent" => 'broadcastedMessageReceived',
            "echo-private:chat.{$auth_id},MessageRead" => 'broadcastedMessageRead',
            'loadConversation', 'pushMessage', 'loadmore',
            'updateHeight','broadcastMessageRead','resetComponent'
        ];
    }


    public function broadcastedMessageRead($event)
    {
        // Check if the user is selecting a conversation
        if($this->selectedConversation){
            //Check if current selected conversation is the same as
            // the conversation of the broadcasted message
            if((int) $this->selectedConversation->id === (int) $event['conversation_id']){
                // if true  mark message as read
                $this->dispatchBrowserEvent('markMessageAsRead');
            }
        }
    }

    function broadcastedMessageReceived($event)
    {
        // Update the conversation details with the last data
      $this->emitTo('chat.chat-list','refresh');
      $broadcastedMessage = Message::find($event['message']);

        // Check if the user is selecting a conversation
        if ($this->selectedConversation) {
            //Check if current selected conversation is the same as
            // the conversation of the broadcasted message
          if ((int) $this->selectedConversation->id  === (int)$event['conversation_id']) {
              // if true show the new message
                $broadcastedMessage->read = 1;
                $broadcastedMessage->save();
                $this->pushMessage($broadcastedMessage->id);
              // Mark message as read for the sender
                $this->emitSelf('broadcastMessageRead');
            }
        }
    }

    public function broadcastMessageRead( )
    {
        broadcast(new MessageRead($this->selectedConversation->id, $this->receiverInstance->id));
    }

    public function pushMessage($messageId)
    {
        $newMessage = Message::find($messageId);
        $this->messages->push($newMessage);
        // Scroll to the last message
        $this->dispatchBrowserEvent('rowChatToBottom');
    }

    function loadmore()
    {
        // Load 10 more messages
        $this->paginateVar = $this->paginateVar + 10;
        $this->messages_count = Message::where('conversation_id',
            $this->selectedConversation->id)->count();

        $this->messages = Message::where('conversation_id',  $this->selectedConversation->id)
            ->skip($this->messages_count -  $this->paginateVar)
            ->take($this->paginateVar)->get();

        $height = $this->height;
        // Send the old height to know where the scroll stopped before update
        $this->dispatchBrowserEvent('updatedHeight', ($height));
    }

    public function resetComponent()
    {
        // Clear conversation details when it is closed
        $this->selectedConversation= null;
        $this->receiverInstance= null;
    }

    function updateHeight($height)
    {
        // dd($height);
        $this->height = $height;
    }

    public function loadConversation(Conversation $conversation, User $receiver)
    {
        // load conversation messages
        $this->selectedConversation =  $conversation;
        $this->receiverInstance =  $receiver;

        $this->messages_count = Message::where('conversation_id',
            $this->selectedConversation->id)->count();

        $this->messages = Message::where('conversation_id',  $this->selectedConversation->id)
            ->skip($this->messages_count -  $this->paginateVar)
            ->take($this->paginateVar)->get();

        // hide the chatList on phone size
        $this->dispatchBrowserEvent('chatSelected');

        // mark messages as read
        Message::where('conversation_id',$this->selectedConversation->id)
         ->where('receiver_id',auth()->user()->id)->update(['read'=> 1]);
        // show the messages as read in the sender screen
        $this->emitSelf('broadcastMessageRead');
    }

    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}
