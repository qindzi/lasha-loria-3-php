<?php

namespace StephaneCoinon\Mailtrap;

class Message extends Model
{

    public function recipientEmails()
    {
        return array_map(function ($email) {
            return trim($email);
        }, explode(',', $this->to_email));
    }


    public function subject()
    {
        return $this->subject;
    }


    public function htmlBody()
    {
        $this->html_body = $this->getRaw($this->html_path);

        return $this->html_body;
    }



    public function textBody()
    {
        $this->txt_body = $this->getRaw($this->txt_path);

        return $this->txt_body;
    }


    public function rawBody()
    {
        $this->raw_body = $this->getRaw($this->raw_path);

        return $this->raw_body;
    }


    public function headers()
    {
        $this->headers = (array) $this->getRaw($this->apiUrl(
            'inboxes/'.$this->inbox_id.'/messages/'.$this->id.'/mail_headers'
        ))->headers;

        return $this->headers;
    }
}
