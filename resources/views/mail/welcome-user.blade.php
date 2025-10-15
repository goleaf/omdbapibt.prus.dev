<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('mail.welcome.subject', ['name' => $user->name]) }}</title>
</head>
<body style="margin:0;padding:0;background-color:#0f172a;color:#e2e8f0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f172a;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#1e293b;border-radius:24px;padding:40px;">
                    <tr>
                        <td style="font-size:16px;line-height:24px;color:#a5b4fc;text-transform:uppercase;letter-spacing:0.3em;font-weight:600;">
                            OMDb Stream
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:24px;font-size:18px;line-height:28px;font-weight:600;color:#f8fafc;">
                            {{ __('mail.welcome.greeting', ['name' => $user->name]) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:16px;font-size:16px;line-height:26px;color:#e2e8f0;">
                            {{ __('mail.welcome.intro') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:16px;font-size:16px;line-height:26px;color:#e2e8f0;">
                            {{ __('mail.welcome.body') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:32px;">
                            <a href="{{ route('dashboard', ['locale' => $user->preferred_locale ?? app()->getLocale()]) }}" style="display:inline-block;background-color:#34d399;color:#064e3b;padding:12px 28px;border-radius:9999px;font-weight:600;text-decoration:none;font-size:16px;">
                                {{ __('mail.welcome.cta') }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:32px;font-size:16px;line-height:26px;color:#e2e8f0;">
                            {{ __('mail.welcome.salutation') }}<br>
                            {{ __('mail.welcome.signature') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
