@extends('layouts.app')
@section('content')
    <div class="col-md-12 centeredVertically">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="col-md-12">{{$formTitle}}</h3>
            </div><!--.panel-heading-->
            <div class="panel-body">
                <form class="jobPairsForm noInputStyles" method="POST"
                      action="{{($company->id == null ? route('createCompany') : route('editCompany', $company->id))}}"
                      enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="formRow row">
                                <!-- Name-->
                                <div class="col-md-3 formElementName">First Name</div>
                                <div class="col-md-9">
                                    <div class="{{ $errors->first('name')?'has-error has-feedback':'' }}">
                                        <div class="inputer">
                                            <div class="input-wrapper">
                                                <input required type="text" class="form-control" name="name" value="{{ old('name') != '' ? old('name') : $company['name']}}">

                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="formRow row margin-top-40">
                                <!-- Description -->
                                <div class="col-md-3 formElementName">Description</div>
                                <div class="col-md-9">
                                    <div class="{{ $errors->first('description')?'has-error has-feedback':'' }}">
                                        <div class="inputer">
                                            <div class="input-wrapper">
                                                <textarea required class="form-control" name="description" placeholder="Company description">{{ old('description') != '' ? old('description') : $company['description']}}</textarea>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="formRow row">
                                <!-- Website -->
                                <div class="col-md-3 formElementName">Website</div>
                                <div class="col-md-9">
                                    <div class="{{ $errors->first('website')?'has-error has-feedback':'' }}">
                                        <div class="inputer">
                                            <div class="input-wrapper">
                                                <input required type="url" class="form-control" name="website"
                                                       value="{{ old('website') != '' ? old('website') : $company['website']}}">
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('website') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="formRow row">
                                <div class="col-md-3 formElementName">Select mentors</div><!--.col-md-3-->
                                <div class="col-md-9">
                                    <select data-placeholder="Mentors working in this company" name="mentors[][id]" class="chosen-select" multiple>
                                        <option></option>
                                        @foreach($mentors as $mentor)
                                            <option value="{{$mentor->id}}" {{in_array($mentor->id, $companyMentorsIds)? 'selected':''}}>{{$mentor->first_name . ' ' . $mentor->last_name}}</option>
                                        @endforeach
                                    </select>
                                    <small class="help-block">Mentors not showing here are assigned to another company</small>
                                </div>
                            </div>

                        <div class="formRow row">
                            <div class="col-md-3 formElementName">Select Company Account manager</div><!--.col-md-3-->
                            <div class="col-md-9">
                                <select data-placeholder="Choose Account manager" name="account_manager_id" class="chosen-select">
                                    <option></option>
                                    @foreach($accountManagers as $accountManager)
                                        <option value="{{$accountManager->id}}" {{$accountManager->id == $company['account_manager_id']? 'selected':''}}>{{$accountManager->first_name . ' ' . $accountManager->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new FormController();
            controller.init();
        });
    </script>
@endsection
