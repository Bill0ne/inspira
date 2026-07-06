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
                                        <p><strong>Passwort zurücksetzen</strong></p>
                                        <p>Sie haben angefordert, Ihr Passwort bei {{ site_title }} zurückzusetzen. Klicken Sie auf den folgenden Button, um ein neues Passwort zu vergeben:</p>
                                        <p style="text-align: center; margin: 30px 0;">
                                            <a href="{{ reset_link }}" style="background-color: #0d6efd; color: #ffffff; padding: 12px 28px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: bold;">Passwort zurücksetzen</a>
                                        </p>
                                        <p>Falls der Button nicht funktioniert, kopieren Sie diesen Link in Ihren Browser:</p>
                                        <p><a href="{{ reset_link }}">{{ reset_link }}</a></p>
                                        <p style="color: #555;">Wenn Sie diese Anfrage nicht gestellt haben, ignorieren Sie diese E-Mail – Ihr Passwort bleibt unverändert.</p>
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
