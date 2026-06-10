<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $subjectLine }}</title>
    <style>
        @media (prefers-color-scheme: dark) {
            .email-bg {
                background-color: #121212 !important;
            }
            .email-card {
                background-color: #1e1e1e !important;
                border: 1px solid #333333 !important;
            }
            .text-body {
                color: #e4e6eb !important;
            }
            .text-muted {
                color: #a0aab2 !important;
            }
            .header-line-thick {
                border-bottom: 3px solid #e4e6eb !important;
            }
            .header-line-thin {
                border-bottom: 1px solid #e4e6eb !important;
            }
            .footer-box {
                background-color: #152b29 !important;
                border-top: 1px solid #1f3f3c !important;
            }
            .text-aratech {
                color: #44c0b5 !important;
            }
        }
    </style>
</head>
<body
    style="
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f3f4f6;
        -webkit-font-smoothing: antialiased;
    "
>
    <table
        role="presentation"
        width="100%"
        cellspacing="0"
        cellpadding="0"
        border="0"
        class="email-bg"
        style="background-color: #f3f4f6; padding: 40px 0"
    >
        <tr>
            <td align="center">
                <table
                    role="presentation"
                    width="100%"
                    max-width="650"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    class="email-card"
                    style="
                        max-width: 650px;
                        background-color: #ffffff;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                    "
                >
                    <tr>
                        <td height="6" style="background-color: #44c0b5; line-height: 6px; font-size: 6px">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px 10px 40px">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td width="25%" align="left" valign="middle">
                                        <img
                                            src="{{ asset('img/logo-aratech-document.png') }}"
                                            alt="PT. Aratech Nusantara Indonesia"
                                            style="max-height: 65px; max-width: 150px; display: block"
                                        />
                                    </td>

                                    <td width="75%" align="right" valign="middle">
                                        <h2
                                            class="text-body"
                                            style="
                                                margin: 0 0 5px 0;
                                                font-size: 16px;
                                                font-weight: 800;
                                                color: #222222;
                                                text-transform: uppercase;
                                                letter-spacing: 0.5px;
                                            "
                                        >
                                            PT ARATECH NUSANTARA INDONESIA
                                        </h2>
                                        <p
                                            class="text-body"
                                            style="margin: 0 0 3px 0; font-size: 10px; color: #444444; line-height: 1.4"
                                        >The Plaza Office Tower 41st Floor, Jl. M.H. Thamrin No.Kav. 28-30<br />
                                        DKI Jakarta, Indonesia</p>
                                        <p
                                            class="text-muted"
                                            style="margin: 0; font-size: 10px; color: #666666"
                                        >Website: https://aratechnology.id/ | Email: office@aratechnology.id | Telp: 021-75999999</p>
                                    </td>
                                </tr>
                            </table>

                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="margin-top: 15px"
                            >
                                <tr>
                                    <td
                                        class="header-line-thick"
                                        style="border-bottom: 3px solid #222222; line-height: 3px; font-size: 3px"
                                    >
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr>
                                    <td style="line-height: 2px; font-size: 2px">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td
                                        class="header-line-thin"
                                        style="border-bottom: 1px solid #222222; line-height: 1px; font-size: 1px"
                                    >
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td
                            class="text-body"
                            style="
                                padding: 20px 40px 40px 40px;
                                font-size: 14px;
                                line-height: 1.6;
                                color: #333333;
                                text-align: justify;
                            "
                        >
                            {!! $bodyContent !!}
                        </td>
                    </tr>

                    <tr>
                        <td
                            class="footer-box"
                            style="padding: 25px 40px; background-color: #e0f6f4; text-align: center"
                        >
                            <p
                                class="text-aratech"
                                style="margin: 0; font-size: 13px; color: #37a097; font-weight: 600"
                            >COREVO Enterprise CRM System</p>
                            <p
                                class="text-muted"
                                style="margin: 6px 0 0 0; font-size: 11px; color: #6b8280; line-height: 1.5"
                            >Pesan ini dihasilkan secara otomatis oleh sistem CRM PT Aratech Nusantara Indonesia. Harap tidak membalas langsung (no-reply) ke alamat email pengirim ini.</p>
                            <p
                                style="margin: 15px 0 0 0; font-size: 10px; color: #8ba3a0"
                            >&copy; {{ date('Y') }} PT Aratech Nusantara Indonesia. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
