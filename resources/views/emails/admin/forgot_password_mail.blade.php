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
                  <td class="em_blue em_font_22" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 26px; line-height: 29px; color:#264780; font-weight:bold;">Forgot your Password?</td>
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
                        Don't worry, This happens even to the best of us. We have you covered <br /> Use this OTP below to reset your Password</td>
                    </p>
                </tr>
                <tr>
                  <td height="26" style="height:26px;" class="em_h20">&nbsp;</td>
                </tr>
                <tr>
                  <td align="center" valign="top">
                    <table width="250" style="width:250px; background-color:#6bafb2; border-radius:4px;" border="0" cellspacing="0" cellpadding="0" align="center">
                      <tr>
                        <td class="em_white" height="42" align="center" valign="middle" style="font-family: Arial, sans-serif; font-size: 16px; color:#ffffff; font-weight:bold; height:42px;"><span>{{ $otp }}</span></td>
                      </tr>
                    </table>
                  </td>
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
