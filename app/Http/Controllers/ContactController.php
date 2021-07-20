<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function form()
    {
        return view('contact.form');
    }

    public function send(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required'
        ];
        $this->validate($request, $rules);

        $to = [
            ['email' => 'hiroshi0104@gmail.com', 'name' => 'Your Name']
        ];

        $data = $request->only('name', 'email', 'message');
        \Mail::to($to)->send(new Contact($data));
     
        return redirect('contact/complete');
    }

    public function complete()
    {
        return view('contact.complete');
    }
}
