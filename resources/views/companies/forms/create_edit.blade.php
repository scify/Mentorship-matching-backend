@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>{{$formTitle}}</h4>
                    </div>
                </div><!--.panel-heading-->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-8">
                            <form class="jobPairsForm noInputStyles" method="POST"
                                  action="{{($company->id == null ? route('createCompany') : route('editCompany', $company->id))}}"
                                  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <!-- Name-->
                                <div class="{{ $errors->first('name')?'has-error has-feedback':'' }}">
                                    <div class="inputer floating-label margin-top-60">
                                        <div class="input-wrapper">
                                            <input required type="text" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : $company['name']}}">
                                            <label for="name">Company Name</label>
                                        </div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                </div>
                                <!-- Description -->
                                <div class="{{ $errors->first('description')?'has-error has-feedback':'' }}">
                                    <div class="inputer floating-label margin-top-60">
                                        <div class="input-wrapper">
                                            <textarea required class="form-control" name="description">{{ old('description') != '' ? old('description') : $company['description']}}</textarea>
                                            <label for="description">Description</label>
                                        </div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('description') }}</span>
                                </div>
                                <!-- Website -->
                                <div class="{{ $errors->first('website')?'has-error has-feedback':'' }}">
                                    <div class="inputer floating-label margin-top-60">
                                        <div class="input-wrapper">
                                            <input required type="url" class="form-control" name="website"
                                                   value="{{ old('website') != '' ? old('website') : $company['website']}}">
                                            <label for="website">Website</label>
                                        </div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('website') }}</span>
                                </div>
                                <!-- HR Contact Details -->
                                <div class="{{ $errors->first('hr_contact_details')?'has-error has-feedback':'' }}">
                                    <div class="inputer floating-label margin-top-60">
                                        <div class="input-wrapper">
                                            <input type="text" class="form-control" name="hr_contact_details"
                                                   value="{{ old('hr_contact_details') != '' ? old('hr_contact_details') : $company['hr_contact_details']}}">
                                            <label for="hr_contact_details">HR Contact Details</label>
                                        </div>
                                    </div>
                                    <span class="help-block">{{ $errors->first('hr_contact_details') }}</span>
                                </div>
                                <!-- Mentors -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="selecterTitle form-full-row">Select mentors</div>
                                        <select data-placeholder="Mentors working in this company" name="mentors[][id]" class="chosen-select" multiple>
                                            <option></option>
                                            @foreach($mentors as $mentor)
                                                <option value="{{$mentor->id}}" {{in_array($mentor->id, $companyMentorsIds)? 'selected':''}}>{{$mentor->first_name . ' ' . $mentor->last_name}}</option>
                                            @endforeach
                                        </select>
                                        <small class="help-block">Mentors not showing here are assigned to another company</small>
                                    </div>
                                </div>
                                <!-- Account manager -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="selecterTitle form-full-row">Select Company Account manager</div>
                                        <select data-placeholder="Choose Account manager" name="account_manager_id" class="chosen-select">
                                            <option value="">No Account manager</option>
                                            @foreach($accountManagers as $accountManager)
                                                <option value="{{$accountManager->id}}" {{$accountManager->id == $company['account_manager_id']? 'selected':''}}>{{$accountManager->first_name . ' ' . $accountManager->last_name}}</option>
                                            @endforeach
                                        </select>
                                        <small class="help-block">Account managers not showing here are assigned to another company</small>
                                    </div>
                                </div>
                                <div class="submitBtnContainer margin-top-100">
                                    <button type="button" class="btn btn-flat-primary">
                                        <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('home') }}">Cancel</a>
                                    </button>
                                    <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple margin-left-10">
                                        {{($company->id == null ? 'Create' : 'Edit')}}
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
            var controller = new FormController();
            controller.init();
        });
    </script>
@endsection
