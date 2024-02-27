<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f0f0;
    }
    .container {
        max-width: 400px;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
    }
    label {
        font-weight: bold;
    }
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        width: 100%;
        background-color: #4caf50;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #45a049;
    }
    .error{
        font-size: 10px !important;
        color:orangered;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <form id="forgotPasswordForm" action="{{route('auth.resetPassword')}}" method="post">
        @csrf
        {{-- <label for="email">Email:</label>
        <input type="email" id="email" name="email" readonly value="{{$user->email}}"> --}}

        <input type="hidden" name="id" value="{{$passwordReset->token}}">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" >
        <div id='passErr' class="error"></div>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <div id='confPassErr' class="error"></div>

        <input type="submit" value="Reset Password">
    </form>
</div>

{{-- jquery cdn --}}
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
</script>

{{-- jquery validation cdn --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js" integrity="sha512-WMEKGZ7L5LWgaPeJtw9MBM4i5w5OSBlSjTjCtSnvFJGSVD26gE5+Td12qN5pvWXhuWaWcVwF++F7aqu9cvqP0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
 $(document).ready(function() {

        $('#forgotPasswordForm').validate({
            rules: {
                password: {
                    required: true,
                    minlength: 8
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                }
            },
            messages: {
                password: {
                    required: 'Please enter your password',
                    minlength: 'Password must be at least 8 characters long'
                },
                confirm_password: {
                    required: 'Please confirm your password',
                    equalTo: 'Passwords do not match'
                }
            },
            errorPlacement: function(error, element) {
                // error.insertAfter(element);
                if (element.attr("name") == "password"){
                    error.appendTo($('#passErr'));
                }else if(element.attr("name") == "confirm_password" ){
                    error.appendTo($('#confPassErr'));
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
</body>
</html>
