<div class="wrap">
  <h2><?php esc_html_e( 'Booking Extension Settings' ); ?></h2>
  <form method="POST" action="<?=get_admin_url();?>admin.php?page=booking-ext-settings">
    
    <div class="card">
      <table class="form-table">
        <tbody>
          <tr>
            <th>
              <label for="input-text">Jitsi Server Domain</label>
            </th>
            <td>
              <input type="text" name="jisti_server_domain" value="<?php echo isset($options['jisti_server_domain']) ? $options['jisti_server_domain'] : ''?>" /><br />
            </td>
          </tr>
          <tr>
            <th>
              <label for="input-text">Jitsi App Id</label>
            </th>
            <td>
              <input type="text" name="jisti_app_id" value="<?php echo isset($options['jisti_app_id']) ? $options['jisti_app_id'] : ''?>" /><br />
            </td>
          </tr>
          <tr>
            <th>
              <label for="input-text">Jitsi App Secret</label>
            </th>
            <td>
              <input type="text" name="jisti_app_secret" value="<?php echo isset($options['jisti_app_secret']) ? $options['jisti_app_secret'] : ''?>" /><br />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="card">
      <table class="form-table">
        <tbody>
          <tr>
            <th>
            </th>
            <td>
              <input type="submit" name="Publish" value="Save Settings" class="button" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </form>

</div><!-- .wrap -->