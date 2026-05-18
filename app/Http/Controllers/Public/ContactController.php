<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'business_type' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // TODO: enviar email con Mail::to(config('mail.from.address'))->send(new ContactMail($request->all()));

        return back()->with([
            'toast' => [
                'type' => 'success',
                'message' => 'Mensaje enviado. Te responderemos pronto.',
            ],
        ]);
    }
}
