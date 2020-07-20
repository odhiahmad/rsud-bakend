<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class HelloUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
//        $namaUser = $this->user->id;
        return (new MailMessage)
            ->from('apktesting@rsud.padangpanjang.go.id','RSUD Padang Panjang')
            ->subject('Konfirmasi Akun: RSUD Padang Panjang')
            ->greeting('Selamat!')
            ->line('Silahkan konfirmasi email anda dengan klik link dibawah ini.')
            ->action('Klik Disini', url('/'))
            ->line('Terimakasih telah menggunakan aplikasi kami!')
            ->priority(1);

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
