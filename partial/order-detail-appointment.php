<div style="padding-bottom:20px;">
  <table 
    cellspacing="0" 
    cellpadding="6" 
    border="1" 
    style="margin-bottom: 20px;color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif" 
    width="100%">
  <tr>
    <td>
      
      <p>Save the date of your appointment for the dates: <br />
        <?php echo $appointment->get_start_date() . ' - ' .$appointment->get_end_date(); ?></p>
      
      <p><?php $time = strtotime($appointment->get_start_date()); ?></p>
      
      <p>Your private meeting link to attend your appointment: <br />
      
      <a href="https://<?php echo $settings['jisti_server_domain'].'/'.$meeting_url_path.($sent_to_admin ? '?jwt='.create_jwt_token($time) : '' ); ?>">
        https://<?php echo $settings['jisti_server_domain'].'/'.$meeting_url_path; ?></a></p>
    </td>
  </tr>

</table>
</div>