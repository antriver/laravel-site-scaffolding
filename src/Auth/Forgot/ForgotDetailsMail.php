<?php

namespace Antriver\LaravelSiteUtils\Auth\Forgot;

use Antriver\LaravelSiteUtils\Mail\Base\ExtendedMailable;
use Antriver\LaravelSiteUtils\Users\User;

class ForgotDetailsMail extends ExtendedMailable
{
    /**
     * @var string
     */
    private $token;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @param User $user
     */
    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->setRecipient($user);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this
            ->subject("Your account information")
            ->line("(That's your username, in case you forgot it.)")
            ->line(
                "You are receiving this email because somebody filled out the Forgotten Details form and entered
                your email address."
            )
            ->line('If you have forgotten your password, click this button to create a new one:')
            ->action('Reset Password', www_url('reset-password').'?id='.$this->recipient->id.'&token='.$this->token)
            ->line(
                'If you did not make this request, no action is required on your part. 
            Your account information is safe, and nobody is seeing this email except you.'
            );

        return $this->view('emails.layouts.default');
    }
}
