<?php

namespace Sharenjoy\NoahShop\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCouponCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $userCoupon;
    protected $promo;

    /**
     * Create a new notification instance.
     *
     * @param $userCoupon
     */
    public function __construct($userCoupon, $promo)
    {
        $this->userCoupon = $userCoupon;
        $this->promo = $promo;

        $this->afterCommit();
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
        return (new MailMessage)
            ->subject('您的折價券已建立成功')
            ->greeting('您好，' . $notifiable->name)
            ->line('我們已為您建立了一張新的折價券。')
            ->line('優惠促銷名稱：' . $this->promo->title)
            ->line('有效期：' . $this->userCoupon->expired_at->format('Y-m-d H:i:s'))
            ->action('查看折價券', url('/user/coupons')) // TODO: 這裡的網址需要根據實際情況修改
            ->line('感謝您的支持！');
    }
}
