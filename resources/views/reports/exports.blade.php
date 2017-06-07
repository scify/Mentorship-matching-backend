<div class="panel margin-top-50">
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
                    <form action="{{ route('exportMentees') }}">
                        <input type="hidden" name="lang" value="gr">
                        <input type="submit" id="mentees-export" class="btn btn-primary btn-ripple custom-disabled-button" value="Download" @if($menteesCount === 0) disabled="disabled" @endif>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-xs-6 report-title">
                    <span>Mentorship sessions to .csv file:</span>
                </div>
                <div class="col-xs-6 report-button">
                    <form action="{{ route('exportSessions') }}">
                        <input type="hidden" name="lang" value="gr">
                        <input type="submit" id="sessions-export" class="btn btn-primary btn-ripple custom-disabled-button" value="Download" @if($mentorshipSessionsCount === 0) disabled="disabled" @endif>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
