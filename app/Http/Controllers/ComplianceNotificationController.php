<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JamesKabz\Sms\Facades\Sms;

class ComplianceNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'compliant' => 'required|boolean',
            'body' => 'nullable|string',
            'template' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        $body = $data['body'] ?? 'Compliance Body';

        if (!empty($data['template'])) {
            return Sms::sendTemplate($data['template'], $data['phone'], [
                'name' => $data['name'],
                'status' => $data['compliant'] ? 'COMPLIANT' : 'NON-COMPLIANT',
                'body' => $body,
            ]);
        }

        $message = $data['message']
            ?? ($data['compliant']
                ? "Hello {$data['name']}, you have Complied with {$body}."
                : "Hello {$data['name']}, you have not Complied with {$body}. Please take action.");

        return Sms::send($data['phone'], $message);
    }
}
