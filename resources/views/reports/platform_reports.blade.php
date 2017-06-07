<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>PLATFORM USERS</h4>
        </div>
    </div><!--.panel-heading-->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Number of Mentors:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $mentorsCount }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Number of Mentees:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $menteesCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel margin-top-50">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>SYSTEM USERS</h4>
        </div>
    </div><!--.panel-heading-->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Number of Admins:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $adminsCount }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Number of Account Managers:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $accountManagersCount }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Number of Matchers:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $matchersCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel margin-top-50">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>SESSIONS</h4>
        </div>
    </div><!--.panel-heading-->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Number of Sessions:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $mentorshipSessionsCount }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Active Sessions:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $activeSessionsCount }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6 report-title">
                    <span>Completed Sessions:</span>
                </div>
                <div class="col-md-6 report-value">
                    <span>{{ $completedSessionsCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>DATA EXPORTS</h4>
        </div>
    </div><!--.panel-heading-->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4">
                <div class="col-xs-6 report-title">
                    <span>Mentors to .csv file:</span>
                </div>
                <div class="col-xs-6 report-button">
                    <form action="{{ route('exportMentors') }}">
                        <input type="hidden" name="lang" value="gr">
                        <input type="submit" id="mentors-export" class="btn btn-primary btn-ripple custom-disabled-button" value="Download" @if($mentorsCount === 0) disabled="disabled" @endif>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-xs-6 report-title">
                    <span>Mentees to .csv file:</span>
                </div>
                <div class="col-xs-6 report-button">
                    <button id="mentees-export" class="btn btn-primary btn-ripple custom-disabled-button" @if($menteesCount === 0) disabled="disabled" @endif>Download</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-xs-6 report-title">
                    <span>Mentorship sessions to .csv file:</span>
                </div>
                <div class="col-xs-6 report-button">
                    <button id="sessions-export" class="btn btn-primary btn-ripple custom-disabled-button" @if($mentorshipSessionsCount === 0) disabled="disabled" @endif>Download</button>
                </div>
            </div>
        </div>
    </div>
</div>
