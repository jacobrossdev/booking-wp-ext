<div style="padding-bottom:20px;">
  <table 
    cellspacing="0" 
    cellpadding="6" 
    border="1" 
    style="margin-bottom: 20px;color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif" 
    width="100%">

    <?php foreach( $meetings_ordered as $mo ):
      $link = $sent_to_admin ? $mo['admin_link'] : $mo['link']; ?>

      <tr>
        <td>
          
          <p>Save the date of your appointment for the dates: <br />
            <?php echo $mo['start_date'] . ' - ' .$mo['end_date']; ?></p>
                    
          <p>Your private meeting link to attend your appointment: <br />
            <a href="<?php echo $link; ?>"><?php echo $link;?></a></p>

        </td>
      </tr>

    <?php endforeach;?>

</table>
</div>