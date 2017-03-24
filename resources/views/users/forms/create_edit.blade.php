@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-8 ">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>{{$formTitle}}</h4>
                    </div>
                </div><!--.panel-heading-->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <form id="gameFlavor-handling-form" class="jobPairsForm noInputStyles" method="POST"
                                  action="{{($user->id == null ? route('createUser') : route('editUser', $user->id))}}"
                                  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <!-- First name-->
                                <div class="{{ $errors->first('first_name')?'has-error has-feedback':'' }}">
                                    <div class="inputer floating-label margin-top-60">
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
                                    <div class="inputer floating-label margin-top-60">
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
                                    <div class="inputer floating-label margin-top-60">
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

                                    <div class="inputer floating-label margin-top-60">
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
                                    <div class="inputer floating-label margin-top-60">
                                        <div class="input-wrapper">
                                            <input name="passwordconfirm" type="password" class="form-control">
                                            <label for="passwordconfirm" class="control-label">Repeat Password</label>
                                        </div>
                                    </div>
                                    @if ($errors->first('passwordconfirm'))
                                        <span class="help-block">{{ $errors->first('passwordconfirm') }}</span>
                                    @endif
                                </div>
                                <!-- User icon selection -->
                                <div id="userIconSelectionContainer" class="row">
                                    <div class="col-md-12">
                                        <div class="selecterTitle form-full-row">Select user icon</div>
                                        {{-- make the first item selected --}}
                                        <?php $first = true; ?>
                                        @foreach($userIcons as $userIcon)
                                            <input name="usericon" type="radio" value="{{ $userIcon->title }}"
                                                   id="{{ $userIcon->title }}" class="form-control userIconRadio"
                                                   @if($first || (isset($user) && $user->user_icon_id != null && $user->user_icon_id === $userIcon->id))
                                                        checked="checked"
                                                   @endif
                                            >
                                            <label for="{{ $userIcon->title }}" class="control-label userIconLabel {{(isset($user) && $user->user_icon_id != null && $user->user_icon_id === $userIcon->id) ? '':'greyscale'}}">
                                                <img class="face-radius" src="{{ asset($userIcon->path) }}"
                                                     alt="{{ $userIcon->title }}">
                                            </label>
                                            {{-- the next item(s) should not be checked  --}}
                                            <?php $first = false; ?>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Skills -->
                                        <div class="selecterTitle form-full-row">Select user roles</div>
                                        <select id="roleSelector" data-placeholder="Choose roles" name="user_roles[][id]" class="chosen-select" multiple>
                                            @foreach($userRoles as $userRole)
                                                <option value="{{$userRole->id}}" {{in_array($userRole->id, $userRoleIds)? 'selected':''}}>{{$userRole->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="accountManagerDetailsContainer" class="row {{!$user->isAccountManager() ? 'hidden' : ''}}">

                                    <div class="col-md-12">
                                        <h6 class="margin-top-20">ACCOUNT MANAGER OPTIONS</h6>
                                        <!-- Company -->
                                        <div class="selecterTitle form-full-row">Select Company</div>
                                        <select data-placeholder="Choose Company" name="company_id" class="chosen-select">
                                            <option value="">No Company</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}" {{$company->id == $user['company_id']? 'selected':''}}>{{$company->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <!-- Capacity -->
                                        <div class="inputer floating-label margin-top-60">
                                            <div class="input-wrapper">
                                                <input name="capacity" type="number" class="form-control" value="{{$user->capacity != null ? $user->capacity->capacity : ""}}">
                                                <label for="capacity" class="control-label">Capacity</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submitBtnContainer margin-top-50">
                                    <button type="button" class="btn btn-flat-primary">
                                        <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('showAllUsers') }}">Cancel</a>
                                    </button>
                                    <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple margin-left-10">
                                        {{($user->id == null ? 'Create' : 'Save')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new UserFormController();
            controller.init();
        });
    </script>
@endsection
