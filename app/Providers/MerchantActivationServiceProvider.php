<?php

namespace App\Providers;

use App\Repositories\MerchantActivationRepo;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Models\Merchant;


class MerchantActivationServiceProvider
{

    protected $mailer;

    protected $activationRepo;

    protected $resendAfter = 24;

    public function __construct(Mailer $mailer, MerchantActivationRepo $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
    }

    public function sendActivationMail($merchant)
    {

        if ($merchant->activated || !$this->shouldSend($merchant)) {
            return;
        }

        $token = $this->activationRepo->createActivation($merchant);

        $link = route('merchant.activate', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->send('front.emails.activation', array('activation_link'=>$link), function (Message $m) use ($merchant) {
            $m->to($merchant->email, $merchant->mer_fname)->subject(trans('localize.merchant.account_activation.title'));
        });
    }

    public function sendActivationMail_by_admin($merchant,$password)
    {

        if ($merchant->activated || !$this->shouldSend($merchant)) {
            return;
        }

        $token = $this->activationRepo->createActivation($merchant);

        $link = route('merchant.activate', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->send('front.emails.merchant_activation_by_admin', array('activation_link'=>$link, 'password'=>$password,'username'=>$merchant->username), function (Message $m) use ($merchant) {
            $m->to($merchant->email, $merchant->mer_fname)->subject(trans('localize.merchant.account_activation.title'));
        });
    }

    public function activateUser($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $merchant = Merchant::find($activation->user_id);

        if ($merchant->mer_staus != 1) {
            $merchant->mer_staus = 2;
            $merchant->save();
        }

        $this->activationRepo->deleteActivation($token);

        return $merchant;

    }

    private function shouldSend($merchant)
    {
        $activation = $this->activationRepo->getActivation($merchant);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }

    public function resendActivation($merchant)
    {
        $token = $this->activationRepo->createActivation($merchant);

        $link = route('merchant.activate', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->send('front.emails.activation', array('activation_link'=>$link), function (Message $m) use ($merchant) {
            $m->to($merchant->email, $merchant->mer_fname)->subject(trans('localize.merchant.account_activation.title'));
        });
    }

}
