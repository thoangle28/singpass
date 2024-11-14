(function($){
  $(document).ready(function(){
    save_api_application();
    save_singpass();
    uploadPermFile();
    saveEmailTemplate();
  });

  //call to save api application
  function save_api_application() {    
    $('#btnAppApi').click(function(){
      $.ajax({
        type: 'POST',
        url: mc_ajax.ajax_url,
        data: {
          action: $('#api_application').val(),
          url: $('#api_endpoint').val(),
          url_mlcb: $('#api_endpoint_mlcb').val(),
          mlcb_uid: $('#mc_mlcb_uid').val(),
          check_otp: $('#api_check_otp_sms').is(':checked'),
          nonce_field: $('#api_nonce_field').val(),
          limited_loan: $('#limited_loan_number').val()
        },
        dataType: "json",
        success: function(response){
          if(response) {
            alert(response.message);
          }
        },
        error: function(){}
      });
    });    
  }

  //call to save api application
  function save_singpass() {    
    $('#btnSingpass').click(function(){
      var formData = $('#singpassForm [name^="singpass_"').serializeArray();

      $.ajax({
        type: 'POST',
        url: mc_ajax.ajax_url,
        data: {
          action: $('#singpass_myinfo').val(),
          form: formData,
          nonce_field: $('#singpass_nonce_field').val()
        },
        dataType: "json",
        success: function(response){
          if(response) {
            console.log(response);
            alert(response.message);
          }
        },
        error: function(){}
      });
    });    
  }
  
  function saveEmailTemplate() {
    $('#btnEmail').click(function(){
      var formData = $('#emailTemplate [name^="email_"').serializeArray();
      var editor_id = 'email_content'; // Replace with editor ID
      var content;
      if (tinymce.get(editor_id)) {
        // Get content from TinyMCE editor
        content = tinymce.get(editor_id).getContent();
      } else {
        // Get content from the textarea
        content = $('#' + editor_id).val();
      }

      $.ajax({
        type: 'POST',
        url: mc_ajax.ajax_url,
        data: {
          action: $('#email_action').val(),
          form: formData,
          email_content: content,
          nonce_field: $('#email_nonce_field').val()
        },
        dataType: "json",
        success: function(response){
          if(response) {
            console.log(response);
            alert(response.message);
          }
        },
        error: function(){}
      });
    });

    $('#btnCustomer').click(function(){
      var formData = $('#emailCustomer [name^="email_"').serializeArray();
      var editor_id = 'email_content1'; // Replace with editor ID
      var content;
      if (tinymce.get(editor_id)) {
        // Get content from TinyMCE editor
        content = tinymce.get(editor_id).getContent();
      } else {
        // Get content from the textarea
        content = $('#' + editor_id).val();
      }

      $.ajax({
        type: 'POST',
        url: mc_ajax.ajax_url,
        data: {
          action: $('#customer_action').val(),
          form: formData,
          email_content: content,
          nonce_field: $('#email_nonce_field').val()
        },
        dataType: "json",
        success: function(response){
          if(response) {
            console.log(response);
            alert(response.message);
          }
        },
        error: function(){}
      });
    });
  }

  function uploadPermFile() {
    $('#upload_image_button').click(function(e) {
      e.preventDefault();
      input = $(this).attr('data-text');
      var file = wp.media({
          title: 'Choose Image',
          button: {
              text: 'Choose Image'
          },  
          multiple: false // mutiple: true if you want to upload multiple files at once
      }).open()
          .on('select', function(e){
              // This will return the selected image from the Media Uploader, the result is an object
              var uploaded_file = file.state().get('selection').first();
              // We convert uploaded_image to a JSON object to make accessing it easier
              // Output to the console uploaded_image              
              var file_url = uploaded_file.toJSON().url;
              // Let's assign the url value to the input field
              $('#' + input).val(file_url);
          });
    });
  }
})(jQuery);