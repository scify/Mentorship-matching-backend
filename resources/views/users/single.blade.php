<div class="card card-user card-clickable card-clickable-over-content">
    <div class="card-body">
        <h4 class="userDetail">{{$user->first_name}} {{$user->last_name}}
            @if($user->isActivated())
                <i class="ion-checkmark-circled green" aria-hidden="true" title="Active user"></i>
            @else
                <i class="fa fa-ban red" aria-hidden="true" title="Deactivated user"></i>
            @endif
        </h4>
        <p class="userDetail">{{$user->email}}</p>
        @if($loggedInUser != null)
            @if($loggedInUser->userHasAccessToCRUDSystemUser())
                <div class="clickable-button">
                    <div class="layer bg-orange"></div>
                    <a class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog" aria-hidden="true"></i></a>
                </div>

                <div class="layered-content bg-orange">
                    <div class="overflow-content">
                        <ul class="borderless float-left">

                            <li><a href="{{route('showEditUserForm', $user->id)}}" class="btn btn-flat btn-ripple"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                            <li>
                                <a data-toggle="modal"
                                   data-userName="{{$user->first_name . $user->last_name}}"
                                   data-userId="{{$user->id}}" class="btn btn-flat btn-ripple deleteUserBtn">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </a>
                            </li>
                        </ul>
                        <ul class="borderless float-right">
                            @if(!$user->isActivated())
                                <li>
                                    <a data-toggle="modal"
                                       data-userName="{{$user->first_name . $user->last_name}}"
                                       data-userId="{{$user->id}}" class="btn btn-flat btn-ripple activateUserBtn">
                                        <i class="fa fa-check" aria-hidden="true"></i> Activate
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a data-toggle="modal"
                                       data-userName="{{$user->first_name . $user->last_name}}"
                                       data-userId="{{$user->id}}" class="btn btn-flat btn-ripple deactivateUserBtn">
                                        <i class="fa fa-ban" aria-hidden="true"></i> Deactivate
                                    </a>
                                </li>
                            @endif
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
        @if($user->roles != null)
            <div class="roles">Roles:
                @foreach($user->roles as $role)
                    <b>{{$role->title}}</b>
                    @if(!$loop->last)
                     ,
                    @endif
                @endforeach
            </div>
        @endif
    </div><!--.card-footer-->
</div><!--.card-->