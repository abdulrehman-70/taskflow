<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Task Assigned</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f5f9; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f5f9; padding:40px 15px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#111827; padding:24px 30px; text-align:center;">

                        <div style="font-size:26px; font-weight:bold; color:#ffffff;">
                            Task<span style="color:#8b5cf6;">Flow</span>
                        </div>

                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:40px 35px;">

                        <h1 style="margin:0 0 20px; font-size:24px; color:#111827;">
                            New task assigned to you
                        </h1>

                        <p style="margin:0 0 20px; font-size:16px; line-height:1.6; color:#4b5563;">
                            Hello {{ $user->name }},
                        </p>

                        <p style="margin:0 0 25px; font-size:16px; line-height:1.6; color:#4b5563;">
                            You have been assigned a new task in
                            <strong>{{ $project->name }}</strong>.
                        </p>

                        <!-- Task Card -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background-color:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; margin-bottom:30px;">
                            <tr>
                                <td style="padding:20px;">

                                    <p style="margin:0 0 8px; font-size:13px; color:#6b7280;">
                                        TASK
                                    </p>

                                    <p style="margin:0 0 18px; font-size:18px; font-weight:bold; color:#111827;">
                                        {{ $task->title }}
                                    </p>

                                    @if($task->description)
                                        <p style="margin:0 0 15px; font-size:14px; line-height:1.6; color:#4b5563;">
                                            {{ $task->description }}
                                        </p>
                                    @endif

                                    <p style="margin:0; font-size:14px; color:#6b7280;">
                                        Priority:
                                        <strong style="color:#111827;">
                                            {{ ucfirst($task->priority) }}
                                        </strong>
                                    </p>

                                    @if($task->due_date)
                                        <p style="margin:8px 0 0; font-size:14px; color:#6b7280;">
                                            Due date:
                                            <strong style="color:#111827;">
                                                {{ $task->due_date->format('M d, Y') }}
                                            </strong>
                                        </p>
                                    @endif

                                </td>
                            </tr>
                        </table>

                        <!-- CTA -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">

                                    <a href="{{ config('app.frontend_url') }}"
                                       style="display:inline-block; background-color:#8b5cf6; color:#ffffff; text-decoration:none; padding:13px 28px; border-radius:8px; font-size:15px; font-weight:bold;">
                                        View Task
                                    </a>

                                </td>
                            </tr>
                        </table>

                        <p style="margin:30px 0 0; font-size:13px; line-height:1.6; color:#9ca3af; text-align:center;">
                            You received this email because a task was assigned to your
                            TaskFlow account.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding:20px 30px; background-color:#f9fafb; border-top:1px solid #e5e7eb; text-align:center;">

                        <p style="margin:0; font-size:13px; color:#6b7280;">
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
