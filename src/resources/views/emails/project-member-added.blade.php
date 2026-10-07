<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>You've been added to a project</title>

    <style>
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px !important;
            }

            .email-container {
                width: 100% !important;
                border-radius: 12px !important;
            }

            .email-header {
                padding: 28px 20px !important;
            }

            .email-content {
                padding: 35px 20px !important;
            }

            .email-title {
                font-size: 25px !important;
                line-height: 1.3 !important;
            }

            .email-description {
                font-size: 15px !important;
                line-height: 1.7 !important;
            }

            .footer {
                padding: 22px 20px !important;
            }
        }
    </style>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f5f7;
    font-family: Arial, Helvetica, sans-serif;
    color: #1f2937;
">

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    class="email-wrapper"
    style="
        background-color: #f4f5f7;
        padding: 40px 20px;
    "
>
    <tr>
        <td align="center">

            <table
                width="600"
                cellpadding="0"
                cellspacing="0"
                border="0"
                class="email-container"
                style="
                    width: 100%;
                    max-width: 600px;
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
                "
            >

                <!-- Header -->
                <tr>
                    <td
                        align="center"
                        class="email-header"
                        style="
                            background-color: #111827;
                            padding: 30px 20px;
                        "
                    >
                        <div style="
                            font-size: 28px;
                            line-height: 1;
                            font-weight: 700;
                            letter-spacing: -1px;
                            color: #ffffff;
                        ">
                            Task<span style="color: #6366f1;">Flow</span>
                        </div>

                        <div style="
                            margin-top: 8px;
                            font-size: 13px;
                            line-height: 20px;
                            color: #9ca3af;
                        ">
                            Team project management made simple
                        </div>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td
                        align="center"
                        class="email-content"
                        style="
                            padding: 45px 40px 38px;
                        "
                    >

                        <!-- Icon -->
                        <table
                            width="64"
                            height="64"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                width: 64px;
                                height: 64px;
                                margin-bottom: 25px;
                            "
                        >
                            <tr>
                                <td
                                    align="center"
                                    valign="middle"
                                    style="
                                        width: 64px;
                                        height: 64px;
                                        border-radius: 50%;
                                        background-color: #eef0ff;
                                        color: #4f46e5;
                                        font-size: 30px;
                                    "
                                >
                                    &#128101;
                                </td>
                            </tr>
                        </table>

                        <!-- Title -->
                        <h1
                            class="email-title"
                            style="
                                margin: 0 0 18px;
                                font-size: 27px;
                                line-height: 36px;
                                font-weight: 700;
                                color: #111827;
                            "
                        >
                            You've been added to a project
                        </h1>

                        <!-- Greeting -->
                        <p style="
                            margin: 0 0 22px;
                            font-size: 16px;
                            line-height: 26px;
                            color: #6b7280;
                        ">
                            Hi
                            <strong style="color: #374151;">
                                {{ $user->name }}
                            </strong>,
                        </p>

                        <!-- Description -->
                        <p
                            class="email-description"
                            style="
                                max-width: 500px;
                                margin: 0 auto 28px;
                                font-size: 16px;
                                line-height: 27px;
                                color: #6b7280;
                            "
                        >
                            You have been added to the following TaskFlow
                            project:
                        </p>

                        <!-- Project information -->
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                background-color: #f9fafb;
                                border-radius: 8px;
                                margin-bottom: 28px;
                            "
                        >
                            <tr>
                                <td style="
                                    padding: 20px;
                                    text-align: left;
                                ">

                                    <p style="
                                        margin: 0 0 8px;
                                        font-size: 12px;
                                        line-height: 18px;
                                        color: #9ca3af;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                    ">
                                        Project
                                    </p>

                                    <p style="
                                        margin: 0 0 16px;
                                        font-size: 18px;
                                        line-height: 26px;
                                        font-weight: 700;
                                        color: #111827;
                                    ">
                                        {{ $project->name }}
                                    </p>

                                    <p style="
                                        margin: 0 0 8px;
                                        font-size: 12px;
                                        line-height: 18px;
                                        color: #9ca3af;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                    ">
                                        Your role
                                    </p>

                                    <p style="
                                        margin: 0;
                                        font-size: 15px;
                                        line-height: 22px;
                                        color: #4f46e5;
                                        font-weight: 600;
                                    ">
                                        {{ ucfirst($role) }}
                                    </p>

                                </td>
                            </tr>
                        </table>

                        <p style="
                            margin: 0;
                            font-size: 14px;
                            line-height: 23px;
                            color: #9ca3af;
                        ">
                            You can now collaborate with your team on this
                            project through TaskFlow.
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td
                        align="center"
                        class="footer"
                        style="
                            padding: 24px 30px;
                            background-color: #f9fafb;
                            border-top: 1px solid #e5e7eb;
                        "
                    >
                        <div style="
                            font-size: 17px;
                            line-height: 22px;
                            font-weight: 700;
                            color: #111827;
                        ">
                            Task<span style="color: #6366f1;">Flow</span>
                        </div>

                        <p style="
                            margin: 7px 0 0;
                            font-size: 12px;
                            line-height: 19px;
                            color: #9ca3af;
                        ">
                            This is an automated email from TaskFlow.
                            Please do not reply to this email.
                        </p>

                        <p style="
                            margin: 6px 0 0;
                            font-size: 12px;
                            line-height: 19px;
                            color: #d1d5db;
                        ">
                            &copy; {{ date('Y') }} TaskFlow.
                            All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
