<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;
use App\Helpers\EncryptionHelper;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\Png;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    public function index($event_id)
    {
        // Ambil semua peserta berdasarkan event_id
        $participants = Participant::where('event_id', $event_id)->get();
        
        // Kirim event_id ke view
        return view('pointakses.user.page.participant_index', compact('participants', 'event_id'));
    }
    

    public function create($event_id)
    {
        // Menampilkan form untuk menambah peserta pada event tertentu
        $event = Event::findOrFail($event_id);  // Pastikan event_id valid
        return view('pointakses.user.page.participant_create', compact('event'));
    }
    

    public function store(Request $request)
    {
        // Validasi input dari form peserta
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'nama_peserta' => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:participants,serial_number,NULL,id,event_id,' . $request->event_id,
        ]);

        // Ambil data dari form
        $data = [
            'nama_peserta' => $request->nama_peserta,
            'instansi' => $request->instansi ?? '',
            'serial_number' => $request->serial_number,
            'event_id' => $request->event_id,
        ];

        // Menghasilkan key dan nonce
        $key = EncryptionHelper::generateKey();
        $nonce = random_bytes(12); // Misalnya menggunakan 12 byte nonce

        // Enkripsi data
        $encryptedData = EncryptionHelper::encrypt(json_encode($data), $key);

        // Menghasilkan QR code dari data terenkripsi
        $renderer = new Png();
        $renderer->setWidth(300);
        $renderer->setHeight(300);
        $writer = new Writer($renderer);
        $qrcode = $writer->writeString($encryptedData);

        // Menyimpan QR code ke storage
        $qrcodePath = 'qrcodes/' . uniqid('qrcode_', true) . '.png';
        Storage::put($qrcodePath, $qrcode);

        // Simpan data peserta ke database
        $participant = Participant::create([
            'event_id' => $data['event_id'],
            'nama_peserta' => $data['nama_peserta'],
            'instansi' => $data['instansi'],
            'serial_number' => $data['serial_number'],
            'key' => bin2hex($key),   // Simpan key sebagai string hex
            'nonce' => bin2hex($nonce), // Simpan nonce sebagai string hex
            'qrcode' => $qrcodePath,  // Simpan path QR code
        ]);

        return redirect()->route('user.participant.index', $data['event_id'])->with('success', 'Peserta berhasil ditambahkan!');
    }
}
