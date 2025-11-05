<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessXlsxJob;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Event\Code\Throwable;

class UploadController extends Controller
{
    public function index(){
        $uploads = Upload::all();
        return view('upload', compact('uploads'));
    }
    public function store(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:51200',
        ]);
        $file = $request->file('file');
        $filename = time() . '_' .$file->getClientOriginalName();
        $filepath = $file->storeAs('uploads', $filename);

        $upload = Upload::create([
            'user_id' => Auth::id(),
            'filename' => $filename,
            'filepath' => $filepath,
            'status' => 'pending',
        ]);
        ProcessXlsxJob::dispatch($upload);
        return view('upload', ['success' => 'File uploaded successfully.', 'uploads' => Upload::all()]);
    }
    public function download($id)
    {
        $upload = Upload::findOrFail($id);

        if ($upload->status !== 'success') {
            return redirect()->back()->withErrors('File not ready yet.');
        }

        $xmlPath = 'processed/' . pathinfo($upload->filename, PATHINFO_FILENAME) . '.xml';

        if (!Storage::disk('local')->exists($xmlPath)) {
            dd('as');
            return redirect()->back()->withErrors('XML not found.');
        }
        
        try {
            return Storage::download($xmlPath, pathinfo($upload->filename, PATHINFO_FILENAME) . '.xml');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors($th->getMessage());
        }
    }
    public function statuses()
    {
        $uploads = Upload::select('id', 'status')->get();
        return response()->json($uploads);
    }

}
