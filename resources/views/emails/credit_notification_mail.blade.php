@extends('emails.layouts.app')

@section('content')
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="em_full_wrap" align="center" bgcolor="#efefef">
    <tr>
      <td align="center" valign="top" class="em_aside5">
        <table align="center" width="650" border="0" cellspacing="0" cellpadding="0" class="em_main_table" style="width:650px; table-layout:fixed;">
          <tr>
            <td align="center" valign="top" style="padding:0 25px; background-color:#ffffff;" class="em_aside10">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                  <td height="45" style="height:45px;" class="em_h20">&nbsp;</td>
                </tr>
                <tr>
                  <td class="em_blue em_font_22" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 26px; line-height: 29px; color:#264780; font-weight:bold;">Account Credit Notification</td>
                </tr>
                <tr>
                  <td height="14" style="height:14px; font-size:0px; line-height:0px;">&nbsp;</td>
                </tr>
                <tr>
                  <td class="em_grey" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 16px; line-height: 26px; color:#434343;">
                    <p>
                        Hello {{ $name }}
                    </p>
                    <p>
                        Your account has just been credited
                        <ul>
                            <li>Bank: Sparkle Microfinance Bank</li>
                            <li>Account Number: {{ $account_number }}</li>
                            <li>Amount: NGN{{ number_format($amount, 2) }}</li>
                            <li>Balance: NGN{{ number_format($balance, 2) }} </li>
                        </ul>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td height="26" style="height:26px;" class="em_h20">&nbsp;</td>
                </tr>
                <tr>
                  <td height="25" style="height:25px;" class="em_h20">&nbsp;</td>
                </tr>
                <tr>
                  <td height="44" style="height:44px;" class="em_h20">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
@endsection
