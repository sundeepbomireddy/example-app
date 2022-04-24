<?php

namespace App\Http\Controllers;
use App\Category;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mailers\AppMailer;

class TicketsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::paginate(10);
       // $users = User::all();
       // $tickets = Ticket::join('users', 'users.id', '=', 'tickets.assigned_to')
              //  ->get(['tickets.*', 'users.name']);
               /*echo '<pre>';
               print_r($tickets);*/
      /* $tickets=  Ticket::join('users', 'tickets.assigned_to', '=', 'users.id')
        ->paginate(15, array('users.*'));*/

        return view('tickets.index', compact('tickets'));
       // return view('tickets.index')->withTicket($tickets);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        $users = User::all();
        return view('tickets.create', compact('categories','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, AppMailer $mailer)
    {



        $this->validate($request, [
            'title' => 'required',
            'assigned_to' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'message' => 'required'
        ]);


        $file = $request->file('userattachment');

        
  
      //Display File Name
     $filename=$file->getClientOriginalName();
  
   
      //Display File Extension
     // echo 'File Extension: '.$file->getClientOriginalExtension();
     // echo '<br>';
  
      //Display File Real Path
    //  echo 'File Real Path: '.$file->getRealPath();
   //   echo '<br>';
   
      //Display File Size
      //echo 'File Size: '.$file->getSize();
     // echo '<br>';
    
      //Display File Mime Type
     // echo 'File Mime Type: '.$file->getMimeType();
    
      //Move Uploaded File
      $destinationPath = 'uploads';
      $file->move($destinationPath,$file->getClientOriginalName());
      
        $ticket = new Ticket([
            'title' => $request->input('title'),
            'user_id' => Auth::user()->id,
            'ticket_id' => strtoupper(str_random(10)),
            'assigned_to' => $request->input('assigned_to'),
            'category_id' => $request->input('category'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'status' => "Open",
            'attachment'=>$filename,
        ]);


        $ticket->save();

        //$mailer->sendTicketInformation(Auth::user(), $ticket);

        return redirect()->back()->with("status", "A ticket with ID: #$ticket->ticket_id has been opened.");
    }

    public function userTickets()
    {
        $tickets = Ticket::where('assigned_to', Auth::user()->id)->paginate(10);

        return view('tickets.user_tickets', compact('tickets'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();
       $users = User::all();
        return view('tickets.show', compact('ticket','users'));
    }

    public function close($ticket_id, AppMailer $mailer)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        $ticket->status = "Closed";

        $ticket->save();

        $ticketOwner = $ticket->user;

        $mailer->sendTicketStatusNotification($ticketOwner, $ticket);

        return redirect()->back()->with("status", "The ticket has been closed.");
    }

      
    public function changeAssignee(Request $request)
    {
        $this->validate($request, [
            'assigned_to' => 'required',
        ]);

        

     /*   $ticket = new Ticket([
            'title' => $request->input('title'),
            'user_id' => Auth::user()->id,
            'ticket_id' => strtoupper(str_random(10)),
            'assigned_to' => $request->input('assigned_to'),
            'category_id' => $request->input('category'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'status' => "Open"
        ]);

        $ticket->save();*/
        Ticket::where('ticket_id', $request->input('ticketid'))->update(array('assigned_to' => $request->input('assigned_to')));

        //$mailer->sendTicketInformation(Auth::user(), $ticket);

        return redirect()->back()->with("status", "Ticket  has been successfully assigned.");
    }
}