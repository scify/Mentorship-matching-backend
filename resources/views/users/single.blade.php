<div class="card card-user card-clickable card-clickable-over-content gameFlavorItem">
    <div class="card-body">
        <h4 class="userDetail">{{$user->first_name}} {{$user->last_name}}</h4>
        <p class="userDetail">{{$user->email}}</p>
        @if(\Illuminate\Support\Facades\Auth::user() != null)
            @if(\Illuminate\Support\Facades\Auth::user()->userHasAccessToCRUDUser())
                <div class="clickable-button">
                    <div class="layer bg-orange"></div>
                    <a class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog" aria-hidden="true"></i></a>
                </div>

                <div class="layered-content bg-orange">
                    <div class="overflow-content">
                        <ul class="borderless">

                            <li><a href="{{route('showUserEditForm', $user->id)}}" class="btn btn-flat btn-ripple"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                            <li>
                                <a data-toggle="modal"
                                   data-userName="{{$user->first_name . $user->last_name}}"
                                   data-userId="{{$user->id}}" class="btn btn-flat btn-ripple deleteUserBtn">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </a>
                            </li>
                        </ul>
                    </div><!--.overflow-content-->
                    <div class="clickable-close-button">
                        <a class="btn btn-floating initial-position floating-close"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </div>
                </div>
            @endif
        @endif
    </div><!--.card-heading-->

    <div class="card-footer">
        {{--<a href="#"><button class="btn btn-xs btn-flat pull-left" style="color: #337ab7"><i class="fa fa-download" aria-hidden="true"></i> Show matches</button></a>--}}
        @if($user->rolesForUser != null)
            <div class="roles">Roles:
                @foreach($user->rolesForUser as $role)
                    <b>{{$role->title}}</b>
                    @if(!$loop->last)
                     ,
                    @endif
                @endforeach
            </div>
        @endif
    </div><!--.card-footer-->
</div><!--.card-->