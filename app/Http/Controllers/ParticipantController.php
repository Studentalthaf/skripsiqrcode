<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Sodium;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\ErrorCorrectionLevel;


class ParticipantController extends Controller
{
    public function index($event_id)
    {
        $participants = Participant::where('event_id', $event_id)->get();
        $event = Event::findOrFail($event_id);
        $encryptionKey = $this->getEncryptionKey($event->encryption_key);
        $user = auth()->user();

        foreach ($participants as $participant) {
            try {
                $decryptedData = $this->decryptData($participant->encrypted_data, $encryptionKey);
                $participant->decrypted_name = $decryptedData['name'];
                $participant->decrypted_email = $decryptedData['email'];
                $participant->decrypted_phone = $decryptedData['phone'];
                $participant->decrypted_logo = $event->logo;
                $participant->decrypted_signature = $event->signature;
                $participant->decrypted_nama_lengkap = $user->nama_lengkap;
                $participant->decrypted_date = $event->date;
                $participant->decrypted_title = $event->title;
            } catch (\Exception $e) {
                $participant->decrypted_name = "Gagal Dekripsi";
            }
        }

        return view('pointakses.user.page.participant_index', compact('participants', 'event_id'));
    }

    public function history()
    {
        $user = auth()->user()->load('participatedEvents');
    
        return view('pointakses.user.page.page_user_history', [
            'user' => $user,
            'events' => $user->participatedEvents,
        ]);
    }
    
    
    
}