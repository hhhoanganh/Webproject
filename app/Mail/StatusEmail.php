<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $status)
    {
        $this->email = $code;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->status)
        return $this->subject("Đơn hàng"+ $this->email + "giao thành công.");
        return $this->subject("Đơn hàng"+ $this->email + "giao thất bại.");
    }
}
