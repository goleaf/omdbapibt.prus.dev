@php($appName = config('app.name'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>{{ __('emails.welcome.subject', ['app' => $appName, 'name' => $user->name]) }}</title>
    </head>
    <body style="margin:0;padding:0;font-family:'Inter',sans-serif;background-color:#0f172a;color:#e2e8f0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;">
            <tr>
                <td align="center">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#1e293b;border-radius:24px;padding:40px;">
                        <tr>
                            <td style="font-size:24px;font-weight:700;text-align:center;padding-bottom:16px;color:#34d399;">
                                {{ $appName }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:18px;font-weight:600;padding-bottom:16px;">
                                {{ __('emails.welcome.greeting', ['name' => $user->name]) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;line-height:1.6;padding-bottom:16px;color:#cbd5f5;">
                                {{ __('emails.welcome.intro', ['app' => $appName]) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;line-height:1.6;padding-bottom:16px;color:#cbd5f5;">
                                {{ __('emails.welcome.features') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;line-height:1.6;padding-bottom:24px;color:#cbd5f5;">
                                {{ __('emails.welcome.cta', ['app' => $appName]) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;color:#94a3b8;">
                                {{ __('emails.welcome.signature', ['app' => $appName]) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
