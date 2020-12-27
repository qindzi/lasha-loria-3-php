<?php

namespace StephaneCoinon\Mailtrap;

use StephaneCoinon\Mailtrap\Message;

class Inbox extends Model
{

    public static function all()
    {
        return ($model = new static)->get($model->apiUrl('inboxes'));
    }


    public static function find($id)
    {
        return ($model = new static)->get($model->apiUrl('inboxes/'.$id));
    }


    public function messages()
    {
        return $this->model(Message::class)->get($this->apiUrl('inboxes/'.$this->id.'/messages'));
    }


    public function message($id)
    {
        return $this->model(Message::class)->get($this->apiUrl('inboxes/'.$this->id.'/messages/'.$id));
    }


    public function lastMessage()
    {
        $messages = $this->messages();

        if (! count($messages)) {
            return null;
        }

        // API returns messages from newest to oldest so last message is the
        // first one of the list
        return $messages[0];
    }



    public function hasMessageFor($email)
    {
        $messages = $this->messages();

        foreach ($messages as $message) {
            if (in_array($email, $message->recipientEmails())) {
                return true;
            }
        }

        return false;
    }


    public function empty($id = null)
    {
        $id = (isset($id)) ? $id : $this->attributes['id'];

        return (new static)->patch($this->apiUrl('inboxes/' . $id . '/clean'));
    }
}
