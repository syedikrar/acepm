<?php

namespace App\Listeners;

use App\Events\NotifySlack;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySlackListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotifySlack  $event
     * @return void
     */
    public function handle(NotifySlack $event)
    {
        $planName = 'the plan';
        $message = '*'.$event->shop->domain.' ('.$event->shop->name.')* just *' .
            $event->type .'* '. env('APP_NAME_FORMATTED').'.';
        if ($event->type == 'installed') $message .= ' Selected Plan is *'. $event->shop->billing->plan .'*.';

        if (env('APP_ENV') == 'production') $this->sendSlackMessage($message);
    }

    protected function sendSlackMessage($message)
    {
        $ch = curl_init("https://slack.com/api/chat.postMessage");
        $data = http_build_query([
            "token" => "xoxb-798325819637-969101467201-Hq0WiKy9wi1I7ZT4tqvoqW79",
            "channel" => '#ace-3in1',
            "text" => $message,
            "username" => "Billy Bot",
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
