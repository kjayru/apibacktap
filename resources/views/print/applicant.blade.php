@extends('print.layout', ['title' => 'Application · ' . $applicant->firstname . ' ' . $applicant->lastname])

@section('content')
    @php
        $education = $applicant->education;
        $military = $applicant->military;
        $disclaimer = $applicant->disclaimer;
        $dias = \App\Filament\Resources\Information\Schemas\InformationInfolist::availableDays($applicant->whichday);
    @endphp

    <h1>Employment application</h1>
    <p class="muted">
        Received {{ $applicant->created_at?->timezone(config('app.admin_timezone'))?->format('M d, Y H:i') }}
    </p>

    <h2>Applicant information</h2>
    <table>
        <tr><th>Name</th><td>{{ $applicant->firstname }} {{ $applicant->mi }} {{ $applicant->lastname }}</td></tr>
        <tr><th>Date</th><td>{{ $applicant->date }}</td></tr>
        <tr><th>Address</th><td>{{ $applicant->address }} {{ $applicant->apartment }}, {{ $applicant->city }} {{ $applicant->state }} {{ $applicant->zipcode }}</td></tr>
        <tr><th>Phone</th><td>{{ $applicant->phone }}</td></tr>
        <tr><th>Email</th><td>{{ $applicant->email }}</td></tr>
        <tr><th>Date of birth</th><td>{{ $applicant->birthday }}</td></tr>
        <tr><th>Social security no.</th><td>{{ $applicant->socialnumber }}</td></tr>
        <tr><th>Place of birth</th><td>{{ $applicant->placebirth }}</td></tr>
        <tr><th>Position applied for and desired pay</th><td>{{ $applicant->appliedpay }}</td></tr>
        <tr><th>Which shift are you applying for?</th><td>{{ $applicant->whichshift }}</td></tr>
        <tr><th>Which days are you available?</th><td>{{ $dias }}</td></tr>
        <tr><th>Are you a citizen of the United States?</th><td>{{ $applicant->citizen }}</td></tr>
        <tr><th>If no, are you authorized to work in the U.S.?</th><td>{{ $applicant->authorized }}</td></tr>
        <tr><th>Have you ever worked for this company?</th><td>{{ $applicant->worked }} {{ $applicant->when }}</td></tr>
        <tr><th>Have you ever been convicted of a felony?</th><td>{{ $applicant->convicted }} {{ $applicant->explain1 }}</td></tr>
        <tr><th>Are you currently under indictment for a crime?</th><td>{{ $applicant->indictment }} {{ $applicant->explain2 }}</td></tr>
    </table>

    @if ($education)
        <h2>Education and training</h2>
        <table>
            <tr><th>Did you graduate High School?</th><td>{{ $education->graduatehigh }}</td></tr>
            <tr><th>High school</th><td>{{ $education->hightschool }} ({{ $education->highfrom }} – {{ $education->hightto }})</td></tr>
            <tr><th>Did you graduate College?</th><td>{{ $education->graduatecollage }}</td></tr>
            <tr><th>College</th><td>{{ $education->collaganame }} ({{ $education->collagefrom }} – {{ $education->collageto }})</td></tr>
            <tr><th>Major</th><td>{{ $education->whatmayor }}</td></tr>
            <tr><th>Level completed</th><td>{{ $education->completed }}</td></tr>
            <tr><th>Active security registration card</th><td>{{ $education->activecard }} {{ $education->officer }}</td></tr>
            <tr><th>Firearm</th><td>{{ $education->firearm }} {{ $education->holster }}</td></tr>
            <tr><th>Others</th><td>{{ $education->others }}</td></tr>
        </table>
    @endif

    @if ($applicant->references->isNotEmpty())
        <h2>References</h2>
        <table>
            @foreach ($applicant->references as $reference)
                <tr>
                    <th>Reference #{{ $loop->iteration }}</th>
                    <td>
                        {{ $reference->fullname }} · {{ $reference->relationship }}<br>
                        {{ $reference->companyref }} · {{ $reference->phoneref }}<br>
                        {{ $reference->addressreference }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($applicant->employments->isNotEmpty())
        <h2>Previous employment</h2>
        <table>
            @foreach ($applicant->employments as $employment)
                <tr>
                    <th>Employer #{{ $loop->iteration }}</th>
                    <td>
                        {{ $employment->company }} · {{ $employment->phoneemp }}<br>
                        {{ $employment->addressempl }}<br>
                        Supervisor: {{ $employment->supervisor }} · {{ $employment->jobtitle }}<br>
                        {{ $employment->from }} – {{ $employment->to }} · ${{ $employment->starting }} – ${{ $employment->ending }}<br>
                        Reason: {{ $employment->reason }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($military)
        <h2>Military service</h2>
        <table>
            <tr><th>Branch</th><td>{{ $military->branch }}</td></tr>
            <tr><th>From / To</th><td>{{ $military->from }} – {{ $military->to }}</td></tr>
            <tr><th>Rank at discharge</th><td>{{ $military->rank }}</td></tr>
            <tr><th>Type of discharge</th><td>{{ $military->type }}</td></tr>
            <tr><th>If other than honorable, explain</th><td>{{ $military->explain }}</td></tr>
        </table>
    @endif

    <h2>Disclaimer and signature</h2>
    <p>
        I certify that my answers are true and complete to the best of my knowledge. If this application leads to
        employment, I understand that false or misleading information in my application or interview may result in my
        release.
    </p>
    <table>
        <tr><th>Signature</th><td>{{ $disclaimer?->signature }}</td></tr>
        <tr><th>Date</th><td>{{ $disclaimer?->datedisclamer }}</td></tr>
        <tr>
            <th>Attached files</th>
            <td>
                @forelse ($disclaimer?->archivos ?? [] as $archivo)
                    {{ $archivo->original_name ?: basename($archivo->file) }}<br>
                @empty
                    No attached files
                @endforelse
            </td>
        </tr>
    </table>
@endsection
