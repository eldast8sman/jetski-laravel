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
                  <td class="em_blue em_font_22" align="center" valign="top" style="font-family: Arial, sans-serif; font-size: 26px; line-height: 29px; color:#264780; font-weight:bold;">Membership Registration</td>
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
                        You have just been added as a member on the Lagos Jetski Application. <br /> Please proceed to download the Mobile App on your device and follow the below steps
                    </p>
                    <ul>
                      <li>Download the Jetski Mobile App from <span><a href="https://play.google.com/store/apps/details?id=com.jetskilagos.api">
                        <img src="{{ asset('logo/playstore.png') }}" alt="Google Play Store" style="width:50px; height:auto">
                      </a></span></li>
                      <li>Click on the Signup Button</li>
                      <li>Provide this as Email: <strong>{{ $email }}</strong></li>
                      <li>Provide this as Pin: <strong>{{ $token }}</strong></li>
                      <li>Click on the <strong>Continue</strong> button and provide your Password</li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td height="26" style="height:26px;" class="em_h20">&nbsp;</td>
                </tr>
                {{-- <tr>
                  <td align="center" valign="top">
                    <table width="250" style="width:250px; background-color:#6bafb2; border-radius:4px;" border="0" cellspacing="0" cellpadding="0" align="center">
                      <tr>
                        <td class="em_white" height="42" align="center" valign="middle" style="font-family: Arial, sans-serif; font-size: 16px; color:#ffffff; font-weight:bold; height:42px;"><a href="{{ $link }}">Activate Account</a></td>
                      </tr>
                    </table>
                  </td>
                </tr> --}}
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
