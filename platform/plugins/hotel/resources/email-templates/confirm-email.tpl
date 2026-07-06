{{ header }}

<table width="100%">
    <tbody>
        <tr>
            <td class="wrapper" width="700" align="center">
                <table class="section" cellpadding="0" cellspacing="0" width="700" bgcolor="#f8f8f8">
                    <tr>
                        <td class="column" align="left">
                            <table>
                                <tbody>
                                <tr>
                                    <td align="left" style="padding: 20px 50px;">
                                        <p><strong>Willkommen bei {{ site_title }}!</strong></p>
                                        <p>Vielen Dank für Ihre Registrierung. Bitte bestätigen Sie Ihre E-Mail-Adresse, um Ihr Konto zu aktivieren:</p>
                                        <p style="text-align: center; margin: 30px 0;">
                                            <a href="{{ verify_link }}" style="background-color: #0d6efd; color: #ffffff; padding: 12px 28px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: bold;">E-Mail-Adresse bestätigen</a>
                                        </p>
                                        <p>Falls der Button nicht funktioniert, kopieren Sie diesen Link in Ihren Browser:</p>
                                        <p><a href="{{ verify_link }}">{{ verify_link }}</a></p>
                                        <p style="color: #555;">Wenn Sie sich nicht bei {{ site_title }} registriert haben, können Sie diese E-Mail ignorieren.</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
{{ footer }}
