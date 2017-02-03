@extends('layouts.app')
@section('content')
    <div class="col-md-6 centeredVertically">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="col-md-12">{{$formTitle}}</h3>
            </div><!--.panel-heading-->
            <div class="panel-body">
                <form id="gameFlavor-handling-form" class="jobPairsForm noInputStyles" method="POST"
                      action="{{($user->id == null ? route('createUser') : route('editUser', $user->id))}}"
                      enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-md-12">
                        <!-- First name-->
                        <div class="{{ $errors->first('first_name')?'has-error has-feedback':'' }}">
                            <div class="inputer floating-label">
                                <div class="input-wrapper">
                                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name') != '' ? old('first_name') : $user['first_name']}}">
                                    <label for="first_name">First Name</label>
                                </div>
                            </div>
                            @if ($errors->first('first_name'))
                                <span class="help-block">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>
                        <!-- Last name-->
                        <div class="{{ $errors->first('last_name')?'has-error has-feedback':'' }}">
                            <div class="inputer floating-label">
                                <div class="input-wrapper">
                                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name') != '' ? old('last_name') : $user['last_name']}}">
                                    <label for="last_name">Last Name</label>
                                </div>
                            </div>
                            @if ($errors->first('last_name'))
                                <span class="help-block">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>
                        <!-- E-mail -->
                        <div class="{{ $errors->first('email')?'has-error has-feedback':'' }}">
                            <div class="inputer floating-label">
                                <div class="input-wrapper">
                                    <input type="email" class="form-control" name="email"
                                           value="{{ old('email') != '' ? old('email') : $user['email']}}">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            @if ($errors->first('email'))
                                <span class="help-block">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <!-- Password -->
                        <div class="{{ $errors->first('password')?'has-error has-feedback':'' }}">

                                <div class="inputer floating-label">
                                    <div class="input-wrapper">

                                <input name="password" type="password" class="form-control"
                                        {{ $errors->first('password')?'aria-describedby="helpBlockPassword"':'' }}>
                                <label for="password" class="control-label">Password</label>
                                    </div>
                                </div>
                                @if ($errors->first('password'))
                                    <span class="help-block">{{ $errors->first('password') }}</span>
                                @endif
                        </div>

                        <!-- Password confirmation -->
                        <div class="{{ $errors->first('passwordconfirm')?'has-error has-feedback':'' }}">
                            <div class="inputer floating-label">
                                <div class="input-wrapper">
                                    <input name="passwordconfirm" type="password" class="form-control">
                                    <label for="passwordconfirm" class="control-label">Repeat Password</label>
                                </div>
                            </div>
                            @if ($errors->first('passwordconfirm'))
                                <span class="help-block">{{ $errors->first('passwordconfirm') }}</span>
                            @endif
                        </div>
                        <div class="row margin-top-60">
                            <div class="col-md-3">Select user roles</div><!--.col-md-3-->
                            <div class="col-md-9">
                                <select data-placeholder="Choose roles" name="user_roles[][id]" class="chosen-select" multiple>
                                    @foreach($userRoles as $userRole)
                                        <option value="{{$userRole->id}}" {{in_array($userRole->id, $userRoleIds)?'selected':''}}>{{$userRole->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="submitBtnContainer margin-top-50">
                            <button type="button" class="btn btn-flat-primary">
                                <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('home') }}">Cancel</a>
                            </button>
                            <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple margin-left-10">
                                {{($user->id == null ? 'Create' : 'Edit')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            $('.chosen-select').chosen({
                width: '100%'
            });
        });
    </script>
@endsection
