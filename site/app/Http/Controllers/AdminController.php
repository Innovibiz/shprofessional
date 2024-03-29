<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Document;
use Illuminate\Support\Facades\File;
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function home(){
        return view('admin.dashboard');
    }
    public function user($id){
        $user=User::where('id',$id)->first();
        return view('admin.user',compact('user'));
    }
    public function files($id,$loc){
        $user=User::where('id',$id)->first();
        $docs=Document::where('user_id',$id)->where('location',$loc)->get();
        return view('admin.files',compact('docs','user','loc'));
    }
    public function getUpload($id,$loc){
        if($loc=="x"){
            return view('admin.locationx',compact('id','loc'));
        }
        if($loc=="y"){
            return view('admin.locationy',compact('id','loc'));
        }
        if($loc=="z"){
            return view('admin.locationz',compact('id','loc'));
        }
        if($loc=="o"){
            return view('admin.locationo',compact('id','loc'));
        }
        
    }
    
    public function uploadDoc(Request $request){
        $doc=new Document();
        $doc->user_id=$request->user_id;
        $doc->title=$request->title;
        $doc->location=$request->location;
        $doc->year=$request->year;
        $doc->note=$request->note;
        if($request->file('file')) {
            $doc->type=$request->file('file')->extension();
            $upload = $request->file('file');
            $fileformat = time() . '.' . $upload->getClientOriginalName();
            if ($upload->move('uploads/documents/', $fileformat)) {
                $doc->document = $fileformat;
            }
            
        }
        if($doc->save()){
            return redirect()->back()->with('success','Document Uploaded and saved.');
        }
        else{
            return redirect()->back()->with('unsuccess','Failed try again.');
        }
        
    }
    public function delete($id){
        $doc=Document::where('id',$id)->first();
        if (File::exists("uploads/documents/".$doc->document)) {
            File::delete("uploads/documents/".$doc->document);
        }else{
            return "file not Found";
        }
        if(Document::where('id',$id)->delete()){
            return redirect()->back()->with('success',' Document Deleted successfully.');
        }
        else{
            return redirect()->back()->with('unsuccess','Failed try again.');
        }
    }
    public function Downf($file){
        return  response()->download(public_path('uploads/documents/'.$file));
    }
    
}
