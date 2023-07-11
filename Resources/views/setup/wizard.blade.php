<div>

    <div class="w-full max-w-full px-3 text-center flex-0">
        <h3 class="mt-12">Setup Wizard.</h3>
        <p class="font-normal dark:text-white text-slate-400">
            System not set yet. Please follow the steps below to set up the system.
        </p>
        <div multisteps-form class="mb-12">

            <div class="w-full max-w-full px-3 m-auto flex-0 lg:w-8/12">
                @include('base::setup.tabs')
            </div>

            <div class="flex flex-wrap">
                <div class="w-full max-w-full px-3 m-auto flex-0 lg:w-8/12">
                    <form id="setup-wizard" class="relative">
                        @csrf

                        @include('base::setup.admin-account')
                        @include('base::setup.migrate-db')

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    var log_wrapper = '';

    function loggingOutput(log_content, log_type = 'migration') {

        log_wrapper += 'DB Migration >$ ' + log_content + "\n";

        if (log_type === 'dataprocessor') {
            log_wrapper += 'Data Processor >$ ' + log_content + "\n";
        } else if (log_type === 'create_user') {
            log_wrapper += 'Create User >$ ' + log_content + "\n";
        }

        var element = document.getElementById('terminal_logger');
        element.textContent = log_wrapper;
        element.scrollTop = element.scrollHeight;
    }

    // Function to retrieve form input values
    function getFormValues() {

        var log_type = 'create_user';

        var form = document.getElementById('setup-wizard');

        var firstName = form.elements['first_name'].value;
        var lastName = form.elements['last_name'].value;
        var username = form.elements['username'].value;
        var password = form.elements['password'].value;
        var phone = form.elements['phone'].value;
        var email = form.elements['email'].value;


        // Validate the inputs
        if (firstName === '' || lastName === '' || username === '' || password === '' || phone === '' || email === '') {
            alert('Please fill in all the fields.');
            return;
        }

        const adminAccount = document.getElementById('admin-account');
        adminAccount.classList.remove('d-block');
        adminAccount.classList.add('d-none');

        const migrateDB = document.getElementById('migrate-db');
        migrateDB.classList.remove('d-none');

        // Create an object with the form data
        var formData = {
            first_name: firstName,
            last_name: lastName,
            username: username,
            password: password,
            phone: phone,
            email: email
        };

        // Convert the object to JSON
        var jsonData = JSON.stringify(formData);

        // Set up the fetch request
        fetch('{!! $url !!}/base/create-user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.elements['_token'].value
                },
                body: jsonData
            })
            .then(function(response) {
                if (response.ok) {
                    loggingOutput('Form data submitted successfully!', log_type);
                } else {
                    loggingOutput('Error occurred. Please try again.', log_type);
                }
            })
            .catch(function(error) {
                loggingOutput('Error occurred. Please try again.', log_type);
            });

        processList();

    }

    //function for processing posting to the server
    async function processList() {
        var log_type = 'migration';
        var form = document.getElementById('setup-wizard');

        var db_list = {!! json_encode($db_list) !!};
        var data_list = {!! json_encode($data_list) !!};

        // Set up the fetch request for automigrator
        await (async (db_list) => {

            for (const classname of db_list) {

                loggingOutput('Migrating ' + classname + '...', log_type);

                await fetch('{!! $url !!}/base/automigrator-migrate?class=' + classname, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.elements['_token'].value
                        },
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            loggingOutput('Automigrator migration failed.', log_type);
                        }

                        return response.json();

                    }).then(data => {
                        loggingOutput(data.message, log_type);
                        loggingOutput('', log_type);
                    });

            }
        })(db_list);

        loggingOutput('', log_type);
        loggingOutput('', log_type);

        log_type = 'dataprocessor';

        // Run the data processor
        await (async (data_list) => {

            for (const classname of data_list) {

                await fetch('{!! $url !!}/base/mybizna-dataprocessor?class=' + classname, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.elements['_token'].value
                        },
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            loggingOutput('Data processor migration failed.', log_type);
                        }

                        return response.json();
                    }).then(data => {
                        loggingOutput(data.message, log_type);
                        loggingOutput('', log_type);
                    });
            }

        })(data_list);

    }

    @if ($has_user)
        processList();
    @endif
</script>


<style>
    .terminal {
        border-radius: 5px 5px 0 0;
        position: relative;
    }

    .terminal .top {
        background: #E8E6E8;
        color: black;
        padding: 5px;
        border-radius: 5px 5px 0 0;
    }

    .terminal .btns {
        position: absolute;
        top: 7px;
        left: 5px;
    }

    .terminal .circle {
        width: 12px;
        height: 12px;
        display: inline-block;
        border-radius: 15px;
        margin-left: 2px;
        border-width: 1px;
        border-style: solid;
    }

    .title {
        text-align: center;
    }

    .red {
        background: #EC6A5F;
        border-color: #D04E42;
    }

    .green {
        background: #64CC57;
        border-color: #4EA73B;
    }

    .yellow {
        background: #F5C04F;
        border-color: #D6A13D;
    }

    .clear {
        clear: both;
    }

    .terminal .body {
        background: black;
        color: #7AFB4C;
        overflow: auto;
    }

    .space {
        margin: 25px;
    }

    .shadow {
        box-shadow: 0px 0px 10px rgba(0, 0, 0, .4)
    }

    .line4 {
        background: black;
        color: #7AFB4C;
    }

    .cursor4 {
        -webkit-animation: blink 1s 11.5s infinite !important;
        -moz-animation: blink 1s 8.5s infinite !important;
        -o-animation: blink 1s 8.5s infinite !important;
        animation: blink 1s 8.5s infinite !important;
    }

    @-webkit-keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @-moz-keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @-o-keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @-webkit-keyframes type {
        to {
            width: 17em;
        }
    }

    @-moz-keyframes type {
        to {
            width: 17em;
        }
    }

    @-o-keyframes type {
        to {
            width: 17em;
        }
    }

    @keyframes type {
        to {
            width: 17em;
        }
    }
</style>
