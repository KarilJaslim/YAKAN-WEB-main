<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    /**
     * Display a listing of contact messages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        $unreadCount = ContactMessage::unread()->count();
        
        return view('admin.contact.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display the specified contact message.
     *
     * @param  \App\Models\ContactMessage  $contactMessage
     * @return \Illuminate\Http\Response
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if (!$contactMessage->is_read) {
            $contactMessage->markAsRead();
        }
        
        return view('admin.contact.show', compact('contactMessage'));
    }

    /**
     * Remove the specified contact message from storage.
     *
     * @param  \App\Models\ContactMessage  $contactMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        
        return redirect()->route('admin.contact.index')
            ->with('success', 'Contact message deleted successfully.');
    }

    /**
     * Mark message as read.
     *
     * @param  \App\Models\ContactMessage  $contactMessage
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();
        
        return back()->with('success', 'Message marked as read.');
    }

    /**
     * Mark message as unread.
     *
     * @param  \App\Models\ContactMessage  $contactMessage
     * @return \Illuminate\Http\Response
     */
    public function markAsUnread(ContactMessage $contactMessage)
    {
        $contactMessage->markAsUnread();
        
        return back()->with('success', 'Message marked as unread.');
    }

    /**
     * Mark all messages as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        ContactMessage::unread()->update(['is_read' => true]);
        
        return back()->with('success', 'All messages marked as read.');
    }

    /**
     * Delete all read messages.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAllRead()
    {
        ContactMessage::read()->delete();
        
        return back()->with('success', 'All read messages deleted successfully.');
    }
}