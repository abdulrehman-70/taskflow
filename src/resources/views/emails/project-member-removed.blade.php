<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Removed from Project</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: Arial, sans-serif; color: #333333;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7; padding: 40px 20px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color: #111827; padding: 24px; text-align: center;">
                        <span style="font-size: 26px; font-weight: bold; color: #ffffff;">
                            Task<span style="color: #8b5cf6;">Flow</span>
                        </span>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding: 40px 35px;">

                        <h2 style="margin: 0 0 20px; color: #111827;">
                            Project membership updated
                        </h2>

                        <p style="margin: 0 0 15px;">
                            Hello {{ $user->name }},
                        </p>

                        <p style="margin: 0 0 20px; line-height: 1.6;">
                            You have been removed from the following TaskFlow project:
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background-color: #f9fafb; border-radius: 8px; margin-bottom: 25px;">
                            <tr>
                                <td style="padding: 18px;">
                                    <strong style="color: #111827;">
                                        {{ $project->name }}
                                    </strong>
                                </td>
                            </tr>
                        </table>

                        <p style="margin: 0; line-height: 1.6; color: #6b7280;">
                            You will no longer have access to this project or its tasks.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding: 20px 35px; background-color: #f9fafb; text-align: center;">
                        <p style="margin: 0; font-size: 13px; color: #9ca3af;">
                            This is an automated email from TaskFlow.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
