$(document).ready(function() {
    $(".link-accounts-button").click(function(ev) {
        ev.preventDefault();
        ev.stopPropagation();

        if ($(this).hasClass("disabled")) return;

        $(this).addClass("disabled");

        var project = $(this).attr('project');
        var email = document.getElementById(project + "InputEmail").value;
        var username = document.getElementById(project + "InputUsername").value;
        var userid = document.getElementById(project + "InputUserid").value;

        console.log(project + " " + userid + " '" + username + "' '" + email + "'");

        var link_accounts_button = $(this);

        $.ajax({
            type: 'POST',
            url: './link_accounts/link_account.php',
            data : {
                        project : project,
                        email : email,
                        username : username,
                        userid : userid
                   },
            dataType : 'json',
            success : function(response) {
                if (response['status'] === 'success') {
                    link_accounts_button.text("Account Successfully Linked");
                } else {
                    link_accounts_button.removeClass("btn-primary");
                    link_accounts_button.addClass("btn-danger");
                    link_accounts_button.text("Account Unsuccessfully Linked");
                    alert(response['error_msg']);
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {
                link_accounts_button.removeClass("btn-primary");
                link_accounts_button.addClass("btn-danger");
                link_accounts_button.text("Account Unsuccessfully Linked");
                alert(errorThrown);
            },
            async: true
        });
    });
});
