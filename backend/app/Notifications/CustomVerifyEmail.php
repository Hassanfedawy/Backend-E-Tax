<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

    $verificationUrl = $this->verificationUrl($notifiable);

    // Extract everything after "verify/"
    $afterVerify = explode('verify/', $verificationUrl)[1]; 
    // Example: "36/abc123?expires=...&signature=..."

    $parts = explode('?', $afterVerify);
    list($id, $hash) = explode('/', $parts[0]);
    $queryString = $parts[1];

    // Build new frontend URL with id & hash as query params
    $loginUrl = config('app.frontend_url') . '/user/auth/login?' . $queryString . "&id={$id}&hash={$hash}";
    

        info($loginUrl);
        return (new MailMessage)
            ->subject('Verify Your Email Address')
             ->view('emails.verify_email', [
                'url' =>$loginUrl,
    
            ]);
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60), //expires
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }
}