<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CardPayment\CardPaymentInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\AlterTranRequest;
use App\Models\Payment;
use App\Models\Project;
use App\Notifications\AlterPaymentNotification;
use Illuminate\Http\Request;
use App\Services\Project\RemittanceService;
use DB;
use Exception;
use Illuminate\Support\Facades\Notification;
use Log;
use App\Traits\UniqueToken;

class PaymentController extends Controller
{
    public function __construct(CardPaymentInterface $card_payment_interface, RemittanceService $remittance)
    {
        $this->card_payment = $card_payment_interface;
        $this->remittance = $remittance;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payments = Payment::withoutGlobalScopes()
            ->search()
            ->narrowDownPaymentOrderId()
            ->narrowDownWithProject()
            ->narrowDownByDate()
            ->narrowDownByPrice()
            ->with([
                'user' => function ($query) {
                    $query->with(['profile', 'address']);
                },
                'inviter'
            ])
            ->sortBySelected($request->sort_type);

        //リレーション先OrderBy
        if ($request->sort_type === 'user_name_asc') {
            $payments = $payments->get()->sortBy('user.name');
        } elseif ($request->sort_type === 'user_name_desc') {
            $payments = $payments->get()->sortByDesc('user.name');
        } elseif ($request->sort_type === 'inviter_name_asc') {
            $payments = $payments->get()->sortBy('inviter.name');
        } elseif ($request->sort_type === 'inviter_name_desc') {
            $payments = $payments->get()->sortByDesc('inviter.name');
            // } elseif ($request->sort_type === 'plan_payment_included_plan_project_user_name_asc') {
            //     $payments = $payments->get()->sortBy('includedPlans.project.user.name')->paginate(10);
            // } elseif ($request->sort_type === 'plan_payment_included_plan_project_user_name_desc') {
            //     $payments = $payments->get()->sortByDesc('includedPlans.project.user.name')->paginate(10);
            // } elseif ($request->sort_type === 'plan_payment_included_plan_project_title_asc') {
            //     $payments = $payments->get()->sortBy('includedPlans.project.title')->paginate(10);
            // } elseif ($request->sort_type === 'plan_payment_included_plan_project_title_desc') {
            //     $payments = $payments->get()->sortByDesc('includedPlans.project.title')->paginate(10);
        } else {
            $payments = $payments->get();
        }

        $payments->map(function ($payment) {
            if ($payment->payment_api === 'GMO') {
                if ($payment->payment_way === 'credit') {
                    $response = $this->card_payment->searchTrade($payment->paymentToken->order_id);
                    if ($response->status() === 200) {
                        $payment->setAttribute('gmo_job_cd', $response['jobCd']);
                    } else {
                        $payment->setAttribute('gmo_job_cd', 'FAILED');
                    }
                } else if ($payment->payment_way === 'cvs') {
                    $response = $this->card_payment->searchTradeMulti($payment->paymentToken->order_id, 3);
                    if (!\Arr::has($response, 'ErrCode') && \Arr::has($response, 'Status')) {
                        $payment->setAttribute('gmo_job_cd', $response['Status']);
                        $payment->setAttribute('convenience', $response['CvsCode']);
                        $payment->setAttribute('conf_no', $response['CvsConfNo']);
                        $payment->setAttribute('receipt_no', $response['CvsReceiptNo']);
                    }
                }
            } else {
                $payment->setAttribute('gmo_job_cd', 'DEFAULT');
            }
        });
        if ($request->job_cd) {
            $payments = $payments->filter(function ($payment) use ($request) {
                return $payment->gmo_job_cd === $request->job_cd;
            });
        }

        return view('admin.payment.index', [
            'payments' => $payments->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(payment $payment)
    {
        //
    }

    public function alterSales(AlterTranRequest $request)
    {
        $payments = Payment::find($request->payments);
        $result = $this->remittance->IsNotFilledPaymentsJobCdConditions($payments, ['AUTH']);
        if ($result['status']) {
            return redirect()->route('admin.payment.index', ['project' => $request->project])->withErrors($result['message']);
        }
        foreach ($payments as $payment) {
            $this->card_payment->alterSales($payment->paymentToken->access_id, $payment->paymentToken->access_pass, $payment->price);
        }
        Notification::route('mail', config('mail.customer_support.address'))->notify(new AlterPaymentNotification($request->project, '実売上計上'));
        return redirect()->route('admin.payment.index', ['project' => $request->project])->with('flash_message', '実売上計上に成功しました。');
    }

    public function alterCancel(AlterTranRequest $request)
    {
        $payments = Payment::find($request->payments);
        $result = $this->remittance->IsExistsPaymentsJobCdConditions($payments, ['VOID', 'EXPIRED', 'CANCEL']);
        if ($result['status']) {
            return redirect()->route('admin.payment.index', ['project' => $request->project])->withErrors($result['message']);
        }
        DB::beginTransaction();
        try {
            foreach ($payments as $payment) {
                if ($payment->payment_way === 'credit') {
                    $payment->offsetUnset('gmo_job_cd');
                    $payment->update(['payment_is_finished' => false]);
                    $this->card_payment->refund($payment->paymentToken->access_id, $payment->paymentToken->access_pass, $payment->price);
                } else if ($payment->payment_way === 'cvs' && $payment->gmo_job_cd === 'PAYSUCCESS') {
                    $payment->offsetUnset('gmo_job_cd');
                    $payment->update(['payment_is_finished' => false]);
                    $deposit_id = UniqueToken::getToken();
                    $this->card_payment->mailRemittance($deposit_id, $payment->price, 1, $payment->user->load('profile'));
                } else if ($payment->payment_way === 'cvs' && $payment->gmo_job_cd !== 'PAYSUCCESS') {
                    $payment->offsetUnset('gmo_job_cd');
                    $payment->update(['payment_is_finished' => false]);
                    $this->card_payment->refundCVS($payment->paymentToken->access_id, $payment->paymentToken->access_pass, $payment->paymentToken->order_id);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::alert($e->getMessage());
            return redirect()->route('admin.payment.index', ['project' => $request->project])->withErrors('売上キャンセルに失敗しました。管理者にご確認ください。');
        }
        Notification::route('mail', config('mail.customer_support.address'))->notify(new AlterPaymentNotification($request->project, '売上キャンセル'));
        return redirect()->route('admin.payment.index', ['project' => $request->project])->with('flash_message', '売上キャンセルに成功しました。');
    }
}
