<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Overdue</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f5f7; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f5f7; padding:40px 15px;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background:#111827; padding:24px; text-align:center;">
                        <div style="font-size:26px; font-weight:bold; color:#ffffff;">
                            Task<span style="color:#8b5cf6;">Flow</span>
                        </div>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:40px 35px;">

                        <h2 style="margin:0 0 20px; color:#111827;">
                            Task Overdue
                        </h2>

                        <p style="margin:0 0 20px; color:#4b5563; line-height:1.6;">
                            Hi {{ $user->name }},
                        </p>

                        <p style="margin:0 0 25px; color:#4b5563; line-height:1.6;">
                            The following task is overdue and requires your attention.
                        </p>

                        <!-- Task Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background:#f9fafb; border-radius:8px; margin-bottom:25px;">
                            <tr>
                                <td style="padding:20px;">

                                    <p style="margin:0 0 10px; font-size:18px; font-weight:bold; color:#111827;">
                                        {{ $task->title }}
                                    </p>

                                    @if($task->due_date)
                                        <p style="margin:0; color:#dc2626;">
                                            Due date:
                                            {{ $task->due_date->format('M d, Y') }}
                                        </p>
                                    @endif

                                    <p style="margin:10px 0 0; color:#6b7280;">
                                        Priority: {{ ucfirst($task->priority) }}
                                    </p>

                                </td>
                            </tr>
                        </table>

                        <p style="margin:0; color:#4b5563; line-height:1.6;">
                            Please review the task and update its status or due date as appropriate.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:25px 35px; background:#f9fafb; text-align:center;">
                        <p style="margin:0; color:#6b7280; font-size:13px;">
                            This is an automated notification from TaskFlow.
                        </p>

                        <p style="margin:8px 0 0; color:#9ca3af; font-size:12px;">
                            © {{ date('Y') }} TaskFlow. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
