<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ContactAdminController extends Controller
{
    /**
     * Latihan 3: daftar pesan masuk, terbaru di atas.
     */
    public function index()
    {
        return view('posts.contacts.index', [
            'contacts'    => Contact::latest()->paginate(10),
            'totalUnread' => Contact::where('is_read', false)->count(),
        ]);
    }

    /**
     * Membuka pesan sekaligus menandainya sudah dibaca.
     */
    public function show(Contact $contact)
    {
        if (! $contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('posts.contacts.show', compact('contact'));
    }
}
