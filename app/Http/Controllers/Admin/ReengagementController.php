<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReengagementStoreRequest;
use App\Mail\CustomerReengagementMail;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\ReengagementLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ReengagementController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        return view('admin.reengagements.index', [
            'logs' => ReengagementLog::query()
                ->with(['customer', 'assignment.employee'])
                ->when(in_array($status, [ReengagementLog::STATUS_SENT, ReengagementLog::STATUS_FAILED], true), function ($query) use ($status): void {
                    $query->where('status', $status);
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function store(ReengagementStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $customer = Customer::query()
            ->withPurchaseMetrics()
            ->findOrFail($data['customer_id']);

        if (! $customer->isLostCustomer(Customer::lostCustomerDays())) {
            return back()
                ->withInput()
                ->withErrors(['customer_id' => 'Only inactive customers can receive re-engagement email.']);
        }

        $assignment = null;

        if (! empty($data['customer_assignment_id'])) {
            $assignment = CustomerAssignment::query()
                ->where('customer_id', $customer->id)
                ->findOrFail($data['customer_assignment_id']);
        }

        $subject = trim((string) ($data['subject'] ?? ''));
        $subject = $subject !== '' ? $subject : 'We have a special update for you from SalesPro';
        $message = trim((string) ($data['message'] ?? ''));
        $message = $message !== '' ? $message : "We noticed you have not purchased from us recently. We would like to reconnect and share new offers that may be useful for you.";
        $logData = [
            'customer_id' => $customer->id,
            'customer_assignment_id' => $assignment?->id,
            'channel' => ReengagementLog::CHANNEL_EMAIL,
            'subject' => $subject,
            'message' => $message,
        ];

        if (! app()->environment('testing') && $this->smtpConfigurationIsIncomplete()) {
            ReengagementLog::query()->create($logData + [
                'status' => ReengagementLog::STATUS_FAILED,
                'failure_reason' => 'SMTP sender email is not configured.',
            ]);

            return back()->with('error', 'Email not sent. Set the real Gmail address in SMTP configuration.');
        }

        try {
            Mail::to($customer->email)->send(new CustomerReengagementMail($customer, $subject, $message));

            ReengagementLog::query()->create($logData + [
                'status' => ReengagementLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

            return back()->with('success', 'Re-engagement email sent successfully.');
        } catch (Throwable $exception) {
            ReengagementLog::query()->create($logData + [
                'status' => ReengagementLog::STATUS_FAILED,
                'failure_reason' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Email attempt saved, but sending failed. Check SMTP configuration.');
        }
    }

    private function smtpConfigurationIsIncomplete(): bool
    {
        $username = (string) Config::get('mail.mailers.smtp.username');
        $fromAddress = (string) Config::get('mail.from.address');

        return $username === ''
            || $fromAddress === ''
            || $username === 'your-gmail-address@gmail.com'
            || $fromAddress === 'your-gmail-address@gmail.com';
    }
}
