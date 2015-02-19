@extends('emails/master-simple')

@section('title') {{ config('app.name') }} Account Activation @endsection

@section('content')
    <table>
        <tr>
            <td>
                <h1>{{ config('app.name') }} Account Activation</h1>
                <p>Hi there {{ $user->name }},</p>
                <p>You are receiving this e-mail because someone (hopefully you) attempted to register an account on
                {!! link_to_route('home', config('app.name')) !!} using this address.</p>
                <table>
                    <tr>
                        <td class="padding">
                            <p>
                                <a href="{{ route('users.auth.doActivate', ['uid' => $user->id, 'code' => $user->getActivationCode()]) }}" class="btn-primary">
                                    Click here to complete your registration
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
                <p>If you are unable to access the above link for any reason, please log into your account and enter in
                the following activation code when prompted:</p>
                <p><strong>{{ $user->getActivationCode() }}</strong></p>
                <p>If you run into any problems, please feel free to contact us at {{ \HTML::email( config('app.email') ) }}.</p>
            </td>
        </tr>
    </table>
@endsection

@section('unsubscribe')
    <td align="center">
        <p>Didn't request this account?
            <a href="{{ route('users.auth.cancelRegistration', ['uid' => $user->id, 'code' => $user->getActivationCode()]) }}">
                <unsubscribe>Cancel Registration</unsubscribe>
            </a>.
        </p>
    </td>
@overwrite