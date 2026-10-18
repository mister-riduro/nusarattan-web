<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Anti-spam Honeypot: field named 'website_hp' must be empty
        if ($request->filled('website_hp')) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Thank you for your message!']);
            }
            return back()->with('success', 'Thank you for your message!');
        }

        $type = $request->input('type', 'contact');

        if ($type === 'catalog_download') {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'business_type' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
            ]);
            $validated['type'] = 'catalog_download';
            $validated['message'] = 'Catalog download request. Business type: ' . ($request->input('business_type') ?? '-');
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'country' => ['nullable', 'string', 'max:255'],
                'message' => ['required', 'string', 'max:5000'],
            ]);
            $validated['type'] = 'contact';
        }

        $validated['ip'] = $request->ip();

        $inquiry = Inquiry::create($validated);

        // Send email notification to owner
        $recipient = env('MAIL_TO_ADDRESS', Setting::get('contact_email', 'nusarattanid@gmail.com'));
        try {
            Mail::raw(
                "New inquiry received on Nusarattan:\n\n" .
                "Type: {$inquiry->type}\n" .
                "Name: {$inquiry->name}\n" .
                "Email: {$inquiry->email}\n" .
                "Country: {$inquiry->country}\n" .
                "Business Type: {$inquiry->business_type}\n" .
                "Message:\n{$inquiry->message}\n\n" .
                "IP: {$inquiry->ip}\n" .
                "Date: " . now()->toDateTimeString(),
                function ($message) use ($recipient, $inquiry) {
                    $message->to($recipient)
                        ->subject("New Nusarattan Inquiry from {$inquiry->name}");
                }
            );
        } catch (\Throwable $e) {
            Log::warning("Failed to send inquiry email: " . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been received.',
                'catalog_url' => Setting::get('catalog_url', '/catalog.pdf'),
            ]);
        }

        return back()->with('success', 'Thank you! Your message has been received.');
    }
}

