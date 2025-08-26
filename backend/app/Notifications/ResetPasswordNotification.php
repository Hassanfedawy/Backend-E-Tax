<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
 
     use Queueable;
    public $token;


    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(config('app.url') . route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            if ($notifiable->is_admin){
                $url=config('app.frontend_url'). '/admin/auth/resetpassword?token='.$this->token.'&email='.$notifiable->getEmailForPasswordReset();

            }    
            else{
                $url=config('app.frontend_url'). '/user/auth/resetpassword?token='.$this->token.'&email='.$notifiable->getEmailForPasswordReset();
            }
           
        return (new MailMessage)
            ->subject('🔑 Reset Your Password')
            ->view('emails.forgot-password', [
                'name' => $notifiable->name,
                'url' => $url,
    
            ]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }








    /*
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        // Use MailMessage with markdown view for full Blade control
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->markdown('emails.forgot-password', ['url' => $url]);
    }*/


}
