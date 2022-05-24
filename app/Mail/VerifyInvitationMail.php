<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $team_member_mail = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($team_member_mail)
    {
        $this->mail_data = $team_member_mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = auth()->user();
        return $this->from('example@gmail.com')->subject('Verification Email')->markdown('email.verifyinvitationmail')->with(['data'=> $this->mail_data,'userInfo' => $user]);
    }
}
