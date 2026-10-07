<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email - TaskFlow</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f4f6f8;
    font-family: Arial, Helvetica, sans-serif;
    color: #1f2937;
">

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background-color: #f4f6f8; padding: 40px 15px;">

    <tr>
        <td align="center">

            <!-- Main container -->
            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="
                       max-width: 600px;
                       width: 100%;
                       background-color: #ffffff;
                       border-radius: 12px;
                       overflow: hidden;
                       box-shadow: 0 4px 20px rgba(0,0,0,0.06);
                   ">

                <!-- Header -->
                <tr>
                    <td align="center"
                        style="
                            background-color: #111827;
                            padding: 28px 30px;
                        ">

                        <div style="
                            font-size: 28px;
                            font-weight: 700;
                            color: #ffffff;
                            letter-spacing: -0.5px;
                        ">
                            Task<span style="color: #6366f1;">Flow</span>
                        </div>

                        <div style="
                            margin-top: 7px;
                            font-size: 13px;
                            color: #9ca3af;
                        ">
                            Team project management made simple
                        </div>

                    </td>
                </tr>


                <!-- Content -->
                <tr>
                    <td style="padding: 45px 45px 35px 45px;">

                        <!-- Icon -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">

                                    <div style="
                                        width: 64px;
                                        height: 64px;
                                        line-height: 64px;
                                        background-color: #eef2ff;
                                        border-radius: 50%;
                                        font-size: 30px;
                                        color: #4f46e5;
                                        text-align: center;
                                    ">
                                        ✓
                                    </div>

                                </td>
                            </tr>
                        </table>


                        <!-- Heading -->
                        <h1 style="
                            margin: 28px 0 12px 0;
                            text-align: center;
                            font-size: 26px;
                            line-height: 34px;
                            color: #111827;
                        ">
                            Verify your email address
                        </h1>


                        <!-- Greeting -->
                        <p style="
                            margin: 0 0 18px 0;
                            text-align: center;
                            font-size: 16px;
                            line-height: 26px;
                            color: #4b5563;
                        ">
                            Hi <strong>{{ $user->name }}</strong>,
                        </p>


                        <!-- Description -->
                        <p style="
                            margin: 0 auto 28px auto;
                            max-width: 470px;
                            text-align: center;
                            font-size: 15px;
                            line-height: 25px;
                            color: #6b7280;
                        ">
                            Welcome to TaskFlow! You're almost ready to start
                            managing your projects and tasks. Please verify your
                            email address to activate your account.
                        </p>


                        <!-- Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">

                                    <a href="{{ $verificationUrl }}"
                                       style="
                                           display: inline-block;
                                           background-color: #4f46e5;
                                           color: #ffffff;
                                           text-decoration: none;
                                           font-size: 15px;
                                           font-weight: 600;
                                           padding: 14px 30px;
                                           border-radius: 7px;
                                           box-shadow: 0 4px 10px rgba(79,70,229,0.25);
                                       ">
                                        Verify Email Address
                                    </a>

                                </td>
                            </tr>
                        </table>


                        <!-- Expiration -->
                        <p style="
                            margin: 25px 0 0 0;
                            text-align: center;
                            font-size: 13px;
                            color: #9ca3af;
                        ">
                            This verification link will expire in
                            <strong>60 minutes</strong>.
                        </p>


                        <!-- Divider -->
                        <div style="
                            height: 1px;
                            background-color: #e5e7eb;
                            margin: 35px 0;
                        "></div>


                        <!-- Fallback URL -->
                        {{-- <p style="
                            margin: 0 0 10px 0;
                            font-size: 13px;
                            line-height: 20px;
                            color: #9ca3af;
                        ">
                            Having trouble with the button? Copy and paste this
                            link into your browser:
                        </p>

                        <p style="
                            margin: 0;
                            word-break: break-all;
                            font-size: 12px;
                            line-height: 19px;
                        ">
                            <a href="{{ $verificationUrl }}"
                               style="
                                   color: #4f46e5;
                                   text-decoration: none;
                               ">
                                {{ $verificationUrl }}
                            </a>
                        </p> --}}


                        <!-- Security notice -->
                        <div style="
                            margin-top: 30px;
                            padding: 14px 16px;
                            background-color: #f9fafb;
                            border-radius: 7px;
                        ">

                            <p style="
                                margin: 0;
                                font-size: 12px;
                                line-height: 19px;
                                color: #6b7280;
                            ">
                                <strong style="color: #374151;">
                                    Didn't create this account?
                                </strong>
                                You can safely ignore this email.
                            </p>

                        </div>

                    </td>
                </tr>


                <!-- Footer -->
                <tr>
                    <td align="center"
                        style="
                            background-color: #f9fafb;
                            border-top: 1px solid #e5e7eb;
                            padding: 24px 30px;
                        ">

                        <div style="
                            font-size: 16px;
                            font-weight: 700;
                            color: #111827;
                            margin-bottom: 8px;
                        ">
                            Task<span style="color: #6366f1;">Flow</span>
                        </div>

                        <p style="
                            margin: 0;
                            font-size: 12px;
                            color: #9ca3af;
                            line-height: 19px;
                        ">
                            This is an automated email from TaskFlow.
                            Please do not reply to this email.
                        </p>

                        <p style="
                            margin: 8px 0 0 0;
                            font-size: 11px;
                            color: #d1d5db;
                        ">
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
