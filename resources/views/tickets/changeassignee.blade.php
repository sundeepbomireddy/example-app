

                

                <div class="panel-body">


                    <form class="form-horizontal"  action="{{ url('change_assignee') }}" method="POST" id="change-assignee" class="form">
                       
                      
                        <input name="ticketid" type="hidden" value="<?=$ticket->ticket_id?>">

                        <div class="form-group">
                            <label  class="col-md-4 control-label">Assignee</label>

                            <div class="col-md-6">
                                <select id="assigned_to"  class="form-control" name="assigned_to" onchange="event.preventDefault();
                                                     document.getElementById('change-assignee').submit();">
                                    
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" <?php if($user->id == $ticket->assigned_to) echo "Selected"; ?>>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                {{ csrf_field() }}
                                
                            </div>
                        </div>
                      

                      


                        <!-- <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-ticket"></i> Open Ticket
                                </button>
                            </div>
                        </div> -->
                    </form>
                </div>
            