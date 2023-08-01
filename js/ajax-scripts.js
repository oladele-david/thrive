// This function updates user profile settings
function updateProfile() {

    let firstName = $("#firstName").val();
    let lastName = $("#lastName").val();
    let otherName = $("#otherName").val();
    let motherMaidenName = $("#motherMaidenName").val();
    let bvn = $("#bvn").val();
    let nin = $("#nin").val();
    let phoneNo = $("#phoneNo").val();
    let emailId = $("#emailId").val();
    let gender = $("#gender").val();
    let dateOfBirth = $("#dateOfBirth").val();
    let address = $("#address").val();
    let countryId = $("#countryId").val();
    let stateId = $("#stateId").val();
    let city = $("#city").val();
    let defaultBankId = $("#defaultBankId").val();
    let bankAccountNo = $("#bankAccountNo").val();
    let bankAccountName = $("#bankAccountName").val();
    let reeveAccountNo = $("#reeveAccountNo").val();

                
    $("#buttonSave").css({"display":"none"});
    $("#loading_spinner").css({"display":"block"});
    $.ajax
    ({
        type:'post',
        url:'profile.php',
        data:{
            updateProfile:"updateProfile",
            lastName:lastName,
            firstName:firstName,
            otherName:otherName,
            motherMaidenName:motherMaidenName,
            bvn:bvn,
            nin:nin,
            phoneNo:phoneNo,
            emailId:emailId,
            gender:gender,
            dateOfBirth:dateOfBirth,
            address:address,
            countryId:countryId,
            stateId:stateId,
            city:city,
            defaultBankId:defaultBankId,
            bankAccountNo:bankAccountNo,
            bankAccountName:bankAccountName,
            reeveAccountNo:reeveAccountNo
        },
        success:function(data) {
            // console.log(data );
            var jsonData = JSON.parse(data)

            if (jsonData.response == "success") {
                $("#loading_spinner").css({"display":"none"});
                $("#buttonSave").css({"display":"block"});

                swal({
                    
                    type: jsonData.response,
                    title: jsonData.title,
                    text: jsonData.msg,
                    confirmButtonText: 'Continue'
                })
            }
            else {
                $("#loading_spinner").css({"display":"none"});
                $("#buttonSave").css({"display":"block"});

                swal({
                    type: jsonData.response,
                    title: jsonData.title,
                    text: jsonData.msg,
                    confirmButtonText: 'Try Again'
                });
                // alert("Wrong Details" + response);
                // console.log(data + " - " + firstName + " - " + lastName + " - " + otherName + " - " + motherMaidenName + " - " + bvn
                //         + " - " + nin + " - " + phoneNo + " - " + emailId  + " - " + gender  + " - " + dateOfBirth  + " - " + address
                //         + " - " + countryId  + " - " + stateId  + " - " + city  + " - " + defaultBankId  + " - " + bankAccountNo  + " - " + bankAccountName
                //         + " - " + reeveAccountNo
                // );
            }
        }
    });


    console.log(
        firstName + " - " + lastName + " - " + otherName + " - " + motherMaidenName + " - " + bvn
        + " - " + nin + " - " + phoneNo + " - " + emailId  + " - " + gender  + " - " + dateOfBirth  + " - " + address
        + " - " + countryId  + " - " + stateId  + " - " + city  + " - " + defaultBankId  + " - " + bankAccountNo  + " - " + bankAccountName
        + " - " + reeveAccountNo
    ) 
}

// This function updates User's Password

function updatePassword() {

    let password = $('#password').val();
    let confirmPassword = $('#confirmPassword').val();

    if (password == confirmPassword) {
        $("#buttonSavePassword").css({"display":"none"});
        $("#loading_spinners").css({"display":"block"});
        $.ajax
        ({
            type:'post',
            url:'profile.php',
            data:{
                updatePassword:"updatePassword",
                password:password
            },
            success:function(data) {
                console.log(data);
                var jsonData = JSON.parse(data)

                if (jsonData.response == "success") {
                    $("#loading_spinners").css({"display":"none"});
                    $("#buttonSavePassword").css({"display":"block"});
                    $('#password').val('');
                    $('#confirmPassword').val('');

                    swal({
                        
                        type: jsonData.response,
                        title: jsonData.title,
                        text: jsonData.msg,
                        confirmButtonText: 'Continue',
                    })
                }
                else {
                    $("#loading_spinners").css({"display":"none"});
                    $("#buttonSavePassword").css({"display":"block"});
                    $('#password').val('');
                    $('#confirmPassword').val('');

                    swal({
                        type: jsonData.response,
                        title: jsonData.title,
                        text: jsonData.msg,
                        confirmButtonText: 'Try Again'
                    });
                    // alert("Wrong Details" + response);
                    //console.log(response + " - " + clientAccessID + " - " + phoneNo + " - " + wardId + " - " + password);
                }
            }
        });
        console.log("Values entered are == " + password + " -- " + confirmPassword);
    } else {
        swal({
            type: "error",
            title: "Oh No!!",
            text: "Your passwords didn't match",
            confirmButtonText: "Try Again"
        });

        console.log("wrong password -- Values entered are == " + password + " -- " + confirmPassword);
        
    }
}

// This function updates Transaction Pin

function updateTransactionPin() {

    let transactionPin = $('#transactionPin').val();
    let confirmTransactionPin = $('#confirmTransactionPin').val();

    if (transactionPin == confirmTransactionPin) {
        $("#buttonSavePin").css({"display":"none"});
        $(".loading_spinners").css({"display":"block"});
        $.ajax
        ({
            type:'post',
            url:'security.php',
            data:{
                updateTransactionPin:"updateTransactionPin",
                transactionPin:transactionPin
            },
            success:function(data) {
                var jsonData = JSON.parse(data)

                if (jsonData.response == "success") {
                    $(".loading_spinners").css({"display":"none"});
                    $("#buttonSavePin").css({"display":"block"});
                    $('#transactionPin').val('');
                    $('#confirmTransactionPin').val('');

                    swal({
                        
                        type: jsonData.response,
                        title: jsonData.title,
                        text: jsonData.msg,
                        confirmButtonText: 'Continue',
                    })
                    // alert("Wrong Details" + response);
                    //console.log("Values entered are == " + transactionPin + " -- " + confirmTransactionPin + " -- " + response);
                }
                else {
                    $(".loading_spinners").css({"display":"none"});
                    $("#buttonSavePin").css({"display":"block"});
                    $('#transactionPin').val('');
                    $('#confirmTransactionPin').val('');

                    swal({
                        type: jsonData.response,
                        title: jsonData.title,
                        text: jsonData.msg,
                        confirmButtonText: 'Try Again'
                    });
                    // alert("Wrong Details" + response);
                    //console.log("Values entered are == " + transactionPin + " -- " + confirmTransactionPin + " -- " + response);

                    //console.log(response + " - " + clientAccessID + " - " + phoneNo + " - " + wardId + " - " + password);
                }
            }
        });
        //console.log("Values entered are == " + transactionPin + " -- " + confirmTransactionPin);
    } else {
        swal({
            type: "error",
            title: "Oh No!!",
            text: "Your Transaction Pins didn't match",
            confirmButtonText: "Try Again"
        });

        console.log("wrong Pin -- Values entered are == " + transactionPin + " -- " + confirmTransactionPin);
        
    }
}

// This function updates Transaction Pin

function updateSecurityQA() {

    let securityQuestion = $('#securityQuestion').val();
    let securityAnswer = $('#securityAnswer').val();

    $("#buttonSaveQuestion").css({"display":"none"});
    $(".loading_spinners").css({"display":"block"});
    $.ajax
    ({
        type:'post',
        url:'security.php',
        data:{
            updateSecurity:"updateSecurity",
            securityQuestion:securityQuestion,
            securityAnswer:securityAnswer

        },
        success:function(data) {
            var jsonData = JSON.parse(data)

            if (jsonData.response == "success") {
                $(".loading_spinners").css({"display":"none"});
                $("#buttonSaveQuestion").css({"display":"block"});
                $('#securityQuestion').val('');
                $('#securityAnswer').val('');

                swal({
                    
                    type: jsonData.response,
                    title: jsonData.title,
                    text: jsonData.msg,
                    confirmButtonText: 'Continue',
                })
                // alert("Wrong Details" + response);
                console.log("Values entered are == " + securityQuestion + " -- " + securityAnswer + " -- " + response);
            }
            else {
                $(".loading_spinners").css({"display":"none"});
                $("#buttonSaveQuestion").css({"display":"block"});
                $('#securityQuestion').val('');
                $('#securityAnswer').val('');

                swal({
                    type: jsonData.response,
                    title: jsonData.title,
                    text: jsonData.msg,
                    confirmButtonText: 'Try Again'
                });
                // alert("Wrong Details" + response);
                console.log("Values entered are == " + securityQuestion + " -- " + securityAnswer + " -- " + response);

                //console.log(response + " - " + clientAccessID + " - " + phoneNo + " - " + wardId + " - " + password);
            }
        }
    });
    console.log("Values entered are == " + securityQuestion + " -- " + securityAnswer);
    
}




(function($) {
    var table = $('#example5').DataTable({
        searching: false,
        paging:true,
        select: false,
        //info: false,         
        lengthChange:false 
        
    });
    $('#example tbody').on('click', 'tr', function () {
        var data = table.row( this ).data();
        
    });
})(jQuery);
