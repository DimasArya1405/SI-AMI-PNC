<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale', 'id'));
        CarbonImmutable::setLocale(config('app.locale', 'id'));
        setlocale(LC_TIME, 'id_ID', 'id_ID.utf8', 'Indonesian_Indonesia.1252', 'Indonesian');

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $email = $notifiable->getEmailForPasswordReset();
            $url = route('password.reset', [
                'token' => $token,
                'email' => $email,
            ], true);
            $broker = config('auth.defaults.passwords');
            $expire = config("auth.passwords.{$broker}.expire", 60);

            return (new MailMessage)
                ->subject('Reset Kata Sandi SIAMI')
                ->greeting('Halo,')
                ->line('Anda menerima email ini karena ada permintaan reset kata sandi untuk akun SIAMI Anda.')
                ->line('Klik atau salin link berikut untuk membuat kata sandi baru:')
                ->line($url)
                ->line("Link reset kata sandi ini akan kedaluwarsa dalam {$expire} menit.")
                ->line('Jika Anda tidak meminta reset kata sandi, abaikan email ini.')
                ->salutation('Terima kasih, SIAMI PNC');
        });
    }
}
