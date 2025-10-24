{{ header }}

<table width="100%">
    <tbody>
    <tr>
        <td class="wrapper" width="700" align="center">
            <table class="section" cellpadding="0" cellspacing="0" width="700" bgcolor="#f8f8f8">
                <tr>
                    <td align="left" style="padding: 20px 50px;">
                        <p>
                            <strong>Hello {{ booking_name }},</strong>
                        </p>
                        <p>
                            Your booking has been updated successfully. Here are the new details:
                        </p>
                        <ul>
                            <li><strong>Course:</strong> {{ course_name }}</li>
                            <li><strong>Session:</strong> {{ session_name }}</li>
                        </ul>
                        <p>
                            You can view your booking details here:
                            <a href="{{ booking_link }}">View Booking</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>

{{ footer }}
