<div class="panel-group accordion" id="accordion_{{$company->id}}">
    <div class="panel">
        <div class="panel-heading active">
            <a class="panel-title" data-toggle="collapse" data-parent="#accordion_{{$company->id}}" href="#collapse_1_{{$company->id}}">{{$company->name}}</a>
        </div>
        @if($company->hasAccountManager())
            <i class="fa fa-user" aria-hidden="true"></i>
        @endif
        <div id="collapse_1_{{$company->id}}" class="panel-collapse collapse in">
            <div class="panel-body">
                <ul class="borderless profileDetailsList">
                    @if($company->website != null)
                        <li>
                            <a href="{{$company->website}}">Company Website</a>
                        </li>
                    @endif
                    @if($company->description != null)
                        <li>
                            <b>Description:</b> {{$company->description}}
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>