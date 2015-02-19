@extends('emails/master-simple')

@section('title') {{ config('app.name') }} Account Recovery @endsection

@section('content')
    <table>
        <tr>
            <td>
                <h1>{{ config('app.name') }} Account Recovery</h1>
                <p>Hi there {{ $user->name }},</p>
                <p>You are receiving this e-mail because someone (hopefully you) initiated a password recovery request
                    for your account. If you did NOT make this request, please discard this e-mail.</p>
                <table>
                    <tr>
                        <td class="padding">
                            <p>
                                <a href="{{ route('users.auth.reset', ['token' => $token]) }}" class="btn-primary">
                                    Click here to reset your accounts password
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
                <p>If you are unable to access the above link for any reason, please copy and paste the following URL
                    into your browser manually:</p>
                <p><strong>{{ route('users.auth.reset', ['token' => $token]) }}</strong></p>
                <p>If you run into any problems, please feel free to contact us at {{ \HTML::email( config('app.email') ) }}.</p>
            </td>
        </tr>
    </table>
@endsection
