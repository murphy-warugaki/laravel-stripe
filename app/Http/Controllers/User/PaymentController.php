<?php

namespace App\Http\Controllers\User;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Payment;
use Laravel\Cashier\Cashier;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getCurrentPayment()
    {
        $user = Auth::user();
        $defaultCard = Payment::getDefaultcard($user);

        return view('user.payment.index', compact('user', 'defaultCard'));
    }

    public function getPaymentForm()
    {
        $user = Auth::user();
        //$stripeCustomer = $user->createOrGetStripeCustomer();
        //$stripeCustomer = $user->updateStripeCustomer();
        if (!$user->stripe_id) {
            $stripeCustomer = $user->createAsStripeCustomer(['name' => $user->name]);
        }
        return view('user.payment.form', ['intent' => $user->createSetupIntent()]);
    }


    public function storePaymentInfo(Request $request)
    {
        \Log::error(var_export($request->all(), true));
        $paymentMethod = $request->payment_method;
        if (!$paymentMethod) {
            $errors = '申し訳ありません、通信状況の良い場所で再度ご登録をしていただくか、しばらく立ってから再度登録を行ってみてください。';

            return response()->json([
            'code' => 400,
            'message' => $errors,
            ], 400);
        }

        $user = Auth::user(); //要するにUser情報を取得したい
        $user->addPaymentMethod($paymentMethod);

        try {
            if ($user->hasPaymentMethod()) {
                $user->updateDefaultPaymentMethod($paymentMethod);
            //$user->updateDefaultPaymentMethodFromStripe();
            } else {
                $user->addPaymentMethod($paymentMethod);
                //$user->updateDefaultPaymentMethodFromStripe();
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $errors = "カード登録に失敗しました。入力いただいた内容に相違がないかを確認いただき、問題ない場合は別のカードで登録を行ってみてください。";
            return response()->json([
            'code' => 400,
            'message' => $errors,
            ], 400);
        }

        $endpoint = 'https://hooks.slack.com/services/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';
        $client   = new \Razorpay\Slack\Client($endpoint);
        $client->send($content);

        return response()->json([
            'code' => 200,
            'message' => 'success',
        ]);
    }


    public function deletePaymentInfo()
    {
        $user = User::find(Auth::id());

        $result = Payment::deleteCard($user);

        if ($result) {
            return redirect('/user/payment')->with('success', 'カード情報の削除が完了しました。');
        } else {
            return redirect('/user/payment')->with('errors', 'カード情報の削除に失敗しました。恐れ入りますが、通信状況の良い場所で再度お試しいただくか、しばらく経ってから再度お試しください。');
        }
    }
}
