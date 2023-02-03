<?php

namespace App\Providers;

use App\Repositories\ActivationRepository;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Models\Customer;


class ActivationServiceProvider
{
    protected $mailer;

    protected $activationRepo;

    protected $resendAfter = 24;

    public function __construct(Mailer $mailer, ActivationRepository $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
    }

    public function sendActivationMail($user)
    {
        if ($user->activated || !$this->shouldSend($user)) {
            return;
        }

        $token = $this->activationRepo->createActivation($user);

        $link = route('email.verify', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

//        $this->mailer->raw($message, function (Message $m) use ($user) {
//            $m->to($user->email)->subject('Activation mail');
//        });
        $this->mailer->send('front.emails.activation', array('activation_link'=>$link), function (Message $m) use ($user) {
            $m->to($user->email, $user->cus_name)->subject(trans('localize.member.account_activation.title'));
        });
    }

    public function sendActivationMail_byAdmin($user, $password)
    {
        if ($user->activated || !$this->shouldSend($user)) {
            return;
        }

        $token = $this->activationRepo->createActivation($user);

        $link = route('email.verify', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->send('front.emails.customer_activation_by_admin', array('activation_link'=>$link, 'password'=>$password,'email'=>$user->email), function (Message $m) use ($user) {
            $m->to($user->email, $user->cus_name)->subject(trans('localize.member.account_activation.title'));
        });
    }

    public function activateUser($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $user = Customer::find($activation->user_id);

        $user->email_verified = true;

        $user->save();

        $this->activationRepo->deleteActivation($token);

        return $user;
    }

    private function shouldSend($user)
    {
        $activation = $this->activationRepo->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }

    public function resendActivation($user)
    {
        $token = $this->activationRepo->createActivation($user);

        $link = route('email.verify', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->send('front.emails.activation', array('activation_link'=>$link), function (Message $m) use ($user) {
            $m->to($user->email, $user->cus_name)->subject(trans('localize.member.account_activation.title'));
        });
    }

}